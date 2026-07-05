<?php

namespace App\Events;

use App\Models\Call;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * يُبَث لكل المشاركين الحاليين في المكالمة الجماعية عند انضمام مشارك جديد،
 * بحيث ينشئ كل واحد منهم اتصال WebRTC جديد (mesh) موجّه للمنضم الجديد.
 */
class GroupParticipantJoined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public User $joinedUser,
        public array $existingParticipantIds
    ) {
    }

    public function broadcastOn(): array
    {
        return array_map(
            fn ($id) => new PrivateChannel('user.' . $id),
            $this->existingParticipantIds
        );
    }

    public function broadcastAs(): string
    {
        return 'call.participant-joined';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'user' => [
                'id' => $this->joinedUser->id,
                'name' => $this->joinedUser->name,
                'avatar_url' => $this->joinedUser->avatar_url,
            ],
        ];
    }
}
