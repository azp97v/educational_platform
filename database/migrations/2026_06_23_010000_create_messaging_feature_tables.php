<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('status_replies')) {
            Schema::create('status_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('status_id')->constrained('user_statuses')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('message_id')->nullable()->constrained('messages')->nullOnDelete();
                $table->text('content');
                $table->timestamps();

                $table->index(['status_id', 'created_at']);
                $table->index(['user_id', 'created_at']);
            });
        }

        if (!Schema::hasTable('status_reactions')) {
            Schema::create('status_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('status_id')->constrained('user_statuses')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('emoji', 16);
                $table->timestamps();

                $table->unique(['status_id', 'user_id']);
                $table->index(['status_id', 'updated_at']);
            });
        }

        if (!Schema::hasTable('pinned_messages')) {
            Schema::create('pinned_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
                $table->foreignId('pinned_by')->constrained('users')->cascadeOnDelete();
                $table->unsignedBigInteger('user_a_id');
                $table->unsignedBigInteger('user_b_id');
                $table->timestamp('pinned_at')->useCurrent();
                $table->timestamps();

                $table->foreign('user_a_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('user_b_id')->references('id')->on('users')->cascadeOnDelete();
                $table->unique(['message_id', 'user_a_id', 'user_b_id']);
                $table->index(['user_a_id', 'user_b_id', 'pinned_at']);
            });
        }

        if (!Schema::hasTable('message_forwards')) {
            Schema::create('message_forwards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_message_id')->constrained('messages')->cascadeOnDelete();
                $table->foreignId('forwarded_message_id')->constrained('messages')->cascadeOnDelete();
                $table->foreignId('forwarded_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique('forwarded_message_id');
                $table->index(['source_message_id', 'forwarded_by']);
            });
        }

        if (!Schema::hasTable('user_messaging_settings')) {
            Schema::create('user_messaging_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->json('privacy')->nullable();
                $table->json('notifications')->nullable();
                $table->json('media')->nullable();
                $table->json('security')->nullable();
                $table->json('chats')->nullable();
                $table->timestamps();

                $table->unique('user_id');
            });
        }

        if (Schema::hasTable('messages') && !Schema::hasColumn('messages', 'forwarded_from_message_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->foreignId('forwarded_from_message_id')
                    ->nullable()
                    ->after('reply_to')
                    ->constrained('messages')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('messages') && Schema::hasColumn('messages', 'forwarded_from_message_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropForeign(['forwarded_from_message_id']);
                $table->dropColumn('forwarded_from_message_id');
            });
        }

        Schema::dropIfExists('user_messaging_settings');
        Schema::dropIfExists('message_forwards');
        Schema::dropIfExists('pinned_messages');
        Schema::dropIfExists('status_reactions');
        Schema::dropIfExists('status_replies');
    }
};
