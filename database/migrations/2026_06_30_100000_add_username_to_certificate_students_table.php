<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificate_students')) {
            return;
        }
        Schema::table('certificate_students', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('certificate_students')) {
            return;
        }
        Schema::table('certificate_students', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
