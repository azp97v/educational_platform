<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int   $groupId,
        public readonly int   $recipientUserId,
        public readonly array $message
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('user.' . $this->recipientUserId)];
    }

    public function broadcastAs(): string
    {
        return 'group.message';
    }

    public function broadcastWith(): array
    {
        return [
            'group_id' => $this->groupId,
            'message'  => $this->message,
        ];
    }
}
