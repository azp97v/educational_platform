<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessagingSetting extends Model
{
    protected $fillable = ['user_id', 'privacy', 'notifications', 'media', 'security', 'chats'];

    protected $casts = [
        'privacy' => 'array',
        'notifications' => 'array',
        'media' => 'array',
        'security' => 'array',
        'chats' => 'array',
    ];
}
