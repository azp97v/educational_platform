<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserMessagingSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeactivateInactiveAccounts extends Command
{
    protected $signature = 'messaging:deactivate-inactive';

    protected $description = 'Deactivate (status=inactive) accounts past their configured inactivity window';

    public function handle(): int
    {
        $settings = UserMessagingSetting::whereNotNull('privacy')->get();
        $deactivated = 0;

        foreach ($settings as $setting) {
            $months = (int) ($setting->privacy['deleteAccountAfterMonths'] ?? 0);
            if ($months <= 0) {
                continue;
            }

            $user = User::find($setting->user_id);
            if (!$user || $user->status === 'inactive') {
                continue;
            }

            $lastActivityTs = DB::table('sessions')->where('user_id', $user->id)->max('last_activity');
            $lastActivity = $lastActivityTs ? now()->createFromTimestamp($lastActivityTs) : $user->created_at;

            if ($lastActivity && $lastActivity->lt(now()->subMonths($months))) {
                $user->update(['status' => 'inactive']);
                $deactivated++;
            }
        }

        $this->info("Deactivated {$deactivated} inactive accounts.");

        return self::SUCCESS;
    }
}
