<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read HasMany $courses
 * @property-read HasMany $progress
 * @property-read HasOne $streak
 * @property-read BelongsToMany $achievements
 * @property-read HasOne $leaderboard
 * @property-read HasMany $smartRewinds
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'username_changed_at',
        'email',
        'password',
        'role',
        'phone',
        'bio',
        'birthday',
        'locale',
        'avatar_url',
        'status',
        'points',
        'teacher_id',
        'email_verified_at',
        'notify_enrollment',
        'notify_inquiry',
        'notify_message',
        'notify_system',
        'notify_certificate',
        'notify_call',
        'auto_issue_certificates',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'username_changed_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'notify_enrollment' => 'bool',
            'notify_inquiry' => 'bool',
            'notify_message' => 'bool',
            'notify_system' => 'bool',
            'notify_certificate' => 'bool',
            'notify_call' => 'bool',
            'auto_issue_certificates' => 'bool',
        ];
    }

    // العلاقات
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function streak()
    {
        return $this->hasOne(Streak::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('achieved_at')
            ->withTimestamps();
    }

    public function leaderboard()
    {
        return $this->hasOne(Leaderboard::class);
    }

    public function smartRewinds()
    {
        return $this->hasMany(SmartRewind::class);
    }

    public function inquiries()
    {
        return $this->hasMany(StudentInquiry::class, 'student_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * الدورات المسجل بها الطالب (للطلاب فقط)
     */
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * الدورات المقبولة للطالب
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'user_id', 'course_id')
            ->where('course_enrollments.status', 'approved')
            ->withTimestamps();
    }

    /**
     * المعلم المسؤول عن الطالب (للطلاب فقط)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * جميع الطلاب التابعين للمعلم (للمعلمين فقط)
     */
    public function students()
    {
        return $this->hasMany(User::class, 'teacher_id');
    }

    /**
     * جهات الاتصال التي حظرها هذا المستخدم
     */
    public function blockedContacts()
    {
        return $this->hasMany(BlockedContact::class, 'blocker_id');
    }

    /**
     * المستخدمون الذين حظروا هذا المستخدم
     */
    public function blockedByContacts()
    {
        return $this->hasMany(BlockedContact::class, 'blocked_id');
    }

    /**
     * مجلدات المحادثات الخاصة بالمستخدم
     */
    public function messageFolders()
    {
        return $this->hasMany(MessageFolder::class, 'user_id')->orderBy('position');
    }

    /**
     * قوالب الشهادات المخصّصة التي رفعها المعلم
     */
    public function customTemplates()
    {
        return $this->hasMany(\App\Models\Certificates\CustomTemplate::class, 'user_id');
    }
}
