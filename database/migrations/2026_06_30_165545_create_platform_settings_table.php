<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->string('group', 50)->default('general');
            $table->string('label', 200)->nullable();
            $table->timestamps();
        });

        DB::table('platform_settings')->insert([
            ['key' => 'platform_name', 'value' => 'منصة إجلال التعليمية', 'type' => 'string', 'group' => 'general', 'label' => 'اسم المنصة'],
            ['key' => 'timezone', 'value' => 'Asia/Riyadh', 'type' => 'string', 'group' => 'general', 'label' => 'المنطقة الزمنية'],
            ['key' => 'locale', 'value' => 'ar', 'type' => 'string', 'group' => 'general', 'label' => 'اللغة الافتراضية'],
            ['key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'access', 'label' => 'التسجيل الذاتي'],
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'integer', 'group' => 'security', 'label' => 'مهلة الجلسة (دقائق)'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'security', 'label' => 'حد محاولات الدخول'],
            ['key' => 'smart_rewind_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'label' => 'Smart Rewind'],
            ['key' => 'certificates_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'label' => 'الشهادات'],
            ['key' => 'gamification_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'features', 'label' => 'عناصر اللعب'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
