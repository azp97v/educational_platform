<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')
                  ->constrained('categories')->nullOnDelete();
        });

        // Map existing string category values to the new category_id
        $map = DB::table('categories')->pluck('id', 'slug');
        DB::table('courses')->whereNotNull('category')->get()->each(function ($course) use ($map) {
            if (isset($map[$course->category])) {
                DB::table('courses')->where('id', $course->id)
                  ->update(['category_id' => $map[$course->category]]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
