<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallAnswered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public array $answer,
        public ?int $fromUserId = null,
        public ?int $toUserId = null
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . ($this->toUserId ?? $this->call->caller_id))];
    }

    public function broadcastAs(): string
    {
        return 'call.answered';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'from_user_id' => $this->fromUserId ?? $this->call->recipient_id,
            'answer' => $this->answer,
        ];
    }
}
