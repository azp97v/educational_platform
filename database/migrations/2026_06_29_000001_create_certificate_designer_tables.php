<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('course');
            $table->date('course_date');
            $table->string('degree');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('custom_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('recipient_name')->nullable();
            $table->string('title')->default('شهادة إتمام');
            $table->string('subtitle')->default('تقديراً لجهودكم');
            $table->text('body_text')->nullable();
            $table->string('primary_color', 7)->default('#4338ca');
            $table->string('secondary_color', 7)->default('#14b8a6');
            $table->string('accent_color', 7)->default('#f59e0b');
            $table->string('background_type')->default('gradient');
            $table->string('background_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('font_family')->default('Cairo');
            $table->string('text_align')->default('center');
            $table->integer('title_x')->default(0);
            $table->integer('title_y')->default(0);
            $table->integer('title_size')->default(38);
            $table->integer('title_rotation')->default(0);
            $table->integer('subtitle_x')->default(0);
            $table->integer('subtitle_y')->default(0);
            $table->integer('subtitle_size')->default(20);
            $table->integer('subtitle_rotation')->default(0);
            $table->integer('name_x')->default(0);
            $table->integer('name_y')->default(0);
            $table->integer('name_size')->default(32);
            $table->integer('name_rotation')->default(0);
            $table->integer('body_x')->default(0);
            $table->integer('body_y')->default(0);
            $table->integer('body_size')->default(18);
            $table->integer('body_rotation')->default(0);
            $table->integer('logo_x')->default(0);
            $table->integer('logo_y')->default(0);
            $table->integer('logo_width')->default(110);
            $table->integer('logo_rotation')->default(0);
            $table->integer('stamp_size')->default(120);
            $table->integer('overlay_opacity')->default(15);
            $table->integer('border_radius')->default(30);
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_stamp')->default(true);
            $table->string('title_color')->default('#ffffff');
            $table->string('subtitle_color')->default('#ffffff');
            $table->string('name_color')->default('#ffffff');
            $table->string('body_color')->default('#ffffff');
            $table->integer('background_position_x')->default(50);
            $table->integer('background_position_y')->default(50);
            $table->integer('background_size')->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_templates');
        Schema::dropIfExists('certificate_students');
    }
};
