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
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('answer_id')->nullable(); // للأسئلة متعددة الخيارات
            $table->longText('answer_text')->nullable(); // للإجابات القصيرة
            $table->boolean('is_marked')->default(false); // هل تم تقييمها؟
            $table->integer('score')->default(0); // الدرجة
            $table->text('teacher_feedback')->nullable(); // تعليق المعلم
            $table->timestamps();
            
            // الفهارس
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('set null');
            
            $table->index(['exam_id', 'user_id']);
            $table->index(['question_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
