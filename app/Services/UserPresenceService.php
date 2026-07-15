<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserPresenceService
{
    public function isOnline(User $user): bool
    {
        return Cache::has('user-is-online-' . $user->id);
    }

    /**
     * Returns the most recent activity timestamp for the user, checking
     * the activity cache, active sessions table, and user->updated_at as fallback.
     */
    public function getLastActivityTimestamp(User $user): ?Carbon
    {
        $cachedActivity = null;
        $cached = Cache::get('last-activity-' . $user->id);

        if ($cached instanceof Carbon) {
            $cachedActivity = $cached->copy()->setTimezone('Asia/Riyadh');
        } elseif (is_numeric($cached)) {
            $cachedActivity = Carbon::createFromTimestamp((int) $cached, 'Asia/Riyadh');
        } elseif (is_string($cached) && trim($cached) !== '') {
            try {
                $cachedActivity = Carbon::parse($cached, 'Asia/Riyadh');
            } catch (\Throwable) {
                // fall through to session lookup
            }
        }

        $sessionActivity = null;
        $lastSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->first();

        if ($lastSession && isset($lastSession->last_activity)) {
            $sessionActivity = Carbon::createFromTimestamp((int) $lastSession->last_activity, 'Asia/Riyadh');
        }

        $fallbackUpdated = optional($user->updated_at)->copy()?->setTimezone('Asia/Riyadh');

        return collect([$cachedActivity, $sessionActivity, $fallbackUpdated])
            ->filter()
            ->sortByDesc(fn (Carbon $ts) => $ts->timestamp)
            ->first();
    }

    public function formatLastSeen(User $user): string
    {
        if ($this->isOnline($user)) {
            return 'متصل الآن';
        }

        $ts = $this->getLastActivityTimestamp($user);

        if (!$ts) {
            return 'غير متصل';
        }

        return 'آخر ظهور ' . $ts->diffForHumans();
    }
}
