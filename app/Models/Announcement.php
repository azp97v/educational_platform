<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'type', 'target_role',
        'is_active', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(fn($q) => $q->where('target_role', 'all')->orWhere('target_role', $role));
    }
}
