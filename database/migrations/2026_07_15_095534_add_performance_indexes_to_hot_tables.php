<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // messages — indexes already applied by partial run; guard with existence check
        $msgIndexes = DB::select("SHOW INDEX FROM messages WHERE Key_name IN ('msg_conversation_idx','msg_unread_idx','msg_deleted_created_idx')");
        $existingMsgKeys = array_column($msgIndexes, 'Key_name');

        Schema::table('messages', function (Blueprint $table) use ($existingMsgKeys) {
            if (!in_array('msg_conversation_idx', $existingMsgKeys)) {
                $table->index(['sender_id', 'recipient_id', 'created_at'], 'msg_conversation_idx');
            }
            if (!in_array('msg_unread_idx', $existingMsgKeys)) {
                $table->index(['recipient_id', 'read_at'], 'msg_unread_idx');
            }
            if (!in_array('msg_deleted_created_idx', $existingMsgKeys)) {
                $table->index(['deleted_at', 'created_at'], 'msg_deleted_created_idx');
            }
        });

        // courses — instructor_id is the correct column name
        $courseIndexes = DB::select("SHOW INDEX FROM courses WHERE Key_name = 'courses_instructor_created_idx'");
        if (empty($courseIndexes)) {
            Schema::table('courses', function (Blueprint $table) {
                $table->index(['instructor_id', 'created_at'], 'courses_instructor_created_idx');
            });
        }

        // lessons — course_id + order: every course listing sorts by this
        $lessonIndexes = DB::select("SHOW INDEX FROM lessons WHERE Key_name = 'lessons_course_order_idx'");
        if (empty($lessonIndexes)) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->index(['course_id', 'order'], 'lessons_course_order_idx');
            });
        }

        // course_enrollments — (course_id, status) for teacher aggregate queries
        $enrollIndexes = DB::select("SHOW INDEX FROM course_enrollments WHERE Key_name = 'enrollments_course_status_idx'");
        if (empty($enrollIndexes)) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                $table->index(['course_id', 'status'], 'enrollments_course_status_idx');
            });
        }
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
