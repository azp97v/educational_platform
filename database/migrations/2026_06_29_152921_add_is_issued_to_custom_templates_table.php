<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->boolean('is_issued')->default(false)->after('show_stamp');
            $table->timestamp('issued_at')->nullable()->after('is_issued');
        });
    }

    public function down(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->dropColumn(['is_issued', 'issued_at']);
        });
    }
};
