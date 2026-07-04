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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false)->after('is_sensitive');
            }
            if (!Schema::hasColumn('messages', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_edited');
            }
            if (!Schema::hasColumn('messages', 'pinned_at')) {
                $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            }
            if (!Schema::hasColumn('messages', 'pinned_by')) {
                $table->unsignedBigInteger('pinned_by')->nullable()->after('pinned_at');
                $table->foreign('pinned_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('messages', 'pinned_by')) {
                $table->dropForeign(['pinned_by']);
                $cols[] = 'pinned_by';
            }
            foreach (['is_edited', 'is_pinned', 'pinned_at'] as $col) {
                if (Schema::hasColumn('messages', $col)) $cols[] = $col;
            }
            if ($cols) $table->dropColumn($cols);
        });
    }
};
