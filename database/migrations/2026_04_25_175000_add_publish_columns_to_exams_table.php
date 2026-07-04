<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('instructions');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });

        // Keep already-created exams visible, while new exams stay hidden until manually published.
        DB::table('exams')->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });
    }
};
