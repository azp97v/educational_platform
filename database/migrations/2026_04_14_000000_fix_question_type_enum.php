<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update the question_type enum to include 'short_answer' instead of 'essay'
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer'])
                ->default('multiple_choice')
                ->change();
        });
    }

    public function down(): void
    {
        // Revert to original enum
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('question_type', ['multiple_choice', 'true_false', 'essay'])
                ->default('multiple_choice')
                ->change();
        });
    }
};
