<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            if (DB::getDriverName() === 'sqlite') {
                $rows = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=?", [$table]);
                return collect($rows)->pluck('name')->contains($indexName);
            }
            $rows = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($rows);
        } catch (\Throwable) {
            return false;
        }
    }

    private function safeAddIndex(string $table, array|string $columns, string $name): void
    {
        if ($this->indexExists($table, $name)) return;
        try {
            Schema::table($table, fn(Blueprint $t) => $t->index((array) $columns, $name));
        } catch (\Throwable) {}
    }

    public function up(): void
    {
        $this->safeAddIndex('messages', ['sender_id', 'recipient_id', 'created_at'], 'msg_conversation_idx');
        $this->safeAddIndex('messages', ['recipient_id', 'read_at'], 'msg_unread_idx');
        $this->safeAddIndex('messages', ['deleted_at', 'created_at'], 'msg_deleted_created_idx');
        $this->safeAddIndex('courses', ['instructor_id', 'created_at'], 'courses_instructor_created_idx');
        $this->safeAddIndex('lessons', ['course_id', 'order'], 'lessons_course_order_idx');
        $this->safeAddIndex('course_enrollments', ['course_id', 'status'], 'enrollments_course_status_idx');
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndexIfExists('msg_conversation_idx');
            $table->dropIndexIfExists('msg_unread_idx');
            $table->dropIndexIfExists('msg_deleted_created_idx');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndexIfExists('courses_instructor_created_idx');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndexIfExists('lessons_course_order_idx');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropIndexIfExists('enrollments_course_status_idx');
        });
    }
};
