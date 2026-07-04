<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinnedMessage extends Model
{
    protected $fillable = ['message_id', 'pinned_by', 'user_a_id', 'user_b_id', 'pinned_at'];

    protected $casts = ['pinned_at' => 'datetime'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
