<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IceCandidateSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public int $fromUserId,
        public int $toUserId,
        public array $candidate
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->toUserId)];
    }

    public function broadcastAs(): string
    {
        return 'call.ice-candidate';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'from_user_id' => $this->fromUserId,
            'candidate' => $this->candidate,
        ];
    }
}
