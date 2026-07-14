<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'who_can_send')) {
                $table->string('who_can_send', 10)->default('all')->after('only_admins_can_message');
            }
            if (!Schema::hasColumn('groups', 'who_can_add_members')) {
                $table->string('who_can_add_members', 10)->default('admins')->after('who_can_send');
            }
            if (!Schema::hasColumn('groups', 'who_can_edit_info')) {
                $table->string('who_can_edit_info', 10)->default('admins')->after('who_can_add_members');
            }
        });

        // Sync legacy only_admins_can_message → who_can_send
        DB::table('groups')->where('only_admins_can_message', true)->update(['who_can_send' => 'admins']);
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            foreach (['who_can_send', 'who_can_add_members', 'who_can_edit_info'] as $col) {
                if (Schema::hasColumn('groups', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
