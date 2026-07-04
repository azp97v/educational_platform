<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $touched = [];

    public function up(): void
    {
        $this->safeIndex('users', 'role', 'idx_users_role');
        $this->safeIndex('users', 'teacher_id', 'idx_users_teacher_id');
        $this->safeIndex('users', 'status', 'idx_users_status');

        $this->safeIndex('courses', 'status', 'idx_courses_status');
        $this->safeIndex('courses', 'deleted_at', 'idx_courses_deleted_at');

        $this->safeIndex('lessons', ['course_id', 'order'], 'idx_lessons_course_id_order');

        $this->safeIndex('exams', 'is_published', 'idx_exams_is_published');
        $this->safeIndex('exams', 'lesson_id', 'idx_exams_lesson_id');

        $this->safeIndex('questions', ['exam_id', 'order'], 'idx_questions_exam_id_order');

        $this->safeIndex('answers', ['question_id', 'is_correct'], 'idx_answers_question_id_is_correct');

        $this->safeIndex('user_progress', 'status', 'idx_user_progress_status');
        $this->safeIndex('user_progress', ['lesson_id', 'user_id', 'status'], 'idx_user_progress_lesson_user_status');

        $this->safeIndex('course_enrollments', ['course_id', 'status'], 'idx_course_enrollments_course_status');
        $this->safeIndex('course_enrollments', 'status', 'idx_course_enrollments_status');

        $this->safeIndex('smart_rewinds', 'exam_id', 'idx_smart_rewinds_exam_id');
        $this->safeIndex('smart_rewinds', ['user_id', 'status'], 'idx_smart_rewinds_user_status');

        $this->safeIndex('student_inquiries', 'course_id', 'idx_student_inquiries_course_id');
        $this->safeIndex('student_inquiries', 'teacher_id', 'idx_student_inquiries_teacher_id');
        $this->safeIndex('student_inquiries', 'student_id', 'idx_student_inquiries_student_id');

        $this->safeIndex('student_questions', 'course_id', 'idx_student_questions_course_id');
        $this->safeIndex('student_questions', 'student_id', 'idx_student_questions_student_id');
        $this->safeIndex('student_questions', 'teacher_id', 'idx_student_questions_teacher_id');

        $this->safeIndex('certificate_students', 'user_id', 'idx_certificate_students_user_id');
        $this->safeIndex('certificate_students', 'recipient_user_id', 'idx_certificate_students_recipient_user_id');

        $this->safeIndex('custom_templates', 'user_id', 'idx_custom_templates_user_id');

        $this->safeIndex('message_folders', 'user_id', 'idx_message_folders_user_id');

        $this->safeIndex('groups', 'created_by', 'idx_groups_created_by');

        $this->safeIndex('notifications', 'read_at', 'idx_notifications_read_at');
    }

    public function down(): void
    {
        foreach (array_reverse($this->touched) as $name) {
            $this->safeDropIndex($name);
        }
    }

    private function safeIndex(string $table, array|string $columns, string $name): void
    {
        try {
            if (!$this->indexExists($table, $name)) {
                Schema::table($table, fn(Blueprint $t) => $t->index((array) $columns, $name));
                $this->touched[] = $name;
            }
        } catch (\Throwable $e) {
            // Table may not exist or column may be missing — skip gracefully
        }
    }

    private function safeDropIndex(string $name): void
    {
        try {
            Schema::table($this->tableForIndex($name), fn(Blueprint $t) => $t->dropIndex($name));
        } catch (\Throwable) {
        }
    }

    private function indexExists(string $table, string $name): bool
    {
        try {
            if (DB::connection()->getDriverName() === 'sqlite') {
                $indexes = DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ?", [$table]);
                return collect($indexes)->pluck('name')->contains($name);
            }
            $rows = DB::select('SHOW INDEX FROM ' . $table);
            return collect($rows)->pluck('Key_name')->contains($name);
        } catch (\Throwable) {
            return false;
        }
    }

    private function tableForIndex(string $name): string
    {
        $map = [
            'idx_users_role' => 'users', 'idx_users_teacher_id' => 'users', 'idx_users_status' => 'users',
            'idx_courses_status' => 'courses', 'idx_courses_deleted_at' => 'courses',
            'idx_lessons_course_id_order' => 'lessons',
            'idx_exams_is_published' => 'exams', 'idx_exams_lesson_id' => 'exams',
            'idx_questions_exam_id_order' => 'questions',
            'idx_answers_question_id_is_correct' => 'answers',
            'idx_user_progress_status' => 'user_progress', 'idx_user_progress_lesson_user_status' => 'user_progress',
            'idx_course_enrollments_course_status' => 'course_enrollments', 'idx_course_enrollments_status' => 'course_enrollments',
            'idx_smart_rewinds_exam_id' => 'smart_rewinds', 'idx_smart_rewinds_user_status' => 'smart_rewinds',
            'idx_student_inquiries_course_id' => 'student_inquiries', 'idx_student_inquiries_teacher_id' => 'student_inquiries', 'idx_student_inquiries_student_id' => 'student_inquiries',
            'idx_student_questions_course_id' => 'student_questions', 'idx_student_questions_student_id' => 'student_questions', 'idx_student_questions_teacher_id' => 'student_questions',
            'idx_certificate_students_user_id' => 'certificate_students', 'idx_certificate_students_recipient_user_id' => 'certificate_students',
            'idx_custom_templates_user_id' => 'custom_templates',
            'idx_message_folders_user_id' => 'message_folders',
            'idx_groups_created_by' => 'groups',
            'idx_notifications_read_at' => 'notifications',
        ];
        return $map[$name] ?? 'users';
    }
};
