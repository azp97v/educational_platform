<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('certificate_student_id')
                  ->constrained('courses')->nullOnDelete();
            $table->string('course_name')->nullable()->after('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['course_id', 'course_name']);
        });
    }
};
