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
            $table->enum('inquiry_type', ['question', 'note'])->default('question')->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_inquiries', function (Blueprint $table) {
            $table->dropColumn('inquiry_type');
        });
    }
};
