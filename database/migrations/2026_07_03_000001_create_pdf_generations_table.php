<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_generations', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique(); // رمز فريد للتحقق
            $table->unsignedBigInteger('user_id');  // المستخدم الطالب
            $table->string('type', 30); // 'preset' أو 'custom'
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('template_num', 5)->nullable();
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->string('file_path')->nullable();  // المسار الكامل للملف المُولَّد
            $table->string('file_name')->nullable();  // اسم الملف للتنزيل
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->timestamp('expires_at')->nullable(); // للتنظيف التلقائي

            $table->index('token');
            $table->index(['user_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_generations');
    }
};
