<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GroupMessageController extends Controller
{
    private function authorizeGroupMember(int $groupId): object
    {
        $group = DB::table('groups')->where('id', $groupId)->first();
        abort_if(!$group, 404, 'المجموعة غير موجودة');
        $isMember = DB::table('group_participants')
            ->where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->exists();
        abort_unless($isMember, 403, 'لست عضواً في هذه المجموعة');
        return $group;
    }

    private function formatMsg(object $msg): array
    {
        return [
            'id'             => $msg->id,
            'groupId'        => $msg->group_id,
            'senderId'       => $msg->sender_id,
            'senderName'     => $msg->sender_name ?? '',
            'senderAvatar'   => $msg->sender_avatar ? asset('storage/' . $msg->sender_avatar) : null,
            'content'        => $msg->content,
            'attachmentUrl'  => $msg->attachment_path ? asset('storage/' . $msg->attachment_path) : null,
            'attachmentName' => $msg->attachment_name,
            'attachmentMime' => $msg->attachment_type,
            'attachmentKind' => $msg->attachment_kind,
            'audioPath'      => $msg->audio_path ? asset('storage/' . $msg->audio_path) : null,
            'audioDuration'  => $msg->audio_duration,
            'messageType'    => $msg->message_type ?? 'text',
            'isEdited'       => (bool) $msg->is_edited,
            'replyToId'      => $msg->reply_to,
            'createdAt'      => Carbon::parse($msg->created_at)->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP'),
            'isGroup'        => true,
        ];
    }

    private function withSender()
    {
        return DB::table('group_messages as gm')
            ->join('users as u', 'u.id', '=', 'gm.sender_id')
            ->select('gm.*', 'u.name as sender_name', 'u.avatar_url as sender_avatar');
    }

    public function load(Request $request, int $groupId)
    {
        $this->authorizeGroupMember($groupId);
        $user = Auth::user();

        $messages = $this->withSender()
            ->where('gm.group_id', $groupId)
            ->whereNull('gm.deleted_at')
            ->orderBy('gm.created_at')
            ->orderBy('gm.id')
            ->limit(100)
            ->get();

        DB::table('group_message_reads')->updateOrInsert(
            ['group_id' => $groupId, 'user_id' => $user->id],
            ['last_read_message_id' => $messages->last()?->id ?? 0, 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json([
            'success'  => true,
            'messages' => $messages->map(fn($m) => $this->formatMsg($m))->values(),
        ]);
    }

    public function send(Request $request, int $groupId)
    {
        $this->authorizeGroupMember($groupId);
        $user = Auth::user();

        $data = $request->validate([
            'content'  => ['nullable', 'string', 'max:10000'],
            'reply_to' => ['nullable', 'integer'],
        ]);

        $content = trim($data['content'] ?? '');
        if ($content === '') {
            return response()->json(['success' => false, 'message' => 'الرسالة فارغة'], 422);
        }

        $msgId = DB::table('group_messages')->insertGetId([
            'group_id'     => $groupId,
            'sender_id'    => $user->id,
            'content'      => $content,
            'reply_to'     => $data['reply_to'] ?? null,
            'message_type' => 'text',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        $msg = $this->withSender()->where('gm.id', $msgId)->first();

        DB::table('group_message_reads')->updateOrInsert(
            ['group_id' => $groupId, 'user_id' => $user->id],
            ['last_read_message_id' => $msgId, 'updated_at' => now(), 'created_at' => now()]
        );

        $payload = $this->formatMsg($msg);

        // Broadcast to all other members
        $members = DB::table('group_participants')
            ->where('group_id', $groupId)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id');

        foreach ($members as $memberId) {
            try {
                broadcast(new \App\Events\GroupMessageSent($groupId, (int) $memberId, $payload));
            } catch (\Throwable $e) {
                Log::warning('GroupMessageSent broadcast failed: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => $payload]);
    }

    public function delta(Request $request, int $groupId)
    {
        $this->authorizeGroupMember($groupId);
        $user = Auth::user();

        $afterId = (int) $request->query('after_id', 0);

        $messages = $this->withSender()
            ->where('gm.group_id', $groupId)
            ->where('gm.id', '>', $afterId)
            ->whereNull('gm.deleted_at')
            ->orderBy('gm.created_at')
            ->limit(50)
            ->get();

        if ($messages->isNotEmpty()) {
            DB::table('group_message_reads')->updateOrInsert(
                ['group_id' => $groupId, 'user_id' => $user->id],
                ['last_read_message_id' => $messages->last()->id, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        return response()->json([
            'success'  => true,
            'messages' => $messages->map(fn($m) => $this->formatMsg($m))->values(),
        ]);
    }

    public function myGroups(Request $request)
    {
        $user = Auth::user();

        $groups = DB::table('groups as g')
            ->join('group_participants as gp', function ($j) use ($user) {
                $j->on('gp.group_id', '=', 'g.id')->where('gp.user_id', '=', $user->id);
            })
            ->leftJoin('group_message_reads as gmr', function ($j) use ($user) {
                $j->on('gmr.group_id', '=', 'g.id')->where('gmr.user_id', '=', $user->id);
            })
            ->select(
                'g.id',
                'g.name',
                'g.avatar_path',
                'g.created_by',
                'gmr.last_read_message_id'
            )
            ->get();

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
                'avatar_url'      => $g->avatar_path ? asset('storage/' . $g->avatar_path) : null,
                'lastMessage'     => $lastMsg ? (($lastMsg->sender_name ?? '') . ': ' . Str::limit($lastMsg->content ?? '', 60)) : '',
                'lastMessageTime' => $lastMsg ? Carbon::parse($lastMsg->created_at)->setTimezone('Asia/Riyadh')->format('Y-m-d\TH:i:sP') : null,
                'unreadCount'     => $unread,
                'isGroup'         => true,
                'hasConversation' => true,
                'isOnline'        => false,
                'lastSeenAt'      => null,
            ];
        }

        return response()->json(['success' => true, 'groups' => $result]);
    }
}
