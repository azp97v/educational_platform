<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInquiry extends Model
{
    protected $fillable = [
        'lesson_id',
        'course_id',
        'student_id',
        'teacher_id',
        'inquiry_type',
        'question_text',
        'answer_text',
        'status', // pending, answered, closed
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // العلاقات
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    public function scopeForLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }
}
