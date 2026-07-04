<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEncryptionKey extends Model
{
    protected $fillable = ['user_id', 'public_key', 'rotated_at'];

    protected $casts = ['rotated_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
