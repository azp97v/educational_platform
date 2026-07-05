<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['lesson_id', 'user_id']);
            $table->index(['lesson_id']);
        });

        Schema::create('lesson_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('user_id');
            $table->text('text');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['lesson_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_notes');
        Schema::dropIfExists('lesson_ratings');
    }
};
