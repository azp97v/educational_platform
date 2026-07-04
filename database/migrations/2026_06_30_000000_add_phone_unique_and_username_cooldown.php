<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'username_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('username_changed_at')->nullable()->after('username');
            });
        }

        if (Schema::hasTable('users') && !$this->hasPhoneUniqueIndex()) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('phone');
                });
            } catch (\Throwable $e) {
                // index may already exist (SQLite)
            }
        }
    }

    /**
     * Check if phone already has a unique index (MySQL-only; SQLite skips).
     */
    protected function hasPhoneUniqueIndex(): bool
    {
        try {
            return collect(\Illuminate\Support\Facades\DB::select('SHOW INDEX FROM users'))
                ->contains(fn ($i) => $i->Column_name === 'phone' && (int) $i->Non_unique === 0);
        } catch (\Throwable $e) {
            return false; // SQLite – assume no unique index so we try
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'username_changed_at')) {
                    $table->dropColumn('username_changed_at');
                }
            });

            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique(['phone']);
                });
            } catch (\Throwable $e) {
            }
        }
    }
};
