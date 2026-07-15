<?php

namespace App\Http\Controllers;

use App\Events\NewNotificationReceived;
use App\Models\BlockedContact;
use App\Models\Message;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Http\Controllers\Traits\MessagingPrivacyTrait;
use App\Services\UserPresenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class MessagingController extends Controller
{
    use MessagingPrivacyTrait;

    private const CONTACTS_LIMIT      = 200;
    private const MESSAGES_PER_PAGE   = 25;
    private const STREAM_POLL_LIMIT   = 120;
    private const SEARCH_LIMIT        = 50;
    private const SEARCH_USERS_LIMIT  = 30;
    private const FREQUENT_CONTACTS   = 5;

    public function __construct(private readonly UserPresenceService $presence) {}

    public function createGroup(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['integer', 'exists:users,id'],
            'avatar' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        $participantIds = collect($data['participant_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== (int) $user->id)
            ->unique()
            ->values();

        if ($participantIds->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'اختر مشاركًا واحدًا على الأقل'], 422);
        }

        $participants = User::whereIn('id', $participantIds)->get();
        foreach ($participants as $participant) {
            if (!$this->canMessage($user, $participant)) {
                return response()->json(['success' => false, 'message' => 'لا يمكنك إضافة هذا المستخدم إلى القروب'], 403);
            }
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('messaging/groups', 'public');
        }

        DB::beginTransaction();
        try {
            $groupId = DB::table('groups')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'avatar_path' => $avatarPath,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $members = $participantIds->push($user->id)->unique()->map(fn ($id) => [
                'group_id' => $groupId,
                'user_id' => (int) $id,
                'role' => ((int) $id === (int) $user->id) ? 'admin' : 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ])->values()->all();

            DB::table('group_participants')->insert($members);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء القروب بنجاح',
                'data' => [
                    'id' => $groupId,
                    'name' => $data['name'],
                    'avatarUrl' => $avatarPath ? $this->getSecureAttachmentUrl($avatarPath) : null,
                    'participantsCount' => count($members),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Group creation failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'تعذر إنشاء القروب'], 500);
        }
    }

    /**
     * Sanitize a free-text message payload before persistence.
     * Defense-in-depth: the Vue frontend already escapes output, but we strip
     * control characters and neutralize any embedded HTML so stored content can
     * never be interpreted as markup by any future/legacy renderer.
     */
    protected function sanitizeContent(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        // Remove non-printable control chars (keep tab/newline) to block payload smuggling.
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $content);
        $clean = $clean ?? '';

        // Neutralize HTML/JS markup. Quotes are kept readable rather than encoded.
        $clean = strip_tags($clean);
        $clean = htmlspecialchars($clean, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', false);

        $clean = trim($clean);

        return $clean === '' ? null : $clean;
    }

    /**
     * يُستخدم أيضاً من CallController للسماح/منع المكالمات بنفس قواعد الحظر والخصوصية.
     */
    public function userCanMessage(User $sender, User $recipient): bool
    {
        return $this->canMessage($sender, $recipient);
    }

    protected function canMessage(User $sender, User $recipient): bool
    {
        if (!$this->passesBaseMessagingRules($sender, $recipient)) {
            return false;
        }

        $privacy = $this->getUserPrivacySettings($recipient);
        $rule = $privacy['messageFrom'] ?? 'all';

        if ($rule === 'nobody') {
            return false;
        }

        if ($rule === 'contacts') {
            return $this->isWithinPrivacyContactsScope($sender, $recipient);
        }

        return true;
    }

    protected function passesBaseMessagingRules(User $sender, User $recipient): bool
    {
        if ($sender->id === $recipient->id) {
            return false;
        }

        if (BlockedContact::where('blocker_id', $recipient->id)->where('blocked_id', $sender->id)->exists()) {
            return false;
        }

        if (BlockedContact::where('blocker_id', $sender->id)->where('blocked_id', $recipient->id)->exists()) {
            return false;
        }

        if ($sender->role === 'teacher') {
            return $recipient->role === 'student';
        }

        if ($sender->role === 'student') {
            return in_array($recipient->role, ['teacher', 'student'], true);
        }

        return false;
    }

    protected function getUserPrivacySettings(User $user): array
    {
        $settings = DB::table('user_messaging_settings')->where('user_id', $user->id)->first();
        return $this->settingsPayload($settings)['privacy'] ?? [];
    }

    protected function isWithinPrivacyContactsScope(User $viewer, User $owner): bool
    {
        return $this->passesBaseMessagingRules($viewer, $owner);
    }

    protected function viewerMatchesVisibilityRule(User $viewer, User $owner, string $rule): bool
    {
        if ($viewer->id === $owner->id) {
            return true;
        }

        return match ($rule) {
            'nobody' => false,
            'contacts' => $this->isWithinPrivacyContactsScope($viewer, $owner),
            default => true,
        };
    }

    protected function canViewerCall(User $viewer, User $owner): bool
    {
        $privacy = $this->getUserPrivacySettings($owner);
        $rule = $privacy['callFrom'] ?? 'all';
        return $this->viewerMatchesVisibilityRule($viewer, $owner, $rule);
    }

    protected function buildGroupContactsPayload(User $user): array
    {
        $groups = DB::table('groups as g')
            ->join('group_participants as gp', function ($j) use ($user) {
                $j->on('gp.group_id', '=', 'g.id')->where('gp.user_id', '=', $user->id);
            })
            ->leftJoin('group_message_reads as gmr', function ($j) use ($user) {
                $j->on('gmr.group_id', '=', 'g.id')->where('gmr.user_id', '=', $user->id);
            })
            ->select('g.id', 'g.name', 'g.avatar_path', 'g.description', 'g.created_by', 'gmr.last_read_message_id', 'gp.role as my_role')
            ->get();

        // Batch member counts
        $groupIds = $groups->pluck('id');
        $memberCounts = DB::table('group_participants')
            ->whereIn('group_id', $groupIds)
            ->groupBy('group_id')
            ->select('group_id', DB::raw('COUNT(*) as cnt'))
            ->pluck('cnt', 'group_id');

        $result = [];
        foreach ($groups as $g) {
            $lastMsg = DB::table('group_messages as gm')
                ->join('users as u', 'u.id', '=', 'gm.sender_id')
                ->where('gm.group_id', $g->id)
                ->whereNull('gm.deleted_at')
                ->orderByDesc('gm.created_at')
                ->select('gm.id', 'gm.content', 'gm.created_at', 'u.name as sender_name')
                ->first();

            $unread = DB::table('group_messages')
                ->where('group_id', $g->id)
                ->where('id', '>', $g->last_read_message_id ?? 0)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('deleted_at')
                ->count();

            $result[] = [
                'id'              => 'group_' . $g->id,
                '_groupId'        => $g->id,
                'name'            => $g->name,
                'description'     => $g->description ?? null,
                'avatar_url'      => $g->avatar_path ? asset('storage/' . $g->avatar_path) : null,
                'lastMessage'     => $lastMsg ? (Str::limit(($lastMsg->sender_name ?? '') . ': ' . ($lastMsg->content ?? ''), 60)) : '',
                'lastMessageTime' => $lastMsg ? \Carbon\Carbon::parse($lastMsg->created_at)->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP') : null,
                'unreadCount'     => $unread,
                'isGroup'         => true,
                'hasConversation' => true,
                'isOnline'        => false,
                'lastSeenAt'      => null,
                'selected'        => false,
                '_membersCount'   => (int) ($memberCounts[$g->id] ?? 0),
                '_isAdmin'        => in_array($g->my_role, ['admin', 'owner']),
                '_createdBy'      => $g->created_by,
            ];
        }
        return $result;
    }

    protected function publicContactPayload(User $viewer, User $contact, array $extra = []): array
    {
        $privacy = $this->getUserPrivacySettings($contact);

        $showPresence = !$privacy['hideOnlineStatus']
            && $this->viewerMatchesVisibilityRule($viewer, $contact, $privacy['lastSeenFor'] ?? 'all');
        $showAvatar = $this->viewerMatchesVisibilityRule($viewer, $contact, $privacy['profilePhotoFor'] ?? 'all');
        $showPhone = $this->viewerMatchesVisibilityRule($viewer, $contact, $privacy['phoneVisibleFor'] ?? 'contacts');

        $isOnline = $showPresence ? $this->presence->isOnline($contact) : false;
        $lastSeenAt = $showPresence ? optional($this->presence->getLastActivityTimestamp($contact))?->toISOString() : null;
        $lastSeen = $showPresence
            ? ($isOnline ? 'نشط الآن' : $this->formatActivityTime($this->presence->getLastActivityTimestamp($contact)))
            : 'غير متاح';

        return array_merge([
            'id' => $contact->id,
            'name' => $contact->name,
            'username' => $contact->username ?? null,
            'avatar_url' => $showAvatar ? $contact->avatar_url : null,
            'phone' => $showPhone ? $contact->phone : null,
            'isOnline' => $isOnline,
            'lastSeen' => $lastSeen,
            'lastSeenAt' => $lastSeenAt,
            'canCall' => $this->canViewerCall($viewer, $contact),
        ], $extra);
    }

    private function enrichContact(User $contact, ?Message $message, int $unreadCount, string $contactType = 'student'): void
    {
        $contact->last_message = $this->getMessagePreviewText($message);
        $contact->last_message_time = $message?->created_at;
        $contact->last_message_status_ref_id = $this->extractStatusRefId($message);
        $contact->has_conversation = !is_null($contact->last_message_time);
        $contact->unread_count = $unreadCount;
        $contact->is_online = $this->presence->isOnline($contact);
        $contact->last_activity_formatted = $this->formatActivityTime($this->presence->getLastActivityTimestamp($contact));
        $contact->last_seen = $contact->is_online ? 'نشط الآن' : $contact->last_activity_formatted;
        $contact->contact_type = $contactType;
    }

    private function buildInitialMessagesJson($messages): array
    {
        return $messages->map(fn ($message) => [
            'id'               => $message->id,
            'senderId'         => $message->sender_id,
            'recipientId'      => $message->recipient_id,
            'content'          => $message->content,
            'attachmentUrl'    => $this->getSecureAttachmentUrl($message->attachment_path),
            'attachmentName'   => $message->attachment_name,
            'attachmentMime'   => $message->attachment_type,
            'attachmentKind'   => $message->attachment_kind,
            'isEdited'         => $message->is_edited,
            'createdAt'        => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'status'           => $message->sender_id === Auth::id() ? 'sent' : 'received',
            'readAt'           => $message->read_at,
            'senderName'       => $message->sender?->name,
            'audioPosition'    => (float) ($message->audio_position ?? 0),
            'isSensitive'      => (bool) $message->is_sensitive,
        ])->values()->all();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'teacher') {
            return $this->teacherIndex($request, $user);
        }
        if ($user->role === 'student') {
            return $this->studentIndex($request, $user);
        }
        abort(403, 'غير مصرح لك بالوصول');
    }

    protected function teacherIndex(Request $request, User $user)
    {
        $contacts = User::where('role', 'student')
            ->orderBy('name')
            ->limit(self::CONTACTS_LIMIT)
            ->get();

        $studentIds = $contacts->pluck('id');

        $latestMessageSub = DB::table('messages')
            ->select(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END as contact_id'), DB::raw('MAX(id) as last_message_id'))
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->whereIn(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END'), $studentIds)
            ->groupBy(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END'))
            ->pluck('last_message_id', 'contact_id');

        $latestMessages = collect();
        if ($latestMessageSub->isNotEmpty()) {
            $latestMessages = Message::with(['sender', 'recipient'])
                ->whereIn('id', $latestMessageSub->values())
                ->get()
                ->keyBy(fn ($m) => $m->sender_id === $user->id ? $m->recipient_id : $m->sender_id);
        }

        $unreadCounts = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->whereIn('sender_id', $studentIds)
            ->groupBy('sender_id')
            ->select(DB::raw('sender_id, COUNT(*) as cnt'))
            ->pluck('cnt', 'sender_id');

        $contacts = $contacts->map(function (User $student) use ($latestMessages, $unreadCounts) {
                $this->enrichContact($student, $latestMessages->get($student->id), (int) ($unreadCounts->get($student->id) ?? 0));
                return $student;
            })->values();

        $newContacts = $contacts
            ->filter(fn ($student) => is_null($student->last_message_time))
            ->sortBy('name')
            ->values();

        // Keep all contacts visible, but prioritize those with conversations.
        $contacts = $contacts
            ->sortByDesc(function ($student) {
                if (!is_null($student->last_message_time)) {
                    return strtotime((string) $student->last_message_time);
                }
                return 0;
            })
            ->values();

        $selectedContact = null;
        $contactQuery = $request->query('contact') ?? $request->query('student');
        if ($contactQuery) {
            $selectedContact = $contacts->firstWhere('id', (int) $contactQuery);
        }
        $selectedContact = $selectedContact ?? $contacts->first();

        $messages = collect();
        if ($selectedContact) {
            $messages = Message::between($user, $selectedContact)
                ->with(['sender', 'recipient', 'replyTo.sender'])
                ->orderBy('created_at')
                ->get();
            $readAt = now();
            Message::where('sender_id', $selectedContact->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => $readAt, 'updated_at' => $readAt]);
        }

        $totalUnread = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $contactsJson = $contacts->map(fn ($contact) => $this->publicContactPayload($user, $contact, [
            'lastMessage' => $contact->last_message,
            'lastMessageTime' => $contact->last_message_time?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'lastMessageStatusRefId' => $contact->last_message_status_ref_id,
            'unreadCount' => $contact->unread_count,
            'selected' => isset($selectedContact) && $selectedContact->id === $contact->id,
            'hasConversation' => (bool) ($contact->has_conversation ?? !is_null($contact->last_message_time)),
            'role' => $contact->contact_type ?? $contact->role ?? 'student',
        ]))->values()->all();

        // Merge groups into contacts list
        $groupContactsJson = $this->buildGroupContactsPayload($user);
        $contactsJson = array_merge($contactsJson, $groupContactsJson);

        $newContactsJson = $newContacts->map(fn ($contact) => $this->publicContactPayload($user, $contact, [
            'role' => $contact->role ?? 'student',
        ]))->values()->all();

        $initialMessagesJson = $this->buildInitialMessagesJson($messages);

        return view('teacher.messaging', [
            'userRole' => 'teacher',
            'contacts' => $contacts,
            'selectedContact' => $selectedContact,
            'messages' => $messages,
            'conversationTitle' => $selectedContact?->name ?? 'المحادثات',
            'conversationStatus' => $selectedContact ? ($selectedContact->is_online ? 'نشط الآن' : $selectedContact->last_seen) : 'اختر جهة اتصال',
            'currentRoute' => route('teacher.messaging'),
            'sendRoute' => route('teacher.messaging.send'),
            'refreshRoute' => route('teacher.messaging.refresh'),
            'contactQueryKey' => 'contact',
            'totalUnread' => $totalUnread,
            'contactsJson' => $contactsJson,
            'newContactsJson' => $newContactsJson,
            'initialMessagesJson' => $initialMessagesJson,
        ]);
    }

    protected function studentIndex(Request $request, User $user)
    {
        $contacts = collect();

        // Add teacher
        $teacher = User::where('role', 'teacher')
            ->when($user->teacher_id, function ($query) use ($user) {
                $query->where('id', $user->teacher_id);
            })
            ->first();

        if ($teacher) {
            $lastMessage   = Message::between($user, $teacher)->latest()->first();
            $teacherUnread = Message::where('sender_id', $teacher->id)->where('recipient_id', $user->id)->whereNull('read_at')->count();
            $this->enrichContact($teacher, $lastMessage, $teacherUnread, 'teacher');
            $contacts->push($teacher);
        }

        // Add classmates (other students)
        $classmates = User::where('role', 'student')
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->limit(self::CONTACTS_LIMIT)
            ->get();

        if ($classmates->isNotEmpty()) {
            $classmateIds = $classmates->pluck('id');

            $latestMessageSub = DB::table('messages')
                ->select(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END as contact_id'), DB::raw('MAX(id) as last_message_id'))
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
                })
                ->whereIn(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END'), $classmateIds)
                ->groupBy(DB::raw('CASE WHEN sender_id = ' . $user->id . ' THEN recipient_id ELSE sender_id END'))
                ->pluck('last_message_id', 'contact_id');

            $latestMessages = collect();
            if ($latestMessageSub->isNotEmpty()) {
                $latestMessages = Message::with(['sender', 'recipient'])
                    ->whereIn('id', $latestMessageSub->values())
                    ->get()
                    ->keyBy(fn ($m) => $m->sender_id === $user->id ? $m->recipient_id : $m->sender_id);
            }

            $unreadCounts = Message::where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->whereIn('sender_id', $classmateIds)
                ->groupBy('sender_id')
                ->select(DB::raw('sender_id, COUNT(*) as cnt'))
                ->pluck('cnt', 'sender_id');

            foreach ($classmates as $classmate) {
                $this->enrichContact($classmate, $latestMessages->get($classmate->id), (int) ($unreadCounts->get($classmate->id) ?? 0));
                $contacts->push($classmate);
            }
        }

        $newContacts = $contacts
            ->filter(fn ($contact) => is_null($contact->last_message_time))
            ->sortBy('name')
            ->values();

        // Keep all contacts visible, but prioritize those with conversations.
        $contacts = $contacts
            ->sortByDesc(function ($contact) {
                if (!is_null($contact->last_message_time)) {
                    return strtotime((string) $contact->last_message_time);
                }
                return 0;
            })
            ->values();

        $selectedContact = $contacts->first();

        $messages = collect();
        if ($selectedContact) {
            $messages = Message::between($user, $selectedContact)
                ->with(['sender', 'recipient', 'replyTo.sender'])
                ->orderBy('created_at')
                ->get();
            $readAt = now();
            Message::where('sender_id', $selectedContact->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => $readAt, 'updated_at' => $readAt]);
        }

        $totalUnread = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $contactsJson = $contacts->map(fn ($contact) => $this->publicContactPayload($user, $contact, [
            'lastMessage' => $contact->last_message,
            'lastMessageTime' => $contact->last_message_time?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'lastMessageStatusRefId' => $contact->last_message_status_ref_id,
            'unreadCount' => $contact->unread_count,
            'selected' => isset($selectedContact) && $selectedContact->id === $contact->id,
            'hasConversation' => (bool) ($contact->has_conversation ?? !is_null($contact->last_message_time)),
            'role' => $contact->contact_type ?? $contact->role ?? 'student',
        ]))->values()->all();

        $groupContactsJson = $this->buildGroupContactsPayload($user);
        $contactsJson = array_merge($contactsJson, $groupContactsJson);

        $newContactsJson = $newContacts->map(fn ($contact) => $this->publicContactPayload($user, $contact, [
            'role' => $contact->role ?? 'student',
        ]))->values()->all();

        $initialMessagesJson = $this->buildInitialMessagesJson($messages);

        return view('student.messaging', [
            'userRole' => 'student',
            'contacts' => $contacts,
            'selectedContact' => $selectedContact,
            'messages' => $messages,
            'conversationTitle' => $selectedContact?->name ?? 'المحادثات',
            'conversationStatus' => $selectedContact ? ($selectedContact->is_online ? 'نشط الآن' : $selectedContact->last_seen) : 'اختر جهة اتصال',
            'currentRoute' => route('student.messaging'),
            'sendRoute' => route('student.messaging.send'),
            'refreshRoute' => route('student.messaging.refresh'),
            'contactQueryKey' => 'contact',
            'totalUnread' => $totalUnread,
            'contactsJson' => $contactsJson,
            'newContactsJson' => $newContactsJson,
            'initialMessagesJson' => $initialMessagesJson,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['nullable', 'string', 'max:8000'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,pptx,txt,mp4,mov,avi,mkv,webm,mp3,wav,zip,rar,7z', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,pptx,txt,mp4,mov,avi,mkv,webm,mp3,wav,zip,rar,7z'],
            'reply_to' => ['nullable', 'integer', 'exists:messages,id'],
            'is_sensitive' => ['nullable', 'boolean'],
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($data['recipient_id']);

        // Sanitize free-text before any further processing.
        $data['content'] = $this->sanitizeContent($data['content'] ?? null);

        if ($sender->id === $recipient->id) {
            return response()->json(['success' => false, 'message' => 'لا يمكنك إرسال رسالة لنفسك.'], 422);
        }

        if (!$this->canMessage($sender, $recipient)) {
            abort(403, 'لا يمكنك إرسال رسالة إلى هذا المستخدم.');
        }

        if (empty($data['content']) && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'يجب إدخال محتوى الرسالة أو إرفاق ملف.'], 422);
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        try {
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('message_attachments', 'public');
                $attachmentType = Storage::disk('public')->mimeType($attachmentPath) ?: $file->getMimeType();
                $attachmentName = $file->getClientOriginalName();
            }

            $message = Message::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'content' => $data['content'] ?? null,
                'attachment_path' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'attachment_name' => $attachmentName,
                'reply_to' => $data['reply_to'] ?? null,
                'is_sensitive' => $attachmentPath ? (bool) ($data['is_sensitive'] ?? false) : false,
            ]);

            if ($recipient->notify_message ?? true) {
                $recipient->notify(new AppNotification(
                    'رسالة جديدة',
                    "لديك رسالة جديدة من {$sender->name}.",
                    $recipient->role === 'teacher' ? route('teacher.messaging') : route('student.messaging'),
                    'message',
                    'ri-mail-open-line'
                ));
                try {
                    event(new NewNotificationReceived($recipient->id, 'message'));
                } catch (\Throwable) {}
            }
        } catch (\Exception $e) {
            \Log::error('Message send failed', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.',
            ], 500, ['X-Error' => config('app.debug') ? $e->getMessage() : null]);
        }

        $replyToData = null;
        if ($message->reply_to) {
            $message->load('replyTo.sender');
            if ($message->replyTo) {
                $replyToData = [
                    'id' => $message->replyTo->id,
                    'content' => $message->replyTo->content,
                    'senderName' => $message->replyTo->sender?->name,
                    'attachmentName' => $message->replyTo->attachment_name,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الرسالة بنجاح',
                'data' => [
                    'id' => $message->id,
                    'sender_name' => $sender->name,
                    'sender_initial' => mb_substr($sender->name, 0, 1),
                    'content' => $message->content,
                    'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
                    'attachmentName' => $message->attachment_name,
                    'attachmentMime' => $message->attachment_type,
                    'attachmentKind' => $message->attachment_kind,
                    'isSensitive' => $message->is_sensitive,
                    'isEdited' => $message->is_edited,
                    'createdAt' => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    'readAt' => $message->read_at,
                    'audioPosition' => 0,
                    'replyTo' => $replyToData,
                ],
        ]);
    }

    public function refresh(Request $request)
    {
        $user = Auth::user();
        $recipientId = (int) $request->query('recipient_id');
        $recipient = User::findOrFail($recipientId);

        if (!$this->canMessage($user, $recipient)) {
            abort(403, 'لا يمكنك التواصل مع هذا المستخدم.');
        }

        $messages = Message::between($user, $recipient)
            ->with(['sender', 'recipient', 'replyTo.sender'])
            ->orderBy('created_at')
            ->limit(self::STREAM_POLL_LIMIT)
            ->get();

        $readAt = now();
        Message::where('sender_id', $recipient->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => $readAt, 'updated_at' => $readAt]);

        $totalUnread = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'senderId' => $message->sender_id,
                        'sender_name' => $message->sender->name,
                        'sender_initial' => mb_substr($message->sender->name, 0, 1),
                        'content' => $message->content,
                        'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
                        'attachmentName' => $message->attachment_name,
                        'attachmentMime' => $message->attachment_type,
                        'attachmentKind' => $message->attachment_kind,
                        'isEdited' => $message->is_edited,
                        'createdAt' => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                        'readAt' => $message->read_at,
                        'audioPosition' => (float) ($message->audio_position ?? 0),
                        'isSensitive' => (bool) $message->is_sensitive,
                    ];
                }),
                'total_unread' => $totalUnread,
            ],
        ]);
    }

    protected function getMessagePreviewText(?Message $message): ?string
    {
        if (!$message) {
            return null;
        }
        if ($message->content && trim($message->content) !== '') {
            return $message->content;
        }
        return $this->getAttachmentPreviewLabel($message->attachment_type, $message->attachment_name);
    }

    protected function extractStatusRefId(?Message $message): ?int
    {
        if (!$message || !$message->content) return null;
        if (preg_match('/\[\[status:(\d+)\]\]/i', $message->content, $m)) {
            return (int) $m[1];
        }
        return null;
    }

    protected function getAttachmentPreviewLabel(?string $mime, ?string $name): string
    {
        if ($mime && str_starts_with($mime, 'image/')) {
            return 'صورة مرفقة';
        }
        if ($mime && str_starts_with($mime, 'video/')) {
            return 'فيديو مرفق';
        }
        if ($mime && str_starts_with($mime, 'audio/')) {
            return 'رسالة صوتية';
        }
        if ($mime === 'application/pdf' || str_contains($name ?? '', '.pdf')) {
            return 'ملف PDF';
        }
        if ($mime === 'application/msword' || str_contains($name ?? '', '.doc') || str_contains($name ?? '', '.docx')) {
            return 'ملف Word';
        }
        if ($mime === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || str_contains($name ?? '', '.xls') || str_contains($name ?? '', '.xlsx')) {
            return 'ملف Excel';
        }
        if ($mime === 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || str_contains($name ?? '', '.ppt') || str_contains($name ?? '', '.pptx')) {
            return 'ملف عرض تقديمي';
        }
        return 'ملف مرفق';
    }





    protected function formatActivityTime(?Carbon $timestamp): string
    {
        if (!$timestamp) {
            return 'غير متاح';
        }
        $activityTime = $timestamp->copy()->setTimezone('Asia/Riyadh');
        $now = now('Asia/Riyadh');
        $diffInSeconds = max(0, $now->timestamp - $activityTime->timestamp);
        if ($diffInSeconds < 60) {
            return 'الآن';
        }
        if ($diffInSeconds < 3600) {
            return 'منذ ' . floor($diffInSeconds / 60) . ' دقيقة';
        }
        if ($diffInSeconds < 86400) {
            return 'منذ ' . floor($diffInSeconds / 3600) . ' ساعة';
        }
        if ($activityTime->isYesterday()) {
            return 'أمس ' . $activityTime->format('h:i A');
        }
        if ($diffInSeconds < 604800) {
            return 'منذ ' . floor($diffInSeconds / 86400) . ' يوم';
        }
        return $activityTime->format('Y-m-d h:i A');
    }
    protected function touchStickerRecent(User $user, int $stickerId): void
    {
        $setting = \App\Models\UserMessagingSetting::firstOrCreate(['user_id' => $user->id]);
        $media = $setting->media ?? [];
        $recent = array_values(array_filter($media['sticker_recent'] ?? [], fn ($id) => $id !== $stickerId));
        array_unshift($recent, $stickerId);
        $media['sticker_recent'] = array_slice($recent, 0, 12);
        $setting->media = $media;
        $setting->save();
    }

    protected function getSecureAttachmentUrl(?string $attachmentPath): ?string
    {
        if (!$attachmentPath) {
            return null;
        }

        if (preg_match('#^https?://#i', trim($attachmentPath))) {
            return trim($attachmentPath);
        }

        $path = str_replace('\\', '/', trim($attachmentPath));
        $path = ltrim($path, '/');
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        if (!$path) {
            return null;
        }

        // Normalize legacy paths
        if (str_starts_with($path, 'messaging/audio/')) {
            $path = 'message_audio/' . basename($path);
        } elseif (str_starts_with($path, 'messaging/attachments/')) {
            $path = 'message_attachments/' . basename($path);
        }

        // Public disk: serve directly via asset URL (fast - no streaming overhead)
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Legacy: filename only (no directory) - search known folders
        if (!str_contains($path, '/')) {
            foreach (['message_audio', 'message_attachments', 'statuses/media', 'statuses/audio'] as $dir) {
                $candidate = $dir . '/' . $path;
                if (Storage::disk('public')->exists($candidate)) {
                    return asset('storage/' . $candidate);
                }
            }
        }

        // Private disk fallback (streaming required)
        if (Storage::disk('local')->exists('messaging/attachments/' . basename($path))) {
            return route('storage.message-attachment', ['path' => 'message_attachments/' . basename($path)]);
        }
        if (Storage::disk('local')->exists('messaging/audio/' . basename($path))) {
            return route('storage.message-audio', ['path' => 'message_audio/' . basename($path)]);
        }

        return asset('storage/' . $path);
    }

    public function searchUsers(Request $request)
    {
        $query = mb_substr(trim((string) $request->query('q', '')), 0, 50);
        $excludeIds = $request->query('exclude', []);

        $users = User::where('id', '!=', Auth::id())
            ->whereNotIn('id', is_array($excludeIds) ? $excludeIds : [])
            ->where(function ($q) use ($query) {
                if ($query !== '') {
                    $q->whereRaw('MATCH(name, email) AGAINST(? IN BOOLEAN MODE)', ['+' . $query . '*'])
                      ->orWhere('name', 'like', "{$query}%");
                }
            })
            ->orderBy('name')
            ->limit(self::SEARCH_USERS_LIMIT)
            ->get(['id', 'name', 'email', 'avatar_url', 'role']);

        $viewer = Auth::user();

        $result = $users->map(function (User $user) use ($viewer) {
            $isOnline  = $this->presence->isOnline($user);
            $lastTs    = $this->presence->getLastActivityTimestamp($user);
            $privacy   = $this->getUserPrivacySettings($user);
            $showPresence = $this->viewerMatchesVisibilityRule($viewer, $user, $privacy['lastSeenFor'] ?? 'all');
            $showAvatar   = $this->viewerMatchesVisibilityRule($viewer, $user, $privacy['profilePhotoFor'] ?? 'all');

            return [
                'id'              => $user->id,
                'name'            => $user->name,
                'role'            => $user->role,
                'avatar_url'      => $showAvatar ? $user->avatar_url : null,
                'isOnline'        => $showPresence ? $isOnline : false,
                'lastSeenAt'      => ($showPresence && $lastTs) ? $lastTs->toISOString() : null,
                'lastSeenLabel'   => $showPresence
                    ? ($isOnline ? 'نشط الآن' : $this->formatActivityTime($lastTs))
                    : null,
            ];
        });

        return response()->json($result);
    }

    public function loadMessages(Request $request)
    {
        $user = Auth::user();
        $recipientId = (int) $request->query('recipient_id');
        $page = (int) $request->query('page', 1);
        $perPage = self::MESSAGES_PER_PAGE;

        $recipient = User::findOrFail($recipientId);

        // Authorization check
        if (!$this->canMessage($user, $recipient)) {
            abort(403, 'لا يمكنك التواصل مع هذا المستخدم.');
        }

        $messages = Message::between($user, $recipient)
            ->with(['sender', 'recipient', 'replyTo.sender'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $messageIds = $messages->getCollection()->pluck('id');
        $reactionsByMessage = $this->reactionsForMessages($messageIds, (int) $user->id);
        $pinnedByMessage = $this->pinnedForMessages($messageIds);

        // Normalize payload for frontend (ensures media/avatars/attachments always available)
        $messages->getCollection()->transform(function ($message) use ($reactionsByMessage, $pinnedByMessage, $user) {
            $reply = null;
            if ($message->reply_to) {
                $repliedMessage = $message->replyTo;
                if ($repliedMessage) {
                    $reply = [
                        'id' => $repliedMessage->id,
                        'content' => $repliedMessage->content,
                        'senderName' => $repliedMessage->sender?->name,
                        'attachmentName' => $repliedMessage->attachment_name,
                    ];
                }
            }

            return [
                'id' => $message->id,
                'senderId' => $message->sender_id,
                'recipientId' => $message->recipient_id,
                'senderName' => $message->sender?->name,
                'senderAvatar' => $message->sender?->avatar_url ? asset('storage/' . ltrim($message->sender->avatar_url, '/')) : null,
                'content' => $message->content,
                'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
                'attachmentName' => $message->attachment_name,
                'attachmentMime' => $message->attachment_type,
                'attachmentKind' => $message->attachment_kind,
                'isEdited' => (bool) $message->is_edited,
                'createdAt' => $message->created_at?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                'readAt' => $message->read_at,
                'replyTo' => $reply,
                'reactions' => $reactionsByMessage[$message->id] ?? [],
                'isPinned' => (bool) ($pinnedByMessage[$message->id] ?? false),
                'forwardedFromMessageId' => $message->forwarded_from_message_id,
                'audioPosition' => (float) ($message->audio_position ?? 0),
                'isSensitive' => (bool) $message->is_sensitive,
            ];
        });

        $contactPayload = $this->publicContactPayload($user, $recipient, [
            'isTyping' => Cache::has('messaging-typing-' . $user->id . '-' . $recipient->id),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'messages' => $messages->items(),
                'contact' => $contactPayload,
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'total' => $messages->total(),
                ],
            ],
        ]);
    }

    public function uploadAudio(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'audio' => ['required', 'file', 'max:5120', 'mimes:wav,mp3,ogg,webm,m4a,aac', 'extensions:wav,mp3,ogg,webm,m4a,aac'],
            'duration' => ['required', 'numeric', 'min:0.5'],
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($data['recipient_id']);

        if (!$this->canMessage($sender, $recipient)) {
            abort(403, 'غير مصرح لهذا التراسل.');
        }

        try {
            $audioFile = $request->file('audio');
            $audioPath = $audioFile->store('message_audio', 'public');

            $audioMime = Storage::disk('public')->mimeType($audioPath) ?: $audioFile->getMimeType();
            $message = Message::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'content' => "رسالة صوتية ({$data['duration']} ثانية)",
                'attachment_path' => $audioPath,
                'attachment_type' => $audioMime ?: 'audio/webm',
                'attachment_name' => $audioFile->getClientOriginalName() ?: 'audio_message.webm',
            ]);

            // Notify recipient
            if ($recipient->notify_message ?? true) {
                $recipient->notify(new AppNotification(
                    'رسالة صوتية جديدة',
                    "لديك رسالة صوتية جديدة من {$sender->name}.",
                    $recipient->role === 'teacher' ? route('teacher.messaging') : route('student.messaging'),
                    'message',
                    'ri-mic-line'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة الصوتية بنجاح',
                'data' => [
                    'id' => $message->id,
                    'attachmentUrl' => $this->getSecureAttachmentUrl($audioPath),
                    'attachmentName' => $message->attachment_name,
                    'attachmentMime' => $message->attachment_type,
                    'attachmentKind' => $message->attachment_kind,
                    'duration' => $data['duration'],
                    'content' => $message->content,
                    'isEdited' => $message->is_edited,
                    'createdAt' => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    'audioPosition' => 0,
                    'isSensitive' => (bool) ($message->is_sensitive ?? false),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Audio message upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة الصوتية',
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,mp4,mov,avi,mkv,webm,mp3,wav,m4a,ogg,zip,rar,7z', 'extensions:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,mp4,mov,avi,mkv,webm,mp3,wav,m4a,ogg,zip,rar,7z'],
            'content' => ['nullable', 'string', 'max:2000'],
            'reply_to' => ['nullable', 'integer', 'exists:messages,id'],
            'is_sensitive' => ['nullable', 'boolean'],
            'kind' => ['nullable', 'string', 'in:sticker_static,sticker_animated,gif'],
            'gif_url' => ['nullable', 'string', 'url', 'max:1000'],
            'sticker_id' => ['nullable', 'integer'],
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($data['recipient_id']);

        if (!$this->canMessage($sender, $recipient)) {
            abort(403, 'غير مصرح لهذا التراسل.');
        }

        $kind = $data['kind'] ?? null;
        $sticker = null;

        if ($kind === 'gif') {
            if (empty($data['gif_url']) || !preg_match('#^https://#i', $data['gif_url'])) {
                return response()->json(['success' => false, 'message' => 'رابط GIF غير صالح.'], 422);
            }
            $path = $data['gif_url'];
            $mime = 'image/gif';
            $name = 'gif.gif';
        } elseif ($kind === 'sticker_static' || $kind === 'sticker_animated') {
            $sticker = \App\Models\Sticker::where('id', $data['sticker_id'] ?? 0)->where('user_id', $sender->id)->first();
            if (!$sticker) {
                return response()->json(['success' => false, 'message' => 'الملصق غير موجود.'], 404);
            }
            $path = $sticker->file_path;
            $mime = $sticker->type === 'animated' ? 'video/webm' : 'image/png';
            $name = 'sticker';
        } elseif (!$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'الملف مطلوب.'], 422);
        }

        try {
            if ($kind === null) {
                $file = $request->file('file');
                $path = $file->store('message_attachments', 'public');
                $mime = Storage::disk('public')->mimeType($path) ?: $file->getMimeType();
                $name = $file->getClientOriginalName() ?: basename($path);
            }

            $caption = trim($data['content'] ?? '');
            $message = Message::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'content' => $caption ?: $name,
                'attachment_path' => $path,
                'attachment_type' => $mime,
                'attachment_kind' => $kind,
                'attachment_name' => $name,
                'reply_to' => $data['reply_to'] ?? null,
                'is_sensitive' => (bool) ($data['is_sensitive'] ?? false),
            ]);

            if ($sticker) {
                $this->touchStickerRecent($sender, $sticker->id);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'senderId' => $message->sender_id,
                    'recipientId' => $message->recipient_id,
                    'content' => $message->content,
                    'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
                    'attachmentName' => $message->attachment_name,
                    'attachmentMime' => $message->attachment_type,
                    'attachmentKind' => $message->attachment_kind,
                    'isSensitive' => $message->is_sensitive,
                    'isEdited' => $message->is_edited,
                    'createdAt' => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    'readAt' => $message->read_at,
                    'audioPosition' => 0,
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'تعذر رفع المرفق.',
            ], 500);
        }
    }

    public function searchMessages(Request $request)
    {
        $user = Auth::user();
        $query = $request->query('q', '');

        $query = mb_substr(trim($query), 0, 100);

        if (mb_strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $messages = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
        })
        ->whereRaw('MATCH(content) AGAINST(? IN BOOLEAN MODE)', ['"' . addslashes($query) . '"'])
        ->with(['sender', 'recipient', 'replyTo.sender'])
        ->orderBy('created_at', 'desc')
        ->limit(self::SEARCH_LIMIT)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        $contactId = (int) $request->input('contact_id');

        $readAt = now();
        Message::where('recipient_id', $user->id)
            ->where('sender_id', $contactId)
            ->whereNull('read_at')
            ->update(['read_at' => $readAt, 'updated_at' => $readAt]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة القراءة',
        ]);
    }

    public function markSingleMessageAsRead(Request $request)
    {
        $user = Auth::user();
        $messageId = (int) $request->input('message_id');

        $message = Message::findOrFail($messageId);

        // Check if user can mark this message as read
        if ($message->recipient_id !== $user->id) {
            abort(403, 'لا يمكنك تحديث هذه الرسالة.');
        }

        $readAt = now();
        $message->update(['read_at' => $readAt, 'updated_at' => $readAt]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة القراءة',
        ]);
    }

    public function update(Request $request, Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'لا يمكنك تعديل رسالة الآخرين.');
        }

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['content'] = $this->sanitizeContent($data['content'] ?? null);

        if (empty($data['content'])) {
            return response()->json(['success' => false, 'message' => 'يجب إدخال محتوى الرسالة.'], 422);
        }

        $message->update([
            'content' => $data['content'],
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الرسالة بنجاح',
            'data' => [
                'id' => $message->id,
                'content' => $message->content,
                'createdAt' => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                'isEdited' => $message->is_edited,
            ],
        ]);
    }

    public function saveAudioPosition(Request $request, Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $data = $request->validate([
            'position' => ['required', 'numeric', 'min:0'],
        ]);

        DB::table('messages')->where('id', $message->id)->update(['audio_position' => $data['position']]);

        return response()->json(['success' => true]);
    }

    public function destroy(Message $message)
    {
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'لا يمكنك حذف رسالة الآخرين.');
        }

        // Delete attachment if exists
        if ($message->attachment_path && Storage::disk('public')->exists($message->attachment_path)) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح',
        ]);
    }

    public function delta(Request $request)
    {
        $user = Auth::user();
        $recipientId = (int) $request->query('recipient_id');
        $recipient = User::findOrFail($recipientId);
        $sinceId = (int) $request->query('since_id', 0);
        // Client sends the timestamp of its last successful sync so we can
        // detect edits, read-receipts and deletions that happened since then.
        $lastSync = $request->query('last_sync');
        $lastSyncTs = null;
        if (!empty($lastSync)) {
            try {
                $lastSyncTs = Carbon::parse($lastSync);
            } catch (\Throwable $e) {
                $lastSyncTs = null;
            }
        }

        if (!$this->canMessage($user, $recipient)) {
            abort(403, 'غير مصرح لهذا التراسل.');
        }

        if ((string) $request->query('mark_read') === '1') {
            $readAt = now();
            Message::where('sender_id', $recipient->id)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => $readAt, 'updated_at' => $readAt]);
        }

        $messages = Message::between($user, $recipient)
            ->with(['sender', 'replyTo.sender'])
            ->when($sinceId > 0, fn ($query) => $query->where('id', '>', $sinceId))
            ->orderBy('created_at')
            ->get();

        $newReactions = $this->reactionsForMessages($messages->pluck('id'), (int) $user->id);
        $newPinned = $this->pinnedForMessages($messages->pluck('id'));

        // Detect changes to already-delivered messages (id <= sinceId).
        $updated = [];
        $deleted = [];
        $newlyRead = [];
        $changedReactions = [];

        if ($sinceId > 0 && $lastSyncTs) {
            // Edited messages: updated_at moved past last sync (excluding pure read bumps handled below).
            $changed = Message::between($user, $recipient)
                ->where('id', '<=', $sinceId)
                ->where('updated_at', '>', $lastSyncTs)
                ->get();

            foreach ($changed as $message) {
                // Read-receipt change on my own outgoing messages.
                if ($message->read_at && $message->read_at->greaterThan($lastSyncTs) && $message->sender_id === $user->id) {
                    $newlyRead[] = [
                        'id' => $message->id,
                        'readAt' => $message->read_at?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    ];
                }

                if ($message->is_edited) {
                    $updated[] = [
                        'id' => $message->id,
                        'content' => $message->content,
                        'isEdited' => true,
                    ];
                }
            }

            // Soft-deleted messages since last sync.
            $deleted = Message::onlyTrashed()
                ->where(function ($q) use ($user, $recipient) {
                    $q->where(function ($qq) use ($user, $recipient) {
                        $qq->where('sender_id', $user->id)->where('recipient_id', $recipient->id);
                    })->orWhere(function ($qq) use ($user, $recipient) {
                        $qq->where('sender_id', $recipient->id)->where('recipient_id', $user->id);
                    });
                })
                ->where('deleted_at', '>', $lastSyncTs)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            // Reactions changed on existing messages since last sync.
            $reactionMessageIds = DB::table('message_reactions as r')
                ->join('messages as m', 'm.id', '=', 'r.message_id')
                ->where('r.updated_at', '>', $lastSyncTs)
                ->where(function ($q) use ($user, $recipient) {
                    $q->where(function ($qq) use ($user, $recipient) {
                        $qq->where('m.sender_id', $user->id)->where('m.recipient_id', $recipient->id);
                    })->orWhere(function ($qq) use ($user, $recipient) {
                        $qq->where('m.sender_id', $recipient->id)->where('m.recipient_id', $user->id);
                    });
                })
                ->pluck('r.message_id')
                ->unique()
                ->values();

            if ($reactionMessageIds->isNotEmpty()) {
                $changedReactions = collect($this->reactionsForMessages($reactionMessageIds, (int) $user->id))
                    ->map(fn ($reactions, $id) => ['id' => (int) $id, 'reactions' => $reactions])
                    ->values()
                    ->all();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'new' => $messages->map(fn ($message) => $this->messagePayload($message, (int) $user->id, $newReactions, $newPinned))->values(),
                'updated' => $updated,
                'deleted' => $deleted,
                'newly_read' => $newlyRead,
                'reactions' => $changedReactions,
                'server_time' => now()->toISOString(),
                'contact' => $this->publicContactPayload($user, $recipient, [
                    'isTyping' => Cache::has('messaging-typing-' . $user->id . '-' . $recipient->id),
                ]),
            ],
        ]);
    }

    public function stream(Request $request)
    {
        $user = Auth::user();
        $recipientId = (int) $request->query('recipient_id');
        $sinceId = (int) $request->query('since_id', 0);
        $recipient = User::findOrFail($recipientId);

        if (!$this->canMessage($user, $recipient)) {
            abort(403);
        }

        // ONE-SHOT SSE — responds immediately and closes connection.
        // This frees the PHP thread so images/audio/video can load.
        $lastId      = max(0, $sinceId);
        $newMessages = Message::between($user, $recipient)
            ->with(['sender'])->where('id', '>', $lastId)->orderBy('created_at')->get();
        $contactPayload = $this->publicContactPayload($user, $recipient);
        return response()->stream(function () use ($newMessages, $contactPayload) {
            echo "retry: 4000\n\n";
            foreach ($newMessages as $message) {
                echo "event: new_message\n";
                echo "data: " . json_encode([
                    'id'             => $message->id,
                    'senderId'       => $message->sender_id,
                    'recipientId'    => $message->recipient_id,
                    'senderName'     => $message->sender?->name,
                    'content'        => $message->content,
                    'attachmentUrl'  => $this->getSecureAttachmentUrl($message->attachment_path),
                    'attachmentName' => $message->attachment_name,
                    'attachmentMime' => $message->attachment_type,
                    'attachmentKind' => $message->attachment_kind,
                    'isEdited'       => $message->is_edited,
                    'createdAt'      => $message->created_at->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    'readAt'         => $message->read_at,
                    'audioPosition'  => (float) ($message->audio_position ?? 0),
                    'isSensitive'    => (bool) $message->is_sensitive,
                ]) . "\n\n";
            }
            echo "event: contact_update\n";
            echo "data: " . json_encode($contactPayload) . "\n\n";
            ob_flush(); flush();
        }, 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection'    => 'close',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function pinMessage(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'message_id' => ['required', 'integer', 'exists:messages,id'],
            'pinned' => ['nullable', 'boolean'],
        ]);

        $message = Message::findOrFail((int) $data['message_id']);
        if ((int) $message->sender_id !== (int) $user->id && (int) $message->recipient_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $userA = min((int) $message->sender_id, (int) $message->recipient_id);
        $userB = max((int) $message->sender_id, (int) $message->recipient_id);
        $existing = DB::table('pinned_messages')
            ->where('message_id', $message->id)
            ->where('user_a_id', $userA)
            ->where('user_b_id', $userB)
            ->first();

        $shouldPin = array_key_exists('pinned', $data) ? (bool) $data['pinned'] : !$existing;
        if ($shouldPin) {
            DB::table('pinned_messages')->updateOrInsert(
                ['message_id' => $message->id, 'user_a_id' => $userA, 'user_b_id' => $userB],
                ['pinned_by' => $user->id, 'pinned_at' => now(), 'created_at' => now(), 'updated_at' => now()]
            );
        } else {
            DB::table('pinned_messages')
                ->where('message_id', $message->id)
                ->where('user_a_id', $userA)
                ->where('user_b_id', $userB)
                ->delete();
        }

        return response()->json(['success' => true, 'data' => ['message_id' => $message->id, 'isPinned' => $shouldPin]]);
    }

    public function forwardMessage(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'message_id' => ['required', 'integer', 'exists:messages,id'],
            'recipient_ids' => ['required', 'array', 'min:1', 'max:10'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $source = Message::findOrFail((int) $data['message_id']);
        if ((int) $source->sender_id !== (int) $user->id && (int) $source->recipient_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $originalSenderSettings = DB::table('user_messaging_settings')->where('user_id', $source->sender_id)->first();
        $originalSenderPrivacy = $this->settingsPayload($originalSenderSettings)['privacy'];
        $allowAttribution = ($originalSenderPrivacy['forwardedMessagesFor'] ?? 'all') !== 'nobody';

        $created = [];
        foreach (collect($data['recipient_ids'])->map(fn ($id) => (int) $id)->unique() as $recipientId) {
            $recipient = User::findOrFail($recipientId);
            if (!$this->canMessage($user, $recipient)) {
                continue;
            }

            $message = Message::create([
                'sender_id' => $user->id,
                'recipient_id' => $recipient->id,
                'content' => $source->content,
                'attachment_path' => $source->attachment_path,
                'attachment_type' => $source->attachment_type,
                'attachment_name' => $source->attachment_name,
                'forwarded_from_message_id' => $allowAttribution ? $source->id : null,
            ]);

            DB::table('message_forwards')->insert([
                'source_message_id' => $source->id,
                'forwarded_message_id' => $message->id,
                'forwarded_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $created[] = $this->messagePayload($message->fresh(['sender']), $user->id);
        }

        return response()->json(['success' => true, 'data' => ['messages' => $created]]);
    }


    public function updateLocale(Request $request)
    {
        $data = $request->validate(['locale' => ['required', 'string', 'in:ar,en']]);

        Auth::user()->update(['locale' => $data['locale']]);

        return response()->json(['success' => true, 'data' => ['locale' => $data['locale']]]);
    }



    public function typingPing(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'is_typing' => ['required', 'boolean'],
            'media_type' => ['nullable', 'string', 'in:image,video,audio,file'],
        ]);

        $recipient = User::findOrFail((int) $data['recipient_id']);
        if (!$this->canMessage($user, $recipient)) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }

        $mediaType = $data['media_type'] ?? null;
        $key = 'messaging-typing-' . $recipient->id . '-' . $user->id;
        if ((bool) $data['is_typing']) {
            Cache::put($key, $mediaType ?: true, now()->addSeconds(8));
        } else {
            Cache::forget($key);
        }

        // Instant real-time broadcast (bypasses queue)
        broadcast(new \App\Events\UserTyping(
            (int) $user->id,
            (int) $recipient->id,
            (bool) $data['is_typing'],
            $mediaType
        ));

        return response()->json(['success' => true]);
    }

    public function fixOldAttachments(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $user = Auth::user();

        // Only allow teachers to run this
        if ($user->role !== 'teacher') {
            abort(403, 'غير مصرح لك بهذا الإجراء');
        }

        $updated = 0;

        // Fix messages with old attachment paths
        $messages = Message::whereNotNull('attachment_path')
            ->where(function ($query) {
                $query->where('attachment_path', 'not like', 'message_attachments/%')
                    ->where('attachment_path', 'not like', 'message_audio/%')
                    ->where('attachment_path', 'not like', '/storage/%');
            })
            ->get();

        foreach ($messages as $message) {
            $oldPath = $message->attachment_path;

            // Fix old paths
            if (str_starts_with($oldPath, 'storage/')) {
                $newPath = str_replace('storage/', '', $oldPath);
                $message->update(['attachment_path' => $newPath]);
                $updated++;
            } elseif (!str_starts_with($oldPath, 'message_attachments/') && !str_starts_with($oldPath, 'message_audio/')) {
                // Assume it's in message_attachments if not specified
                $message->update(['attachment_path' => 'message_attachments/' . $oldPath]);
                $updated++;
            }
        }

        return response()->json(['success' => true, 'updated_count' => $updated]);
    }

    // ─── Saved Messages ──────────────────────────────────────────────

    public function getSavedMessages(Request $request)
    {
        $user = Auth::user();
        $saved = \App\Models\SavedMessage::where('user_id', $user->id)
            ->with(['message.sender'])
            ->orderBy('saved_at', 'desc')
            ->get()
            ->map(function ($s) {
                $msg = $s->message;
                if (!$msg) return null;
                return [
                    'savedId'        => $s->id,
                    'savedAt'        => $s->saved_at?->toISOString(),
                    'id'             => $msg->id,
                    'content'        => $msg->content,
                    'senderId'       => $msg->sender_id,
                    'senderName'     => $msg->sender?->name,
                    'senderAvatar'   => $msg->sender?->avatar_url ? asset('storage/' . ltrim($msg->sender->avatar_url, '/')) : null,
                    'attachmentUrl'  => $this->getSecureAttachmentUrl($msg->attachment_path),
                    'attachmentName' => $msg->attachment_name,
                    'attachmentMime' => $msg->attachment_type,
                    'attachmentKind' => $msg->attachment_kind,
                    'messageType'    => $msg->message_type,
                    'createdAt'      => $msg->created_at?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
                    'recipientId'    => $msg->recipient_id,
                    'audioPosition'  => (float) ($msg->audio_position ?? 0),
                    'isSensitive'    => (bool) $msg->is_sensitive,
                ];
            })->filter()->values();

        return response()->json(['success' => true, 'data' => $saved]);
    }

    public function saveMessage(Request $request, $messageId)
    {
        $user = Auth::user();
        $message = Message::findOrFail($messageId);
        // Ensure user is part of this conversation
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403);
        }
        \App\Models\SavedMessage::firstOrCreate(
            ['user_id' => $user->id, 'message_id' => $message->id],
            ['saved_at' => now()]
        );
        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    public function unsaveMessage(Request $request, $messageId)
    {
        $user = Auth::user();
        \App\Models\SavedMessage::where('user_id', $user->id)
            ->where('message_id', $messageId)
            ->delete();
        return response()->json(['success' => true]);
    }

    public function getSavedMessageIds(Request $request)
    {
        $user = Auth::user();
        $ids = \App\Models\SavedMessage::where('user_id', $user->id)->pluck('message_id');
        return response()->json(['success' => true, 'ids' => $ids]);
    }

    // ─── Wallpaper ───────────────────────────────────────────────────

    public function setWallpaper(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate(['contact_id' => ['required', 'integer', 'exists:users,id'], 'wallpaper_key' => ['nullable', 'string', 'max:60'], 'custom_gradient' => ['nullable', 'string', 'max:255']]);

        $contact = User::findOrFail((int) $data['contact_id']);
        if (!$this->canMessage($user, $contact)) {
            abort(403, 'Unauthorized conversation.');
        }

        $userA = min($user->id, (int) $data['contact_id']); $userB = max($user->id, (int) $data['contact_id']);
        DB::table('conversation_wallpapers')->updateOrInsert(['user_a_id' => $userA, 'user_b_id' => $userB], ['wallpaper_key' => $data['wallpaper_key'] ?? null, 'custom_gradient' => $data['custom_gradient'] ?? null, 'updated_at' => now(), 'created_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function getWallpaper(Request $request)
    {
        $user = Auth::user(); $contactId = (int) $request->query('contact_id');

        $contact = User::find($contactId);
        if (!$contact || !$this->canMessage($user, $contact)) {
            abort(403, 'Unauthorized conversation.');
        }

        $userA = min($user->id, $contactId); $userB = max($user->id, $contactId);
        $wp = DB::table('conversation_wallpapers')->where('user_a_id', $userA)->where('user_b_id', $userB)->first();
        return response()->json(['success' => true, 'data' => $wp ? ['wallpaper_key' => $wp->wallpaper_key, 'custom_gradient' => $wp->custom_gradient] : null]);
    }

    // ─── Reactions ───────────────────────────────────────────────────

    /**
     * Toggle a single emoji reaction by the current user on a message.
     * A user may hold at most one reaction emoji per message: selecting a new
     * emoji replaces the previous one; selecting the same emoji removes it.
     */
    public function toggleReaction(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'message_id' => ['required', 'integer', 'exists:messages,id'],
            'emoji'      => ['required', 'string', 'max:10'],
        ]);

        $message = Message::findOrFail($data['message_id']);

        // The user must be a participant in this conversation.
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            abort(403, 'لا يمكنك التفاعل مع هذه الرسالة.');
        }

        $emoji = trim($data['emoji']);

        DB::transaction(function () use ($message, $user, $emoji) {
            $current = DB::table('message_reactions')
                ->where('message_id', $message->id)
                ->where('user_id', $user->id)
                ->first();

            if ($current && $current->emoji === $emoji) {
                // Same emoji tapped again → remove it.
                DB::table('message_reactions')
                    ->where('message_id', $message->id)
                    ->where('user_id', $user->id)
                    ->delete();
                return;
            }

            // Replace any previous reaction by this user, then add the new one.
            DB::table('message_reactions')
                ->where('message_id', $message->id)
                ->where('user_id', $user->id)
                ->delete();

            DB::table('message_reactions')->insert([
                'message_id' => $message->id,
                'user_id'    => $user->id,
                'emoji'      => $emoji,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'message_id' => $message->id,
                'reactions'  => $this->formatReactions($message->id, $user->id),
            ],
        ]);
    }

    /**
     * Build the reaction payload for one message in the exact shape the
     * frontend expects: [{ emoji, count, myReaction }].
     */
    protected function formatReactions(int $messageId, int $userId): array
    {
        $rows = DB::table('message_reactions')
            ->where('message_id', $messageId)
            ->get(['user_id', 'emoji']);

        return $rows
            ->groupBy('emoji')
            ->map(fn ($group, $emoji) => [
                'emoji'      => (string) $emoji,
                'count'      => $group->count(),
                'myReaction' => $group->contains(fn ($r) => (int) $r->user_id === $userId),
            ])
            ->values()
            ->all();
    }

    /**
     * Bulk-load reactions for a set of message ids, keyed by message id.
     * Avoids N+1 queries when serializing a conversation page.
     */
    protected function reactionsForMessages($messageIds, int $userId): array
    {
        $ids = collect($messageIds)->filter()->map(fn ($id) => (int) $id)->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        $rows = DB::table('message_reactions')
            ->whereIn('message_id', $ids->all())
            ->get(['message_id', 'user_id', 'emoji']);

        return $rows
            ->groupBy('message_id')
            ->map(fn ($byMessage) => $byMessage
                ->groupBy('emoji')
                ->map(fn ($group, $emoji) => [
                    'emoji'      => (string) $emoji,
                    'count'      => $group->count(),
                    'myReaction' => $group->contains(fn ($r) => (int) $r->user_id === $userId),
                ])
                ->values()
                ->all())
            ->all();
    }

    protected function pinnedForMessages($messageIds): array
    {
        $ids = collect($messageIds)->filter()->map(fn ($id) => (int) $id)->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        return DB::table('pinned_messages')
            ->whereIn('message_id', $ids->all())
            ->pluck('message_id')
            ->mapWithKeys(fn ($id) => [(int) $id => true])
            ->all();
    }

    protected function messagePayload(Message $message, int $viewerId, array $reactions = [], array $pinned = []): array
    {
        $message->loadMissing('sender', 'replyTo.sender');

        $reply = null;
        if ($message->reply_to && $message->replyTo) {
            $reply = [
                'id' => $message->replyTo->id,
                'content' => $message->replyTo->content,
                'senderName' => $message->replyTo->sender?->name,
                'attachmentName' => $message->replyTo->attachment_name,
            ];
        }

        return [
            'id' => $message->id,
            'senderId' => $message->sender_id,
            'recipientId' => $message->recipient_id,
            'senderName' => $message->sender?->name,
            'senderAvatar' => $message->sender?->avatar_url ? asset('storage/' . ltrim($message->sender->avatar_url, '/')) : null,
            'content' => $message->content,
            'attachmentUrl' => $this->getSecureAttachmentUrl($message->attachment_path),
            'attachmentName' => $message->attachment_name,
            'attachmentMime' => $message->attachment_type,
            'attachmentKind' => $message->attachment_kind,
            'isEdited' => (bool) $message->is_edited,
            'createdAt' => $message->created_at?->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'readAt' => $message->read_at,
            'replyTo' => $reply,
            'reactions' => $reactions[$message->id] ?? [],
            'isPinned' => (bool) ($pinned[$message->id] ?? false),
            'forwardedFromMessageId' => $message->forwarded_from_message_id,
            'audioPosition' => (float) ($message->audio_position ?? 0),
            'isSensitive' => (bool) $message->is_sensitive,
        ];
    }

    protected function accountPayload(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'email'      => $user->email,
            'role'       => $user->role,
            'avatar_url' => $user->avatar_url,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    public function exportMyData(Request $request)
    {
        $user = Auth::user();

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderBy('created_at')
            ->get(['id', 'sender_id', 'recipient_id', 'content', 'attachment_name', 'created_at'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'direction' => $m->sender_id === $user->id ? 'sent' : 'received',
                'other_user_id' => $m->sender_id === $user->id ? $m->recipient_id : $m->sender_id,
                'content' => $m->content,
                'attachment_name' => $m->attachment_name,
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        $payload = json_encode([
            'exported_at' => now()->toIso8601String(),
            'account' => $this->accountPayload($user),
            'messages' => $messages,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return response($payload, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="my-messages.json"',
        ]);
    }

    public function getFrequentContacts(Request $request)
    {
        $user = Auth::user();

        $contacts = Message::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->selectRaw('CASE WHEN sender_id = ? THEN recipient_id ELSE sender_id END as other_id, COUNT(*) as cnt', [$user->id])
            ->groupBy('other_id')
            ->orderByDesc('cnt')
            ->limit(self::FREQUENT_CONTACTS)
            ->get();

        $users = User::whereIn('id', $contacts->pluck('other_id'))->get()->keyBy('id');

        $data = $contacts->map(function ($row) use ($users) {
            $u = $users->get($row->other_id);
            if (!$u) {
                return null;
            }
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar_url' => $u->avatar_url,
                'message_count' => (int) $row->cnt,
            ];
        })->filter()->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * يولّد صورة QR حقيقية تُرمّز رابط بطاقة التعريف العامة لهذا المستخدم،
     * بحيث يفتحها أي قارئ أكواد عادي (كاميرا الهاتف) مباشرة كرابط حقيقي.
     */
    public function qrCode(User $user)
    {
        $url = route('profile.card', $user);

        $result = (new \Endroid\QrCode\Builder\Builder(
            data: $url,
            size: 320,
            margin: 10,
            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
        ))->build();

        return response($result->getString(), 200)
            ->header('Content-Type', $result->getMimeType())
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * بطاقة تعريف عامة آمنة: الاسم + الصورة + الدور فقط، بلا بريد أو هاتف،
     * يمكن لأي شخص مسح الكود ومشاهدتها دون تسجيل دخول.
     */
    public function publicProfileCard(User $user)
    {
        $authUser = Auth::user();
        $route = $authUser->role === 'teacher' ? 'teacher.messaging' : 'student.messaging';

        if ((int) Auth::id() === (int) $user->id) {
            return redirect()->route($route);
        }

        return redirect()->route($route, ['open_user' => $user->id]);
    }

    // ── E2E Encryption ──────────────────────────────────────────────────────

    public function registerEncryptionKey(Request $request)
    {
        $request->validate(['public_key' => 'required|string|max:2048']);

        \App\Models\UserEncryptionKey::updateOrCreate(
            ['user_id' => auth()->id()],
            ['public_key' => $request->public_key, 'rotated_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    public function getEncryptionPublicKey(User $user)
    {
        $key = \App\Models\UserEncryptionKey::where('user_id', $user->id)->value('public_key');
        return response()->json(['public_key' => $key]);
    }

    public function contactsUnread(Request $request)
    {
        $user = Auth::user();

        $unreadCounts = Message::where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->select(DB::raw('sender_id, COUNT(*) as cnt, MAX(id) as last_id'))
            ->get()
            ->keyBy('sender_id');

        if ($unreadCounts->isEmpty()) {
            return response()->json(['success' => true, 'contacts' => []]);
        }

        $senderIds = $unreadCounts->keys()->all();
        $senders = User::whereIn('id', $senderIds)->select('id', 'name', 'avatar_url')->get()->keyBy('id');

        $lastMessages = Message::whereIn('id', $unreadCounts->pluck('last_id')->all())
            ->select('id', 'sender_id', 'content', 'attachment_name', 'message_type')
            ->get()
            ->keyBy('sender_id');

        $contacts = $unreadCounts->map(function ($row) use ($senders, $lastMessages) {
            $sender = $senders->get($row->sender_id);
            $msg = $lastMessages->get($row->sender_id);
            return [
                'id'          => $row->sender_id,
                'name'        => $sender?->name ?? '?',
                'avatar_url'  => $sender?->avatar_url,
                'unreadCount' => (int) $row->cnt,
                'lastMessage' => $msg ? ($msg->content ?: ($msg->attachment_name ?: 'مرفق')) : '',
            ];
        })->values()->all();

        return response()->json(['success' => true, 'contacts' => $contacts]);
    }
}
