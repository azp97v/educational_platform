<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'content',
        'attachment_path',
        'attachment_type',
        'attachment_kind',
        'attachment_name',
        'read_at',
        'reply_to',
        'forwarded_from_message_id',
        'audio_position',
        'is_sensitive',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'audio_position' => 'float',
        'is_sensitive' => 'bool',
    ];

    protected $appends = [
        'is_edited',
        'formatted_time',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'reply_to');
    }

    public function forwardedFrom(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'forwarded_from_message_id');
    }

    public static function between(User $userA, User $userB)
    {
        return self::where(function ($query) use ($userA, $userB) {
            $query->where('sender_id', $userA->id)
                ->where('recipient_id', $userB->id);
        })->orWhere(function ($query) use ($userA, $userB) {
            $query->where('sender_id', $userB->id)
                ->where('recipient_id', $userA->id);
        });
    }

    public function getIsEditedAttribute(): bool
    {
        if (!$this->updated_at || !$this->created_at) {
            return false;
        }

        // Ignore read-receipt updates; those naturally bump updated_at.
        if ($this->read_at && $this->updated_at->equalTo($this->read_at)) {
            return false;
        }

        // Avoid false positives from sub-second timestamp differences on insert.
        return $this->updated_at->greaterThan($this->created_at->copy()->addSecond());
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('H:i');
    }
}
