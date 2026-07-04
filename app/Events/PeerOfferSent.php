<?php

namespace App\Events;

use App\Models\Call;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * عرض WebRTC عام بين أي طرفين داخل مكالمة جماعية (مطلوب عند انضمام مشارك جديد
 * فينشئ كل مشارك موجود اتصالاً مستقلاً موجّهاً للمنضم الجديد).
 */
class PeerOfferSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Call $call,
        public int $fromUserId,
        public int $toUserId,
        public array $offer
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.' . $this->toUserId)];
    }

    public function broadcastAs(): string
    {
        return 'call.peer-offer';
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'from_user_id' => $this->fromUserId,
            'offer' => $this->offer,
        ];
    }
}
