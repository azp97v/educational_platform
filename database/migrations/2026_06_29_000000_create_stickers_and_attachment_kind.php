<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stickers')) {
            Schema::create('stickers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->enum('type', ['static', 'animated']);
                $table->string('file_path');
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
            });
        }

        if (Schema::hasTable('messages') && !Schema::hasColumn('messages', 'attachment_kind')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('attachment_kind', 32)->nullable()->after('attachment_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('messages') && Schema::hasColumn('messages', 'attachment_kind')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('attachment_kind');
            });
        }

        Schema::dropIfExists('stickers');
    }
};
