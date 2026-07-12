<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('certificate_student_id')->nullable()->after('user_id');
            $table->foreign('certificate_student_id')
                  ->references('id')
                  ->on('certificate_students')
                  ->nullOnDelete();
        });

        // Backfill: match existing templates to students via recipient_name + user_id
        $templates = DB::table('custom_templates')
            ->whereNull('certificate_student_id')
            ->whereNotNull('recipient_name')
            ->get(['id', 'user_id', 'recipient_name']);

        foreach ($templates as $tpl) {
            $student = DB::table('certificate_students')
                ->where('user_id', $tpl->user_id)
                ->where('name', $tpl->recipient_name)
                ->orderBy('id')
                ->first(['id']);

            if ($student) {
                DB::table('custom_templates')
                    ->where('id', $tpl->id)
                    ->update(['certificate_student_id' => $student->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('custom_templates', function (Blueprint $table) {
            $table->dropForeign(['certificate_student_id']);
            $table->dropColumn('certificate_student_id');
        });
    }
};
