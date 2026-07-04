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
        Schema::table('users', function (Blueprint $table) {
            // Add teacher_id column for students to link them to their teacher
            // Only add if it doesn't already exist
            if (!Schema::hasColumn('users', 'teacher_id')) {
                $table->foreignId('teacher_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};
