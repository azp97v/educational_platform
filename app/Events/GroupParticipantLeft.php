<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupParticipantLeft implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public int $leftUserId,
        public array $remainingParticipantIds
    ) {
    }

    public function broadcastOn(): array
    {
        return array_map(
            fn ($id) => new PrivateChannel('user.' . $id),
            $this->remainingParticipantIds
        );
    }

    public function broadcastAs(): string
    {
        return 'call.participant-left';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'user_id' => $this->leftUserId,
        ];
    }
}
