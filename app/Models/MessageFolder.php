<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageFolder extends Model
{
    protected $fillable = ['user_id', 'name', 'icon', 'include_ids', 'exclude_ids', 'position'];

    protected $casts = [
        'include_ids' => 'array',
        'exclude_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
