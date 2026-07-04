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
        Schema::create('student_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->longText('question_text');
            $table->longText('answer_text')->nullable();
            $table->enum('status', ['pending', 'answered', 'closed'])->default('pending');
            $table->integer('priority')->default(0); // 0: normal, 1: high, -1: low
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index(['lesson_id', 'student_id']);
            $table->index(['course_id', 'teacher_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_questions');
    }
};
