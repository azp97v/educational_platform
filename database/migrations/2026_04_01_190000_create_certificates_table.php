<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('certificate_number')->unique(); // رقم فريد للشهادة
            $table->string('qr_code')->nullable(); // رمز QR
            $table->decimal('score', 5, 2)->default(0); // النسبة النهائية
            $table->timestamp('issued_at')->useCurrent(); // تاريخ الإصدار
            $table->timestamp('expires_at')->nullable(); // تاريخ الانتهاء (اختياري)
            $table->string('pdf_url')->nullable(); // رابط ملف PDF
            $table->boolean('is_verified')->default(true); // هل تم التحقق من الشهادة
            $table->timestamps();
            $table->index('user_id');
            $table->index('course_id');
            $table->index('certificate_number');
        });

        Schema::create('certificate_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('verification_token')->unique(); // رمز التحقق
            $table->foreignId('certificate_id')->constrained('certificates')->onDelete('cascade');
            $table->timestamp('verified_at')->nullable();
            $table->integer('verification_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_verifications');
        Schema::dropIfExists('certificates');
    }
};
