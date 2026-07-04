<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->float('media_pos_x')->nullable()->after('filter_style');
            $table->float('media_pos_y')->nullable()->after('media_pos_x');
            $table->float('media_scale')->nullable()->default(1)->after('media_pos_y');
            $table->float('media_rotate')->nullable()->default(0)->after('media_scale');
        });
    }

    public function down(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->dropColumn(['media_pos_x', 'media_pos_y', 'media_scale', 'media_rotate']);
        });
    }
};
