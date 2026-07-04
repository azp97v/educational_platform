<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
                $table->string('phone')->nullable()->after('role');
                $table->text('bio')->nullable()->after('phone');
                $table->string('avatar_url')->nullable()->after('bio');
                $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('avatar_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn(['role', 'phone', 'bio', 'avatar_url', 'status']);
            }
        });
    }
};
