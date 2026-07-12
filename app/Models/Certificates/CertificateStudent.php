<?php

namespace App\Models\Certificates;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateStudent extends Model
{
    protected $table = 'certificate_students';

    protected $fillable = [
        'user_id', 'recipient_user_id', 'name', 'username', 'email', 'course', 'course_date', 'degree', 'image',
    ];

    protected $casts = [
        'course_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function customTemplates(): HasMany
    {
        return $this->hasMany(CustomTemplate::class, 'certificate_student_id');
    }
}
