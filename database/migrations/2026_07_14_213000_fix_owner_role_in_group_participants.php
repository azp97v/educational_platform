<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('group_participants')
            ->where('role', 'owner')
            ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        // irreversible normalisation — 'owner' was a legacy value
    }
};
