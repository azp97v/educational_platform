<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_wallpapers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_a_id');
            $table->unsignedBigInteger('user_b_id');
            $table->string('wallpaper_key', 60)->nullable();   // e.g. 'wp_midnight', 'wp_ocean'
            $table->string('custom_gradient', 255)->nullable(); // raw CSS gradient
            $table->timestamps();

            $table->foreign('user_a_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_b_id')->references('id')->on('users')->onDelete('cascade');

            // Canonical pair: always store with user_a_id < user_b_id
            $table->unique(['user_a_id', 'user_b_id']);
            $table->index('user_a_id');
            $table->index('user_b_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_wallpapers');
    }
};
