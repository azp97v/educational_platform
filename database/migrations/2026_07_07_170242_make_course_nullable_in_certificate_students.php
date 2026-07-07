<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_students', function (Blueprint $table) {
            $table->string('course')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('certificate_students', function (Blueprint $table) {
            // Set empty string for any null values before making it required
            \DB::table('certificate_students')->whereNull('course')->update(['course' => '']);
            $table->string('course')->nullable(false)->change();
        });
    }
};
