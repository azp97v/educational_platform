<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\UserMessagingSetting;
use Illuminate\Console\Command;

class DeleteExpiredMessages extends Command
{
    protected $signature = 'messaging:delete-expired';

    protected $description = 'Delete messages older than each user\'s configured auto-delete duration';

    public function handle(): int
    {
        $settings = UserMessagingSetting::whereNotNull('privacy')->get();
        $deleted = 0;

        foreach ($settings as $setting) {
            $days = (int) ($setting->privacy['autoDeleteDays'] ?? 0);
            if ($days <= 0) {
                continue;
            }

            $userId = $setting->user_id;
            $cutoff = now()->subDays($days);

            $deleted += Message::where('created_at', '<', $cutoff)
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)->orWhere('recipient_id', $userId);
                })
                ->delete();
        }

        $this->info("Deleted {$deleted} expired messages.");

        return self::SUCCESS;
    }
}
