<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الإنجاز
            $table->text('description'); // وصف الإنجاز
            $table->string('badge_icon'); // أيقونة الشارة
            $table->enum('type', ['points', 'exams_passed', 'consecutive_days', 'smart_rewind_mastered']); // نوع الإنجاز
            $table->integer('requirement'); // المتطلب (عدد النقاط، عدد الاختبارات، إلخ)
            $table->integer('reward_points')->default(50); // النقاط المكافآت
            $table->timestamps();
            $table->index('type');
        });

        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('achievements')->onDelete('cascade');
            $table->timestamp('achieved_at')->useCurrent();
            $table->timestamps();
            $table->unique(['user_id', 'achievement_id']);
            $table->index('user_id');
        });

        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_points')->default(0); // إجمالي النقاط
            $table->integer('exams_passed')->default(0); // عدد الاختبارات المنجزة
            $table->integer('consecutive_days')->default(0); // أيام متتالية
            $table->integer('smart_rewinds_mastered')->default(0); // عدد Smart Rewinds المتقنة
            $table->integer('rank')->default(0); // الترتيب
            $table->timestamp('last_activity_at')->nullable(); // آخر نشاط
            $table->timestamps();
            $table->unique('user_id');
            $table->index('total_points');
            $table->index('rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('leaderboards');
    }
};
