<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->string('bg_color', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->string('bg_color', 80)->nullable()->change();
        });
    }
};
