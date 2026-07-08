<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('category')->nullable()->after('image_url');
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->nullable()->after('category');
            $table->enum('duration_unit', ['hours', 'days', 'months'])->default('hours')->after('duration');
            $table->unsignedSmallInteger('max_students')->nullable()->after('duration_unit');
            $table->date('start_date')->nullable()->after('max_students');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['category', 'level', 'duration_unit', 'max_students', 'start_date', 'end_date']);
        });
    }
};
