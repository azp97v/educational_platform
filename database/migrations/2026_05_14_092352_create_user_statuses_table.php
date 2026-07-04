<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video', 'text'])->default('text');
            $table->string('content_url')->nullable();   // مسار الصورة أو الفيديو
            $table->string('audio_url')->nullable();     // موسيقى خلفية اختيارية
            $table->text('text_content')->nullable();    // النص المكتوب فوق الحالة
            $table->string('text_color', 20)->default('#ffffff');
            $table->string('font_style', 60)->default('Tajawal'); // الخط المختار
            $table->integer('font_size')->default(24);
            $table->string('bg_color', 80)->nullable();  // تدرج أو لون خلفية
            $table->string('filter_style', 40)->nullable(); // warm/cool/bw/soft
            $table->integer('duration_hours')->default(24); // 24 / 72 / 168
            $table->timestamp('expires_at')->nullable();
            $table->enum('privacy_type', ['all', 'contacts', 'selected', 'except'])->default('all');
            $table->json('privacy_user_ids')->nullable(); // معرفات المستخدمين المُختارين/المستثنين
            $table->unsignedBigInteger('views_count')->default(0);
            $table->boolean('allow_reply')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_statuses');
    }
};
