<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return $course->status === 'published' || $user->id === $course->instructor_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'teacher' || $user->role === 'admin';
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->role === 'admin';
    }

    public function restore(User $user, Course $course): bool
    {
        return false;
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return false;
    }
}
