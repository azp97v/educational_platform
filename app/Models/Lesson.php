<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'lesson_type',
        'name',
        'description',
        'video_url',
        'audio_file',
        'video_file',
        'content',
        'duration',
        'order',
    ];

    // العلاقات
    public function getTitleAttribute()
    {
        return $this->attributes['name'] ?? null;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function userProgress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function inquiries()
    {
        return $this->hasMany(StudentInquiry::class);
    }
}
