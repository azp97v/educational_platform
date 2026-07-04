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
        Schema::table('student_inquiries', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->after('lesson_id')
                ->constrained('courses')
                ->onDelete('cascade');
            
            // Add index for querying by course_id and status
            $table->index(['course_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_inquiries', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'status']);
            $table->dropForeignKey(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
