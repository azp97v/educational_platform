<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\CallParticipant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupStaleCalls extends Command
{
    protected $signature   = 'calls:cleanup';
    protected $description = 'Mark stuck ringing/accepted calls as missed/ended and clean stale participants';

    public function handle(): int
    {
        // Calls stuck ringing > 3 min → missed
        $stuckRinging = Call::where('status', 'ringing')
            ->where('created_at', '<', now()->subMinutes(3))
            ->get();

        foreach ($stuckRinging as $call) {
            DB::transaction(function () use ($call) {
                $call->update(['status' => 'missed', 'ended_at' => now()]);
                $call->participants()
                    ->whereIn('status', ['ringing', 'invited'])
                    ->update(['status' => 'missed', 'left_at' => now()]);
            });
        }

        // Calls stuck accepted > 4 hours → ended
        $stuckAccepted = Call::where('status', 'accepted')
            ->where('answered_at', '<', now()->subHours(4))
            ->get();

        foreach ($stuckAccepted as $call) {
            DB::transaction(function () use ($call) {
                $duration = $call->answered_at
                    ? max(0, (int) now()->diffInSeconds($call->answered_at))
                    : 0;

                $call->update([
                    'status'           => 'ended',
                    'ended_at'         => now(),
                    'duration_seconds' => $duration,
                ]);

                $call->participants()
                    ->whereIn('status', ['joined', 'ringing'])
                    ->update(['status' => 'left', 'left_at' => now()]);
            });
        }

        // Orphaned participants (joined/ringing) on already-ended calls
        CallParticipant::whereIn('status', ['joined', 'ringing'])
            ->whereHas('call', fn ($q) => $q->whereIn('status', ['ended', 'missed', 'rejected']))
            ->update(['status' => 'left', 'left_at' => now()]);

        $this->info(sprintf(
            'Cleaned %d stuck ringing, %d stuck accepted, orphaned participants fixed.',
            $stuckRinging->count(),
            $stuckAccepted->count()
        ));

        return self::SUCCESS;
    }
}
