<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public array $offer,
        public ?int $toUserId = null
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . ($this->toUserId ?? $this->call->recipient_id))];
    }

    public function broadcastAs(): string
    {
        return 'call.initiated';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'type' => $this->call->type,
            'is_group' => (bool) $this->call->is_group,
            'offer' => $this->offer,
            'caller' => [
                'id' => $this->call->caller->id,
                'name' => $this->call->caller->name,
                'avatar_url' => $this->call->caller->avatar_url,
            ],
        ];
    }
}
