<?php

namespace App\Policies;

use App\Models\SmartRewind;
use App\Models\User;

class SmartRewindPolicy
{
    /**
     * التحقق من أن الطالب يملك هذا الـ Rewind
     */
    public function view(User $user, SmartRewind $rewind): bool
    {
        return $user->id === $rewind->user_id;
    }

    /**
     * التحقق من صلاحية المعلم برؤية Rewinds طلابه
     */
    public function viewForCourse(User $user, SmartRewind $rewind): bool
    {
        // المعلم يمكنه رؤية Rewinds الطلاب في مساراته
        return $rewind->exam->lesson->course->instructor_id === $user->id;
    }
}
