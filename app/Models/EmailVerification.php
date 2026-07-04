<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'email',
        'code',
        'attempts',
        'last_attempt_at',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];

    public function isExpired()
    {
        return now()->isAfter($this->expires_at);
    }

    public function canResendCode()
    {
        if (!$this->last_attempt_at) {
            return true;
        }

        return now()->diffInMinutes($this->last_attempt_at) >= 3;
    }

    public function getRemainingTime()
    {
        $minutes = now()->diffInMinutes($this->last_attempt_at, false);
        $remainingSeconds = (3 * 60) - ($minutes * 60);
        return max(0, ceil($remainingSeconds));
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
