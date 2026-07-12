<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        // Seed initial categories matching the hardcoded list
        $categories = [
            ['slug' => 'quran',       'name' => 'القرآن الكريم والتجويد'],
            ['slug' => 'fiqh',        'name' => 'الفقه والشريعة'],
            ['slug' => 'hadith',      'name' => 'الحديث والسيرة'],
            ['slug' => 'aqeedah',     'name' => 'العقيدة والتوحيد'],
            ['slug' => 'arabic',      'name' => 'اللغة العربية'],
            ['slug' => 'language',    'name' => 'اللغات الأجنبية'],
            ['slug' => 'literature',  'name' => 'الأدب والنصوص'],
            ['slug' => 'math',        'name' => 'الرياضيات'],
            ['slug' => 'science',     'name' => 'العلوم الطبيعية'],
            ['slug' => 'programming', 'name' => 'البرمجة والتقنية'],
            ['slug' => 'history',     'name' => 'التاريخ والجغرافيا'],
            ['slug' => 'social',      'name' => 'الدراسات الاجتماعية'],
            ['slug' => 'education',   'name' => 'التربية وعلم النفس'],
            ['slug' => 'business',    'name' => 'الأعمال والإدارة'],
            ['slug' => 'design',      'name' => 'التصميم والفنون'],
            ['slug' => 'health',      'name' => 'الصحة واللياقة'],
            ['slug' => 'other',       'name' => 'أخرى'],
        ];

        $now = now();
        foreach ($categories as &$cat) {
            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
        }

        DB::table('categories')->insert($categories);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
