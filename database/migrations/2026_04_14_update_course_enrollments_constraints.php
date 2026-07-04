<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تحديث قيود الحذف في جدول course_enrollments
     * من CASCADE إلى RESTRICT
     */
    public function up(): void
    {
        // حذف الجدول القديم
        Schema::dropIfExists('course_enrollments');

        // إعادة إنشاؤه مع القيود الصحيحة
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('course_id')->constrained('courses')->onDelete('restrict');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('enrolled_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unique(['user_id', 'course_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
