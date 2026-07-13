<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Call $call)
    {
    }

    public function broadcastOn(): array
    {
        // Broadcast to every participant (handles group calls and avoids null recipient_id)
        $userIds = $this->call->participants()
            ->pluck('user_id')
            ->filter()
            ->unique();

        $channels = $userIds->map(fn ($id) => new PrivateChannel('user.' . $id))->all();

        return $channels ?: [new PrivateChannel('user.' . $this->call->caller_id)];
    }

    public function broadcastAs(): string
    {
        return 'call.ended';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'status' => $this->call->status,
            'duration_seconds' => $this->call->duration_seconds,
        ];
    }
}
