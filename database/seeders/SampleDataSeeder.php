#!/usr/bin/env php
<?php

/**
 * Script لإدراج بيانات تجريبية في قاعدة البيانات
 * 
 * الاستخدام: php seed_data.php
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;

echo "🌱 جاري إدراج البيانات التجريبية...\n\n";

try {
    // 1. إنشاء المستخدمين
    echo "1️⃣  إنشاء المستخدمين...\n";
    
    $admin = User::firstOrCreate(
        ['email' => 'admin@iglal.com'],
        [
            'name' => 'مدير النظام',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '0501234567',
            'bio' => 'مسؤول النظام'
        ]
    );
    echo "   ✅ Admin: admin@iglal.com\n";

    $teacher = User::firstOrCreate(
        ['email' => 'teacher@iglal.com'],
        [
            'name' => 'محمد المعلم',
            'password' => bcrypt('password123'),
            'role' => 'teacher',
            'status' => 'active',
            'phone' => '0509876543',
            'bio' => 'معلم البرمجة'
        ]
    );
    echo "   ✅ Teacher: teacher@iglal.com\n";

    for ($i = 1; $i <= 3; $i++) {
        User::firstOrCreate(
            ['email' => "student$i@iglal.com"],
            [
                'name' => "طالب $i",
                'password' => bcrypt('password123'),
                'role' => 'student',
                'status' => 'active',
                'phone' => '050' . (1000000 + $i),
                'bio' => "طالب نشيط"
            ]
        );
        echo "   ✅ Student $i: student$i@iglal.com\n";
    }

    // 2. إنشاء المسارات
    echo "\n2️⃣  إنشاء المسارات...\n";
    
    $courses = [];
    $courseNames = [
        ['name' => 'مبادئ البرمجة بـ PHP', 'desc' => 'تعلم أساسيات البرمجة مع PHP من الصفر'],
        ['name' => 'قواعد البيانات MySQL', 'desc' => 'إتقان إدارة قواعد البيانات الأساسية'],
        ['name' => 'Laravel المتقدم', 'desc' => 'بناء تطبيقات ويب احترافية مع Laravel']
    ];

    foreach ($courseNames as $course) {
        $c = Course::firstOrCreate(
            ['name' => $course['name']],
            [
                'instructor_id' => $teacher->id,
                'description' => $course['desc'],
                'duration' => 40 + rand(20, 60),
                'status' => 'published'
            ]
        );
        $courses[] = $c;
        echo "   ✅ {$course['name']}\n";
    }

    // 3. إنشاء الدروس
    echo "\n3️⃣  إنشاء الدروس...\n";
    
    foreach ($courses as $course) {
        for ($i = 1; $i <= 3; $i++) {
            Lesson::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'name' => "الدرس " . ($i)
                ],
                [
                    'video_url' => "https://example.com/video-$i.mp4",
                    'duration' => 20 + rand(10, 30),
                    'order' => $i
                ]
            );
        }
        echo "   ✅ {$course->name} (3 دروس)\n";
    }

    // 4. إنشاء الاختبارات والأسئلة
    echo "\n4️⃣  إنشاء الاختبارات والأسئلة...\n";
    
    $lessons = Lesson::all();
    foreach ($lessons as $lesson) {
        $exam = Exam::firstOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'name' => 'اختبار ' . $lesson->name,
                'passing_score' => 70,
                'attempts_allowed' => 3
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            $question = Question::firstOrCreate(
                [
                    'exam_id' => $exam->id,
                    'question_text' => "السؤال $i في الدرس {$lesson->name}",
                ],
                [
                    'question_type' => 'multiple_choice',
                    'order' => $i,
                    'video_timestamp' => ($i - 1) * 120  // كل سؤال بعد دقيقتين من الفيديو
                ]
            );

            // إضافة إجابات
            for ($j = 1; $j <= 4; $j++) {
                Answer::firstOrCreate(
                    [
                        'question_id' => $question->id,
                        'answer_text' => "الخيار $j"
                    ],
                    [
                        'is_correct' => $j === 1,  // الخيار الأول هو الصحيح
                        'explanation' => "هذا الخيار صحيح لأن...",
                        'order' => $j
                    ]
                );
            }
        }
        echo "   ✅ {$lesson->name} - اختبار بـ 5 أسئلة\n";
    }

    echo "\n✅ تم إدراج البيانات التجريبية بنجاح!\n\n";
    echo "📝 بيانات الدخول:\n";
    echo "═══════════════════════════════════════\n";
    echo "👨‍💼 Admin:\n";
    echo "   البريد: admin@iglal.com\n";
    echo "   كلمة المرور: password123\n\n";
    echo "👨‍🏫 Teacher:\n";
    echo "   البريد: teacher@iglal.com\n";
    echo "   كلمة المرور: password123\n\n";
    echo "👨‍🎓 Student:\n";
    echo "   البريد: student1@iglal.com\n";
    echo "   كلمة المرور: password123\n\n";
    echo "═══════════════════════════════════════\n";
    echo "🌐 الآن يمكنك الوصول إلى: http://localhost:8000\n";

} catch (\Exception $e) {
    echo "❌ خطأ: {$e->getMessage()}\n";
    exit(1);
}
?>
