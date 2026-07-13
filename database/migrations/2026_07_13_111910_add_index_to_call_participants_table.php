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
        Schema::table('call_participants', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'cp_user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('call_participants', function (Blueprint $table) {
            $table->dropIndex('cp_user_status_idx');
        });
    }
};
