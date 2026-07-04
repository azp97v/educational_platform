<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Course extends Model
{
    use SoftDeletes;

    /**
     * ✅ معالجة soft deletes في route model binding
     * يضمن عدم تحميل المسارات المحذوفة في المسارات
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    /**
     * ✅ استبعاد المسارات المحذوفة من الاستعلامات الافتراضية
     * عندما لا يتم استخدام withTrashed()
     */
    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            // لا نطبق هنا لأننا نريد استخدام withTrashed() حيث لزم الأمر
            // لكن نتأكد من استخدام whereNull('deleted_at') في الاستعلامات المهمة
        });
    }

    protected $fillable = [
        'instructor_id',
        'name',
        'description',
        'image_url',
        'duration',
        'status',
    ];

    // العلاقات
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Lesson::class);
    }

    /**
     * طلبات الالتحاق (جميع الحالات)
     */
    public function enrollmentRequests()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * الطلاب المسجلين الفعليين
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'user_id')
            ->where('course_enrollments.status', 'approved')
            ->withTimestamps();
    }

    /**
     * طلبات الالتحاق المعلقة
     */
    public function pendingEnrollments()
    {
        return $this->enrollmentRequests()->pending();
    }

    /**
     * عدد الطلاب المسجلين
     */
    public function studentCount()
    {
        return $this->enrolledStudents()->count();
    }
}
