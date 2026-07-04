<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->unsignedTinyInteger('text_pos_x')->default(50)->after('font_size');
            $table->unsignedTinyInteger('text_pos_y')->default(50)->after('text_pos_x');
            $table->smallInteger('text_rotate')->default(0)->after('text_pos_y');
            $table->string('text_bg_style', 20)->default('none')->after('text_rotate');
            $table->json('text_objects')->nullable()->after('text_bg_style');
        });
    }

    public function down(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->dropColumn(['text_pos_x', 'text_pos_y', 'text_rotate', 'text_bg_style', 'text_objects']);
        });
    }
};
