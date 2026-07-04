<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageForward extends Model
{
    protected $fillable = ['source_message_id', 'forwarded_message_id', 'forwarded_by'];
}
