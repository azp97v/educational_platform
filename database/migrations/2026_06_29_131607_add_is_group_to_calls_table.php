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
        Schema::table('calls', function (Blueprint $table) {
            $table->boolean('is_group')->default(false)->after('type');
            $table->unsignedInteger('max_participants')->default(5)->after('is_group');
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->foreignId('recipient_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropColumn(['is_group', 'max_participants']);
        });
    }
};
