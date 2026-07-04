<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'attempts',
        'expires_at',
        'last_sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    /**
     * Check if OTP has expired
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if can resend (1 minute for first request, then 3 minutes)
     */
    public function canResend(): bool
    {
        if (!$this->last_sent_at) {
            return true;  // First time, can send immediately
        }

        $minutesPassed = now()->diffInMinutes($this->last_sent_at);
        $created = $this->created_at;
        
        // If this is the very first send (created_at within last 2 minutes), wait 1 minute
        if ($created && now()->diffInMinutes($created) < 2) {
            return $minutesPassed >= 1;
        }
        
        // Else wait 3 minutes for subsequent resends
        return $minutesPassed >= 3;
    }

    /**
     * Get seconds remaining until resend allowed
     */
    public function getResendWaitTime(): int
    {
        if (!$this->last_sent_at || $this->canResend()) {
            return 0;
        }

        $minutesPassed = now()->diffInMinutes($this->last_sent_at);
        $created = $this->created_at;
        
        // First request: 1-minute cooldown
        if ($created && now()->diffInMinutes($created) < 2) {
            return max(0, 60 - now()->diffInSeconds($this->last_sent_at));
        }
        
        // Subsequent requests: 3-minute cooldown
        return max(0, 180 - now()->diffInSeconds($this->last_sent_at));
    }

    /**
     * Get minutes and seconds remaining for OTP validity
     */
    public function getExpiresInMinutes(): int
    {
        return max(0, (int)now()->diffInMinutes($this->expires_at, false));
    }

    /**
     * Scope: Find by email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email)->latest()->first();
    }

    /**
     * Scope: Only non-expired OTPs
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
