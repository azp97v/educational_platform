<?php

namespace App\Http\Controllers\Traits;

use App\Models\BlockedContact;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait MessagingPrivacyTrait
{
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

    protected function sanitizeContent(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $content);
        $clean = $clean ?? '';
        $clean = strip_tags($clean);
        $clean = htmlspecialchars($clean, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
        $clean = trim($clean);

        return $clean === '' ? null : $clean;
    }

    protected function settingsPayload($settings): array
    {
        $decode = static function ($value, array $fallback): array {
            if (is_array($value)) {
                return array_merge($fallback, $value);
            }
            if (is_string($value) && $value !== '') {
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                    return array_merge($fallback, $decoded);
                }
            }
            return $fallback;
        };

        return [
            'privacy' => $decode($settings->privacy ?? null, [
                'lastSeenFor'            => 'all',
                'profilePhotoFor'        => 'all',
                'messageFrom'            => 'all',
                'callFrom'               => 'all',
                'phoneVisibleFor'        => 'contacts',
                'forwardedMessagesFor'   => 'all',
                'hideOnlineStatus'       => false,
                'autoDeleteDays'         => 0,
                'frequentContactsEnabled'=> true,
                'deleteAccountAfterMonths' => 0,
            ]),
            'notifications' => $decode($settings->notifications ?? null, [
                'soundEnabled'   => true,
                'previewEnabled' => true,
                'badgeEnabled'   => true,
                'desktopEnabled' => false,
                'volume'         => 100,
            ]),
            'media' => $decode($settings->media ?? null, [
                'autoDownloadImages' => true,
                'autoDownloadVideos' => false,
                'autoDownloadFiles'  => false,
                'quality'            => '720p',
                'wifiOnly'           => true,
            ]),
            'security' => $decode($settings->security ?? null, [
                'pinEnabled'   => false,
                'twoFaEnabled' => false,
            ]),
            'chats' => $decode($settings->chats ?? null, [
                'defaultWallpaper'   => 'default',
                'compactMode'        => false,
                'sendWithEnter'      => true,
                'reduceMotion'       => false,
                'defaultTheme'       => '',
                'nameColor'          => '',
                'fontFamily'         => 'default',
                'autoNightMode'      => false,
                'doubleClickAction'  => 'reply',
                'tabsPosition'       => 'left',
                'spellcheckEnabled'  => true,
                'showFolderTags'     => false,
                'showUnreadInTitle'  => false,
            ]),
        ];
    }
}
