<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // messages — zero indexes on the hottest table in the system
        // conversation lookup: (sender→recipient OR recipient→sender) ORDER BY created_at
        // unread count:        WHERE recipient_id = X AND read_at IS NULL
        // soft-delete filter:  WHERE deleted_at IS NULL (almost every query)
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'recipient_id', 'created_at'], 'msg_conversation_idx');
            $table->index(['recipient_id', 'read_at'],                  'msg_unread_idx');
            $table->index(['deleted_at', 'created_at'],                 'msg_deleted_created_idx');
        });

        // courses — teacher_id is the primary filter on every teacher page load
        Schema::table('courses', function (Blueprint $table) {
            $table->index(['teacher_id', 'created_at'], 'courses_teacher_created_idx');
        });

        // lessons — course_id + order: listing lessons is done in every course view
        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['course_id', 'order'], 'lessons_course_order_idx');
        });

        // course_enrollments — existing unique(user_id, course_id) is covered;
        // add (course_id, status) for teacher queries aggregating per-course counts
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->index(['course_id', 'status'], 'enrollments_course_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('msg_conversation_idx');
            $table->dropIndex('msg_unread_idx');
            $table->dropIndex('msg_deleted_created_idx');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_teacher_created_idx');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_course_order_idx');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_course_status_idx');
        });
    }
};
