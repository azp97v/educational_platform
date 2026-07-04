<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('audio_file')->nullable()->after('video_url');
            $table->string('video_file')->nullable()->after('audio_file');
            $table->longText('content')->nullable()->after('video_file');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['audio_file', 'video_file', 'content']);
        });
    }
};
