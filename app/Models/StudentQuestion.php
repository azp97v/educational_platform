<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentQuestion extends Model
{
    protected $fillable = [
        'lesson_id',
        'course_id',
        'student_id',
        'teacher_id',
        'title',
        'question_text',
        'answer_text',
        'status',
        'priority',
        'answered_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
