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
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('attachment_kind', 32)->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('audio_path')->nullable();
            $table->integer('audio_duration')->nullable();
            $table->enum('message_type', ['text', 'file', 'audio'])->default('text');
            $table->boolean('is_edited')->default(false);
            $table->unsignedBigInteger('reply_to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_messages');
    }
};
