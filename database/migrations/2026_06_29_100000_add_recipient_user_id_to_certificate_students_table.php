<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificate_students') || Schema::hasColumn('certificate_students', 'recipient_user_id')) {
            return;
        }
        Schema::table('certificate_students', function (Blueprint $table) {
            $table->foreignId('recipient_user_id')->nullable()->after('user_id')
                ->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('certificate_students')) {
            return;
        }
        Schema::table('certificate_students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipient_user_id');
        });
    }
};
