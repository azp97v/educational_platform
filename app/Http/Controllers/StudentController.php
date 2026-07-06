<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\StudentInquiry;
use App\Models\UserProgress;
use App\Models\Streak;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
use App\Models\SmartRewind;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use ZipArchive;

class StudentController extends Controller
{
    /**
     * عرض لوحة تحكم الطالب مع جميع البيانات الحقيقية
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ المسارات المقبول فيها الطالب فقط (البيانات الحقيقية)
        $enrolledCourses = $user->enrolledCourses()
            ->where('courses.status', 'published')  // ✅ qualified with table name
            ->whereNull('courses.deleted_at')  // ✅ qualified with table name
            ->with('lessons')
            ->get();

        // ✅ تصفية المسارات المحذوفة من بيانات الالتحاق
        if($enrolledCourses->isEmpty()) {
            $enrolledCourses = collect();
        }

        // ✅ المسارات المعلقة (pending) - في انتظار موافقة المعلم
        $pendingCourses = Course::whereHas('enrollmentRequests', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'pending');
        })
        ->where('status', 'published')
        ->whereNull('deleted_at')
        ->with('lessons')
        ->get();

        // ✅ المسارات المرفوضة - يمكن إعادة الطلب
        $rejectedCourses = Course::whereHas('enrollmentRequests', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'rejected');
        })
        ->where('status', 'published')
        ->whereNull('deleted_at')
        ->with('lessons')
        ->get();

        $teacherId = $user->teacher_id;
        $availableCoursesQuery = Course::where('status', 'published')
            ->whereNull('deleted_at')
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('course_id')
                    ->from('course_enrollments')
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved', 'rejected']);
            });

        if ($teacherId) {
            $availableCoursesQuery->where('instructor_id', $teacherId);
        }

        $availableCourses = $availableCoursesQuery->orderBy('created_at', 'desc')->get();

        // حساب نسبة التقدم وعدد الدروس المكتملة لكل مسار — دفعة واحدة
        $allLessonIds = $enrolledCourses->flatMap->lessons->pluck('id')->unique()->values();
        $progressLookup = UserProgress::whereIn('lesson_id', $allLessonIds)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->flip();

        $courseProgress = [];
        $courseDetails = [];
        foreach ($enrolledCourses as $course) {
            $lessons = $course->lessons;
            $totalLessons = $lessons->count();

            $completedLessons = $totalLessons > 0
                ? $lessons->sum(fn($l) => $progressLookup->has($l->id) ? 1 : 0)
                : 0;
            $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            $courseProgress[$course->id] = $percentage;
            $courseDetails[$course->id] = [
                'total' => $totalLessons,
                'completed' => $completedLessons,
                'lessons' => $lessons
            ];
        }

        // الدرس الحالي (آخر درس بدأه الطالب)
        $currentLesson = $user->progress()
            ->where('status', 'in_progress')
            ->with('lesson.course')
            ->latest('updated_at')
            ->first();

        // بيانات صفحة الدروس
        $pageLesson = $currentLesson ? $currentLesson->lesson : ($enrolledCourses->first() ? $enrolledCourses->first()->lessons->first() : null);
        $pageLessonDetails = null;

        if ($pageLesson) {
            $course = $pageLesson->course;
            $lessons = $course->lessons->sortBy('order');

            // حساب حالة التقدم لكل درس — دفعة واحدة
            $pageLessonIds = $lessons->pluck('id');
            $pageProgressMap = UserProgress::whereIn('lesson_id', $pageLessonIds)
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('lesson_id');

            $lessonsWithProgress = [];
            $pageCompletedCount = 0;
            foreach ($lessons as $lesson) {
                $progress = $pageProgressMap->get($lesson->id);
                $isCompleted = $progress && $progress->status === 'completed';
                if ($isCompleted) $pageCompletedCount++;

                $lessonsWithProgress[] = [
                    'lesson' => $lesson,
                    'progress' => $progress,
                    'isCompleted' => $isCompleted,
                    'index' => count($lessonsWithProgress) + 1
                ];
            }

            $pageLessonDetails = [
                'lesson' => $pageLesson,
                'course' => $course,
                'lessons' => $lessons,
                'lessonsWithProgress' => $lessonsWithProgress,
                'completed' => $pageCompletedCount,
            ];
        }

        // جميع الاختبارات المتاحة للطالب — دفعة واحدة مع عدد الأسئلة
        $availableExams = [];
        $allCourseLessonIds = $enrolledCourses->flatMap->lessons->pluck('id')->unique()->values();
        if ($allCourseLessonIds->isNotEmpty()) {
            $exams = Exam::whereIn('lesson_id', $allCourseLessonIds)
                ->published()
                ->notExpired()
                ->withCount('questions')
                ->get();
            foreach ($exams as $exam) {
                $availableExams[] = [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'description' => $exam->description,
                    'duration' => $exam->duration,
                    'questions_count' => $exam->questions_count,
                ];
            }
        }

        // الطلاب المتنافسون من أعلى النقاط (query واحد، top3 مشتقة منه)
        $topStudents  = $this->getTopStudents(20);
        $top3Students = $topStudents->take(3);

        // بيانات الطالب الشخصية - استخدام StreakService للحصول على البيانات الصحيحة
        $streakService = new StreakService();
        $myStreak = $user->streak ?? new Streak();

        // تحديث بيانات الـ streak من الخدمة
        $streakData = $streakService->getStreakData($user);
        $myStreak->current_streak = $streakData['current_streak'];
        $myStreak->longest_streak = $streakData['longest_streak'];
        $myStreak->total_points = $streakData['total_points'];

        $myCertificates = $user->certificates()->get();
        $myAchievements = $user->achievements()->get();
        $myProgress = $user->progress()->with('lesson.course')->get();

        return view('student.dashboard', compact(
            'enrolledCourses',
            'availableCourses',
            'courseProgress',
            'courseDetails',
            'currentLesson',
            'pageLessonDetails',
            'availableExams',
            'topStudents',
            'top3Students',
            'myStreak',
            'myCertificates',
            'myAchievements',
            'myProgress'
        ));
    }

    /**
     * صفحة الأكاديمية والمسارات
     */
    public function academy()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ تحميل المسارات المقبول بها الطالب
        // ✅ Qualify table names to avoid ambiguous column errors
        $enrolledCourses = $user->enrolledCourses()
            ->where('courses.status', 'published')  // ✅ qualified
            ->whereNull('courses.deleted_at')  // ✅ qualified
            ->with('lessons')
            ->get();

        // ✅ المسارات المعلقة (pending) - في انتظار موافقة المعلم
        $pendingCourses = Course::whereHas('enrollmentRequests', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'pending');
        })
        ->where('status', 'published')
        ->whereNull('deleted_at')
        ->with('lessons')
        ->get();

        // ✅ المسارات المرفوضة - يمكن إعادة الطلب
        $rejectedCourses = Course::whereHas('enrollmentRequests', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('status', 'rejected');
        })
        ->where('status', 'published')
        ->whereNull('deleted_at')
        ->with('lessons')
        ->get();

        // ✅ جميع دورات معلم الطالب المتاحة للتسجيل (لم يسجل بعد)
        $teacherId = $user->teacher_id;
        $availableCoursesQuery = Course::where('status', 'published')
            ->whereNull('deleted_at')  // ✅ استبعاد المسارات المحذوفة
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('course_id')
                    ->from('course_enrollments')
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'approved', 'rejected']);
            });

        if ($teacherId) {
            $availableCoursesQuery->where('instructor_id', $teacherId);
        }

        $availableCourses = $availableCoursesQuery->orderBy('created_at', 'desc')->get();

        // تقدم المسارات — دفعة واحدة
        $allLessonIds = $enrolledCourses->flatMap->lessons->pluck('id')->unique()->values();
        $progressLookup = UserProgress::whereIn('lesson_id', $allLessonIds)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->flip();

        $courseProgress = [];
        $courseDetails = [];
        foreach ($enrolledCourses as $course) {
            $lessons = $course->lessons;
            $totalLessons = $lessons->count();

            $completedLessons = $totalLessons > 0
                ? $lessons->sum(fn($l) => $progressLookup->has($l->id) ? 1 : 0)
                : 0;
            $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

            $courseProgress[$course->id] = $percentage;
            $courseDetails[$course->id] = [
                'total' => $totalLessons,
                'completed' => $completedLessons
            ];
        }

        return view('student.academy', compact(
            'enrolledCourses',
            'pendingCourses',
            'rejectedCourses',
            'availableCourses',
            'courseProgress',
            'courseDetails'
        ));
    }

    /**
     * صفحة الاختبارات
     */
    public function exams()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $enrolledCourses = $user->enrolledCourses()->with('lessons')->get();

        // جميع الاختبارات المتاحة — دفعة واحدة
        $availableExams = [];
        $inProgressExams = [];
        $completedExams = [];
        $allExams = [];

        $endedExams = [];

        $allLessonIds = $enrolledCourses->flatMap->lessons->pluck('id')->unique()->values();
        if ($allLessonIds->isNotEmpty()) {
            $exams = Exam::whereIn('lesson_id', $allLessonIds)
                ->published()
                ->notExpired()
                ->withCount('questions')
                ->get();
            foreach ($exams as $exam) {
                $examData = [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'description' => $exam->instructions ?? 'اختبار شامل في المادة',
                    'duration' => 60,
                    'questions_count' => $exam->questions_count,
                    'level' => 'متقدم'
                ];

                $availableExams[] = $examData;
                $allExams[] = $examData;
            }

            // الاختبارات المنتهية (expires_at في الماضي)
            $expiredExams = Exam::whereIn('lesson_id', $allLessonIds)
                ->published()
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->withCount('questions')
                ->get();

            $examIds = $expiredExams->pluck('id');
            $attempts = \DB::table('exam_attempts')
                ->whereIn('exam_id', $examIds)
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->orderByDesc('score')
                ->get()
                ->keyBy('exam_id');

            foreach ($expiredExams as $exam) {
                $attempt = $attempts->get($exam->id);
                $endedExams[] = [
                    'id'              => $exam->id,
                    'name'            => $exam->name,
                    'description'     => $exam->instructions ?? 'اختبار شامل في المادة',
                    'questions_count' => $exam->questions_count,
                    'expires_at'      => $exam->expires_at->format('Y-m-d'),
                    'submitted'       => (bool) $attempt,
                    'score'           => $attempt ? (int) $attempt->score : null,
                    'percentage'      => $attempt ? (float) $attempt->percentage : null,
                    'passed'          => $attempt ? (bool) $attempt->passed : false,
                ];
            }
        }

        return view('student.exams', compact('availableExams', 'inProgressExams', 'completedExams', 'allExams', 'endedExams'));
    }

    /**
     * صفحة المتنافسين
     */
    public function competition()
    {
        $topStudents  = $this->getTopStudents(20);
        $top3Students = $topStudents->take(3);

        return view('student.competition', compact('topStudents', 'top3Students'));
    }

    /**
     * صفحة الإنجازات
     */
    public function achievementsPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // استخدام StreakService للحصول على البيانات الصحيحة
        $streakService = new StreakService();
        $myStreak = $user->streak ?? new Streak();

        // تحديث بيانات الـ streak من الخدمة
        $streakData = $streakService->getStreakData($user);
        $myStreak->current_streak = $streakData['current_streak'];
        $myStreak->longest_streak = $streakData['longest_streak'];
        $myStreak->total_points = $streakData['total_points'];

        $myCertificates = $user->certificates()->get();
        $myAchievements = $user->achievements()->get();
        $myDesignedCertificates = \App\Models\Certificates\CertificateStudent::where('recipient_user_id', $user->id)
            ->where('user_id', $user->teacher_id)
            ->latest()
            ->get()
            ->map(function ($student) {
                $student->setRelation('issuedTemplates', \App\Models\Certificates\CustomTemplate::where('user_id', $student->user_id)
                    ->where('recipient_name', $student->name)
                    ->where('is_issued', true)
                    ->latest()
                    ->get());

                return $student;
            })
            ->filter(fn ($student) => $student->issuedTemplates->isNotEmpty())
            ->values();

        return view('student.achievements', compact('myStreak', 'myCertificates', 'myAchievements', 'myDesignedCertificates'));
    }

    /**
     * التقديم على الالتحاق بمسار
     */
    public function requestEnrollment(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن المسار منشور
        if ($course->status !== 'published') {
            return redirect()->route('student.index')
                ->with('error', 'هذا المسار غير متاح حالياً. يرجى المحاولة لاحقاً');
        }

        // ✅ التحقق من أن المسار لم يُحذف
        if ($course->trashed()) {
            return redirect()->route('student.index')
                ->with('error', 'هذا المسار لم يعد متوفراً');
        }

        // التحقق من أن المسار من معلم الطالب
        if ($course->instructor_id !== $user->teacher_id) {
            return redirect()->route('student.index')
                ->with('error', 'لا تملك الصلاحية للالتحاق بهذا المسار');
        }

        // ✅ التحقق من عدم وجود طلب التحاق معلق أو موافق عليه
        $existing = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            $statusText = $existing->status === 'pending' ? 'قيد المراجعة' : 'موافق عليه';
            return redirect()->route('student.index')
                ->with('warning', "أنت بالفعل {$statusText} في هذا المسار");
        }

        // ✅ السماح بإعادة الطلب بعد الرفض (حذف السجل السابق المرفوض)
        $rejected = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'rejected')
            ->first();

        if ($rejected) {
            $rejected->forceDelete();  // حذف نهائي للسماح بإعادة الطلب
        }

        // إنشاء طلب التحاق جديد
        $enrollment = CourseEnrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
            'enrolled_at' => null  // سيتم تعيينه عند الموافقة
        ]);

        if ($enrollment) {
            $teacher = $course->instructor;
            if ($teacher && ($teacher->notify_enrollment ?? true)) {
                $teacher->notify(new AppNotification(
                    'طلب انضمام جديد',
                    "{$user->name} طلب الالتحاق بمسارك \"{$course->name}\".",
                    route('teacher.enrollment.requests'),
                    'enrollment',
                    'ri-user-add-line'
                ));
            }

            return redirect()->route('student.index')
                ->with('success', 'تم تقديم طلب الالتحاق بنجاح! بانتظار موافقة المعلم');
        } else {
            return redirect()->route('student.index')
                ->with('error', 'حدث خطأ أثناء تقديم الطلب. يرجى المحاولة لاحقاً');
        }
    }

    /**
     * صفحة بطاقة المسار للطالب عند الإشعار
     */
    public function courseCard(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($course->trashed() || $course->status !== 'published') {
            return redirect()->route('student.index')
                ->with('error', 'هذا المسار غير متاح حالياً.');
        }

        if ($course->instructor_id !== $user->teacher_id) {
            return redirect()->route('student.index')
                ->with('error', 'لا تملك صلاحية الوصول إلى هذا المسار.');
        }

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment && $enrollment->status === 'approved') {
            return redirect()->route('student.course.show', $course);
        }

        $lessonCount = $course->lessons()->count();
        $pending = $enrollment && $enrollment->status === 'pending';
        $preview = true;

        $lessonsList = $course->lessons()->orderBy('order')->get();
        $totalLessons = $lessonsList->count();
        $progress = 0;
        $completedLessonsCount = 0;

        $lessons = $lessonsList->map(function ($lesson) {
            return $this->mapLessonForStudent($lesson, false);
        })->toArray();

        return view('student.course', compact(
            'course',
            'lessons',
            'progress',
            'totalLessons',
            'completedLessonsCount',
            'preview',
            'pending',
            'lessonCount',
            'enrollment'
        ));
    }

    /**
     * عرض تفاصيل مسار (للطلاب المقبولين فقط)
     */
    public function show(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن المسار لم يتم حذفه
        if ($course->trashed()) {
            return redirect()->route('student.index')
                ->with('error', 'هذا المسار لم يعد متوفراً');
        }

        // ✅ التحقق من أن المسار منشور
        if ($course->status !== 'published') {
            return redirect()->route('student.index')
                ->with('error', 'هذا المسار غير متاح حالياً');
        }

        // ✅ التحقق من أن الطالب مقبول بشكل صريح في هذا المسار
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.index')
                ->with('error', 'لم تلتحق بهذا المسار بعد');
        }

        if ($enrollment->status !== 'approved') {
            $statusMsg = $enrollment->status === 'pending'
                ? 'طلبك قيد المراجعة من قبل المعلم'
                : 'تم رفض طلبك للالتحاق بهذا المسار';
            return redirect()->route('student.index')
                ->with('warning', $statusMsg);
        }

        // تحميل الدروس مع إحصائيات التقدم بكفاءة
        $lessonsList = $course->lessons()->orderBy('order')->with('userProgress')->get();

        $completedLessonsCount = UserProgress::whereIn('lesson_id', $lessonsList->pluck('id'))
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalLessons = $lessonsList->count();
        $progress = $totalLessons > 0 ? round(($completedLessonsCount / $totalLessons) * 100) : 0;

        // جلب جميع معرّفات الدروس المكتملة دفعةً واحدة (يمنع N+1)
        $completedIds = UserProgress::whereIn('lesson_id', $lessonsList->pluck('id'))
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->flip();

        $lessons = $lessonsList->map(function($lesson) use ($completedIds) {
            return $this->mapLessonForStudent($lesson, $completedIds->has($lesson->id));
        })->toArray();

        return view('student.course', compact('course', 'lessons', 'progress', 'totalLessons', 'completedLessonsCount'));
    }

    public function startLesson(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن الدرس موجود والدورة موجودة
        if (!$lesson->course || $lesson->course->trashed()) {
            return redirect()->route('student.index')
                ->with('error', 'المسار غير متاح حالياً');
        }

        // التحقق من أن الطالب مقبول في الدورة
        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.index')
                ->with('error', 'لا تملك الصلاحية للوصول إلى هذا الدرس');
        }

        $allLessons = $lesson->course->lessons()->orderBy('order')->get();

        $currentProgress = $user->progress()->firstOrCreate(
            ['lesson_id' => $lesson->id],
            ['status' => 'in_progress', 'started_at' => now()]
        );

        $allLessonIds = $allLessons->pluck('id');
        $completedMap = UserProgress::whereIn('lesson_id', $allLessonIds)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->flip();

        $lessonsWithProgress = $allLessons->map(function($ls) use ($completedMap, $lesson) {
            return [
                'id' => $ls->id,
                'title' => $ls->title,
                'duration' => $ls->duration,
                'completed' => $completedMap->has($ls->id),
                'is_current' => $ls->id === $lesson->id
            ];
        })->toArray();

        $lessonNotes = StudentInquiry::where('lesson_id', $lesson->id)
            ->where('student_id', $user->id)
            ->where('inquiry_type', 'note')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.lesson', compact('lesson', 'lessonNotes', 'lessonsWithProgress', 'currentProgress'));
    }

    public function watchLesson(Lesson $lesson)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $course = $lesson->course;

            if(!$course) {
                return redirect()->route('student.index')
                    ->with('error', 'المسار غير متاح حالياً');
            }

            if(method_exists($course, 'trashed') && $course->trashed()) {
                return redirect()->route('student.index')
                    ->with('error', 'المسار غير متاح حالياً');
            }

            if($course->status !== 'published') {
                return redirect()->route('student.index')
                    ->with('error', 'هذا المسار غير منشور');
            }

            $enrollment = CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'approved')
                ->first();

            if (!$enrollment) {
                return redirect()->route('student.index')
                    ->with('error', 'لا تملك الصلاحية للوصول إلى هذا الدرس');
            }

            $allLessons = $course->lessons()->orderBy('order')->get();

            $currentProgress = $user->progress()->firstOrCreate(
                ['lesson_id' => $lesson->id],
                ['status' => 'in_progress', 'started_at' => now()]
            );

            $allLessonIds = $allLessons->pluck('id');
            $completedMap = UserProgress::whereIn('lesson_id', $allLessonIds)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->pluck('lesson_id')
                ->flip();

            $lessonsWithProgress = $allLessons->map(function($ls) use ($completedMap, $lesson) {
                return [
                    'id' => $ls->id,
                    'title' => $ls->name,
                    'duration' => $ls->duration,
                    'completed' => $completedMap->has($ls->id),
                    'is_current' => $ls->id === $lesson->id
                ];
            })->toArray();

            $lessonNotes = StudentInquiry::where('lesson_id', $lesson->id)
                ->where('student_id', $user->id)
                ->where('inquiry_type', 'note')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('student.lesson', compact('lesson', 'lessonsWithProgress', 'lessonNotes', 'currentProgress'));
        } catch (\Throwable $e) {
            \Log::error('watchLesson failed: ' . $e->getMessage(), [
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('student.index')
                ->with('error', 'حدث خطأ أثناء تحميل الدرس. يرجى المحاولة مرة أخرى.');
        }
    }

    public function streamLessonMedia(Request $request, Lesson $lesson, string $type)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $course = $lesson->course;

        if (!$course || $course->trashed() || $course->status !== 'published') {
            abort(404);
        }

        $hasAccess = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $relativePath = $type === 'audio' ? $lesson->audio_file : $lesson->video_file;
        if (!$relativePath) {
            abort(404);
        }

        $preferredDisk = (string) config('media.disk', 'public');
        $diskNames = array_values(array_unique(array_filter([$preferredDisk, 'public', 'local'])));
        $disk = null;

        foreach ($diskNames as $diskName) {
            try {
                $candidate = Storage::disk($diskName);
                if ($candidate->exists($relativePath)) {
                    $disk = $candidate;
                    break;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        if (!$disk) {
            abort(404);
        }

        $absolutePath = method_exists($disk, 'path') ? $disk->path($relativePath) : null;
        if (!$absolutePath || !is_file($absolutePath)) {
            $stream = $disk->readStream($relativePath);
            if ($stream === false) {
                abort(404);
            }

            return response()->stream(function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            }, 200, [
                'Content-Type' => guessMessagingMimeType($relativePath, $disk->mimeType($relativePath)),
                'Accept-Ranges' => 'bytes',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $size = filesize($absolutePath);
        $start = 0;
        $end = max(0, $size - 1);
        $status = 200;
        $headers = [
            'Content-Type' => guessMessagingMimeType($relativePath, $disk->mimeType($relativePath)),
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
        ];

        $range = (string) $request->headers->get('Range', '');
        if (preg_match('/bytes=(\d*)-(\d*)/', $range, $matches)) {
            if ($matches[1] !== '') {
                $start = max(0, (int) $matches[1]);
            }
            if ($matches[2] !== '') {
                $end = min($end, (int) $matches[2]);
            }
            if ($start > $end || $start >= $size) {
                return response('', 416, [
                    'Content-Range' => "bytes */{$size}",
                    'Accept-Ranges' => 'bytes',
                ]);
            }
            $status = 206;
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
        }

        $length = $end - $start + 1;
        $headers['Content-Length'] = (string) $length;

        return response()->stream(function () use ($absolutePath, $start, $length) {
            $handle = fopen($absolutePath, 'rb');
            if ($handle === false) {
                return;
            }
            fseek($handle, $start);
            $remaining = $length;
            while ($remaining > 0 && !feof($handle)) {
                $chunkSize = min(8192, $remaining);
                echo fread($handle, $chunkSize);
                $remaining -= $chunkSize;
                if (connection_aborted()) {
                    break;
                }
            }
            fclose($handle);
        }, $status, $headers);
    }

    public function showExam($examId)
    {
        $user = Auth::user();

        $exam = Exam::findOrFail($examId);

        if (!$exam->is_published) {
            return redirect()->route('student.exams')
                ->with('error', 'هذا الاختبار غير منشور بعد.');
        }

        if ($exam->isExpired()) {
            // Allow viewing results if student already submitted
            $hasAttempt = \DB::table('exam_attempts')
                ->where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->exists();

            if ($hasAttempt) {
                return redirect()->route('student.exam.results', ['exam' => $exam->id]);
            }

            return redirect()->route('student.exams')
                ->with('error', 'انتهت صلاحية هذا الاختبار.');
        }

        if (!$exam->lesson || !$exam->lesson->course || $exam->lesson->course->trashed()) {
            return redirect()->route('student.index')
                ->with('error', 'المسار أو الاختبار غير متاح');
        }

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $exam->lesson->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.index')
                ->with('error', 'لا تملك الصلاحية للوصول إلى هذا الاختبار');
        }

        $attemptsAllowed = (int) ($exam->attempts_allowed ?? 0);
        if ($attemptsAllowed > 0) {
            $previousAttempts = \DB::table('exam_attempts')
                ->where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->count();

            if ($previousAttempts >= $attemptsAllowed) {
                return redirect()->route('student.exam.results', ['exam' => $exam->id])
                    ->with('error', 'لقد استنفدت عدد المحاولات المسموح بها (' . $attemptsAllowed . ').');
            }
        }

        $questions = $exam->questions()->with('answers')->get();
        return view('student.exam', compact('exam', 'questions'));
    }

    public function updateProgress(Request $request, Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $streakService = new StreakService();

        $validated = $request->validate([
            'action' => 'nullable|in:track,complete',
            'watched_seconds' => 'nullable|numeric|min:0',
            'duration_seconds' => 'nullable|numeric|min:0',
        ]);

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'لا تملك صلاحية الوصول إلى هذا الدرس.',
            ], 403);
        }

        $action = $validated['action'] ?? 'complete';
        $watchedSeconds = (float) ($validated['watched_seconds'] ?? 0);
        $durationSeconds = (float) ($validated['duration_seconds'] ?? 0);
        $watchPercent = $durationSeconds > 0
            ? min(100, max(0, (int) floor(($watchedSeconds / $durationSeconds) * 100)))
            : 0;

        $progress = UserProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$progress) {
            $progress = UserProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'status' => 'in_progress',
                'progress_percentage' => 0,
                'started_at' => now(),
            ]);
        }

        if ($action === 'track') {
            if ($progress->status !== 'completed') {
                $progress->update([
                    'status' => 'in_progress',
                    'progress_percentage' => max((int) $progress->progress_percentage, $watchPercent),
                    'started_at' => $progress->started_at ?? now(),
                ]);
            }

            $freshProgress = $progress->fresh();
            $proofProgress = (int) ($freshProgress->progress_percentage ?? 0);
            $canComplete = $durationSeconds > 0 ? $watchPercent >= 90 : $proofProgress >= 90;

            return response()->json([
                'success' => true,
                'progress_percentage' => $proofProgress,
                'can_complete' => $canComplete,
            ]);
        }

        $requiresPlaybackProof = in_array($lesson->lesson_type, ['video-upload', 'audio-upload', 'youtube'], true);
        if ($requiresPlaybackProof && $durationSeconds > 0 && $watchPercent < 90) {
            return response()->json([
                'success' => false,
                'message' => 'يجب مشاهدة 90% على الأقل قبل إكمال الدرس.',
                'progress_percentage' => max((int) $progress->progress_percentage, $watchPercent),
            ], 422);
        }

        if ($requiresPlaybackProof && $durationSeconds <= 0 && (int) $progress->progress_percentage < 90) {
            return response()->json([
                'success' => false,
                'message' => 'يجب مشاهدة 90% على الأقل قبل إكمال الدرس.',
                'progress_percentage' => (int) $progress->progress_percentage,
            ], 422);
        }

        $isNewCompletion = false;
        if ($progress->status !== 'completed') {
            $progress->update([
                'status' => 'completed',
                'progress_percentage' => 100,
                'completed_at' => now(),
                'started_at' => $progress->started_at ?? now(),
            ]);
            $isNewCompletion = true;
        }

        if ($isNewCompletion) {
            $streakService->addPoints($user, 10, 'lesson_completion');
        } else {
            $streakService->updateStreak($user, 0);
        }

        return response()->json([
            'success' => true,
            'progress_percentage' => 100,
            'message' => 'تم إكمال الدرس بنجاح.',
            'points' => $user->fresh()->points,
            'streak' => $streakService->getCurrentStreak($user->fresh())
        ]);
    }

    public function downloadLessonResources(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'لا تملك صلاحية تحميل موارد هذا الدرس.');
        }

        try {
            @ini_set('memory_limit', '1024M');
            @set_time_limit(300);

            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipName = "lesson-{$lesson->id}-resources-" . now()->format('Ymd-His') . '.zip';
            $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipName;

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                return redirect()->back()->with('error', 'تعذر تجهيز ملف التحميل.');
            }

            $hasAny = false;

            $diskNames = array_values(array_unique(array_filter([(string) config('media.disk', 'public'), 'public', 'local'])));

            $findFile = function ($relativePath) use ($diskNames) {
                if (!$relativePath) return null;
                foreach ($diskNames as $diskName) {
                    try {
                        $s = Storage::disk($diskName);
                        if ($s->exists($relativePath)) {
                            $p = method_exists($s, 'path') ? $s->path($relativePath) : null;
                            if ($p && file_exists($p)) return $p;
                        }
                    } catch (\Throwable $e) { continue; }
                }
                return null;
            };

            $videoPath = $findFile($lesson->video_file);
            if ($videoPath) {
                $zip->addFile($videoPath, basename($lesson->video_file));
                $hasAny = true;
            }

            $audioPath = $findFile($lesson->audio_file);
            if ($audioPath) {
                $zip->addFile($audioPath, basename($lesson->audio_file));
                $hasAny = true;
            }

            if (!empty($lesson->content)) {
                $zip->addFromString('lesson-content.txt', trim(strip_tags($lesson->content)));
                $hasAny = true;
            }

            if (!empty($lesson->video_url)) {
                $zip->addFromString('video-link.txt', trim($lesson->video_url));
                $hasAny = true;
            }

            $zip->close();

            if (!$hasAny) {
                @unlink($zipPath);
                return redirect()->back()->with('error', 'لا توجد موارد متاحة للتحميل في هذا الدرس.');
            }

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Lesson resource download failed: ' . $e->getMessage(), [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ]);
            if (isset($zipPath) && file_exists($zipPath)) {
                @unlink($zipPath);
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء تجهيز الموارد. يرجى المحاولة مرة أخرى.');
        }
    }
    public function submitExam(Request $request, Exam $exam)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $streakService = new StreakService();

            if (
                !$exam->is_published ||
                $exam->isExpired() ||
                !$exam->lesson ||
                !$exam->lesson->course ||
                (method_exists($exam->lesson->course, 'trashed') && $exam->lesson->course->trashed()) ||
                $exam->lesson->course->status !== 'published' ||
                !CourseEnrollment::where('user_id', $user->id)
                    ->where('course_id', $exam->lesson->course_id)
                    ->where('status', 'approved')
                    ->exists()
            ) {
                return redirect()->route('student.exams')
                    ->with('error', 'لا تملك صلاحية تقديم هذا الاختبار.');
            }

            $validated = $request->validate([
                'answers' => 'required|array'
            ]);

            $lock = Cache::lock('exam_submit:' . $exam->id . ':' . $user->id, 30);

            if (!$lock->get()) {
                return redirect()->route('student.exam.results', ['exam' => $exam->id])
                    ->with('info', 'تتم معالجة إجابتك بالفعل.');
            }

            try {
                DB::transaction(function () use ($request, $exam, $user, $validated, $streakService) {
                    $previousAttempts = (int) DB::table('exam_attempts')
                        ->where('exam_id', $exam->id)
                        ->where('user_id', $user->id)
                        ->count();

                    $attemptsAllowed = $exam->attempts_allowed;
                    if (!is_null($attemptsAllowed) && $attemptsAllowed > 0 && $previousAttempts >= $attemptsAllowed) {
                        throw new \Exception('لقد استنفدت عدد المحاولات المسموح بها لهذا الاختبار.');
                    }

                    $questions = $exam->questions()->with('answers')->get();
                    $correctCount = 0;
                    $totalPoints = 0;
                    $multipleChoiceCount = 0;
                    $rows = [];

                    foreach ($questions as $question) {
                        $selectedAnswer = $validated['answers'][$question->id] ?? null;

                        if ($question->question_type === 'short_answer') {
                            $rows[] = [
                                'exam_id' => $exam->id,
                                'question_id' => $question->id,
                                'user_id' => $user->id,
                                'answer_text' => is_string($selectedAnswer) ? $selectedAnswer : '',
                                'answer_id' => null,
                                'is_marked' => false,
                                'score' => 0,
                                'teacher_feedback' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        } else {
                            $multipleChoiceCount++;
                            $correctAnswer = $question->answers->firstWhere('is_correct', true);

                            if ($selectedAnswer !== null && !$question->answers->contains('id', $selectedAnswer)) {
                                continue;
                            }

                            $isCorrect = $selectedAnswer !== null && $correctAnswer && $selectedAnswer == $correctAnswer->id;

                            if ($isCorrect) {
                                $correctCount++;
                                $totalPoints += 50;
                            }

                            $rows[] = [
                                'exam_id' => $exam->id,
                                'question_id' => $question->id,
                                'user_id' => $user->id,
                                'answer_id' => $selectedAnswer,
                                'answer_text' => null,
                                'is_marked' => true,
                                'score' => $isCorrect ? 50 : 0,
                                'teacher_feedback' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (!empty($rows)) {
                        DB::table('exam_answers')->insert($rows);
                    }

                    $percentage = $multipleChoiceCount > 0 ? ($correctCount / $multipleChoiceCount) * 100 : 0;
                    $bonusPoints = 0;

                    if ($multipleChoiceCount > 0) {
                        if ($percentage >= 100) {
                            $bonusPoints = 100;
                        } elseif ($percentage >= 80) {
                            $bonusPoints = 50;
                        } elseif ($percentage >= 60) {
                            $bonusPoints = 25;
                        }
                        $totalPoints += $bonusPoints;
                    }

                    $passed = $percentage >= ($exam->passing_score ?? 70);
                    $attemptNumber = $previousAttempts + 1;

                    DB::table('exam_attempts')->insert([
                        'exam_id' => $exam->id,
                        'user_id' => $user->id,
                        'attempt_number' => $attemptNumber,
                        'total_questions' => $multipleChoiceCount,
                        'correct_answers' => $correctCount,
                        'score' => $totalPoints,
                        'percentage' => $percentage,
                        'passed' => $passed,
                        'started_at' => now()->subHours(1),
                        'submitted_at' => now(),
                        'duration_seconds' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $streakService->addPoints($user, $totalPoints, 'exam_completion');
                });
            } finally {
                $lock->release();
            }

            return redirect()->route('student.exam.results', ['exam' => $exam->id])
                ->with('success', 'تم تقديم الاختبار بنجاح!');
        } catch (\Exception $e) {
            \Log::error('submitExam failed: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
            ]);
            return redirect()->route('student.exams')
                ->with('error', 'حدث خطأ أثناء تقديم الاختبار: ' . $e->getMessage());
        }
    }

    /**
     * عرض نتائج الاختبار
     */
    public function examResults(Exam $exam)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (
                !$exam->lesson ||
                !$exam->lesson->course ||
                (method_exists($exam->lesson->course, 'trashed') && $exam->lesson->course->trashed()) ||
                !CourseEnrollment::where('user_id', $user->id)
                    ->where('course_id', $exam->lesson->course_id)
                    ->where('status', 'approved')
                    ->exists()
            ) {
                return redirect()->route('student.exams')
                    ->with('error', 'لا تملك صلاحية الوصول لنتائج هذا الاختبار.');
            }

            $attempt = \DB::table('exam_attempts')
                ->where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->orderBy('attempt_number', 'desc')
                ->first();

            if (!$attempt) {
                return redirect()->route('student.exams')->with('error', 'لم تقدم هذا الاختبار بعد');
            }

            $attempt->submitted_at = \Carbon\Carbon::parse($attempt->submitted_at);
            $attempt->started_at = \Carbon\Carbon::parse($attempt->started_at);

            $answers = \DB::table('exam_answers')
                ->where('exam_id', $exam->id)
                ->where('user_id', $user->id)
                ->get();

            $questions = $exam->questions()->with('answers')->get();

            return view('student.exam-results', compact('exam', 'attempt', 'answers', 'questions', 'user'));
        } catch (\Exception $e) {
            \Log::error('examResults failed: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
            ]);
            return redirect()->route('student.exams')
                ->with('error', 'حدث خطأ أثناء تحميل نتائج الاختبار.');
        }
    }

    public function submitExamAnswer(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question_id' => 'required|exists:questions,id',
            'selected_answer_id' => 'required|exists:answers,id',
            'video_timestamp' => 'nullable|integer',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $question = Question::findOrFail($request->question_id);
        $selectedAnswer = Answer::findOrFail($request->selected_answer_id);
        $correctAnswer = $question->answers()->where('is_correct', true)->first();

        // Check if answer is correct
        $isCorrect = $selectedAnswer->id === $correctAnswer->id;

        // Get default explanation if not provided
        $explanation = $correctAnswer->explanation ??
            'الإجابة الصحيحة هي: ' . $correctAnswer->answer_text;

        // If answer is wrong, create Smart Rewind
        if (!$isCorrect) {
            SmartRewind::createFromWrongAnswer(
                Auth::id(),
                $question->id,
                $exam->id,
                $request->video_timestamp ?? 0,
                $explanation
            );

            return response()->json([
                'success' => false,
                'message' => 'إجابة خاطئة. تم إضافة Smart Rewind لك!',
                'correct_answer' => $correctAnswer->answer_text,
                'explanation' => $explanation,
                'points' => -5
            ]);
        }

        // If correct, award points
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->increment('points', 10);

        return response()->json([
            'success' => true,
            'message' => 'إجابة صحيحة! +10 نقاط',
            'points' => 10
        ]);
    }

    private function mapLessonForStudent(Lesson $lesson, bool $isCompleted): array
    {
        $presentation = $this->resolveLessonPresentation($lesson);

        return [
            'id' => $lesson->id,
            'title' => $lesson->title ?? $lesson->name,
            'name' => $lesson->name,
            'description' => $lesson->description,
            'duration' => $lesson->duration,
            'completed' => $isCompleted,
            'lesson_type' => $lesson->lesson_type,
            'video_url' => $lesson->video_url,
            'video_file' => $lesson->video_file,
            'audio_file' => $lesson->audio_file,
            'content' => $lesson->content,
            'content_kind' => $presentation['kind'],
            'content_icon' => $presentation['icon'],
            'content_label' => $presentation['label'],
        ];
    }

    private function resolveLessonPresentation(Lesson $lesson): array
    {
        $lessonType = (string) ($lesson->lesson_type ?? '');
        $videoUrl = (string) ($lesson->video_url ?? '');
        $videoFile = (string) ($lesson->video_file ?? '');
        $audioFile = (string) ($lesson->audio_file ?? '');
        $content = (string) ($lesson->content ?? '');
        $allText = strtolower($videoUrl . ' ' . $videoFile . ' ' . $audioFile . ' ' . $content);

        if (
            $lessonType === 'youtube' ||
            $this->isYoutubeUrl($videoUrl) ||
            str_contains($allText, 'youtube.com') ||
            str_contains($allText, 'youtu.be')
        ) {
            return ['kind' => 'youtube', 'icon' => 'ri-youtube-fill', 'label' => 'المحتوى اللي من اليوتيوب'];
        }

        if (
            $lessonType === 'audio-upload' ||
            !empty($audioFile) ||
            $this->hasExtension($allText, ['mp3', 'wav', 'm4a', 'aac', 'ogg', 'flac'])
        ) {
            return ['kind' => 'audio', 'icon' => 'ri-mic-line', 'label' => 'المحتوى الصوتي'];
        }

        if ($this->hasExtension($allText, ['pdf'])) {
            return ['kind' => 'pdf', 'icon' => 'ri-file-pdf-line', 'label' => 'ملف PDF'];
        }

        if (
            $lessonType === 'video-upload' ||
            !empty($videoFile) ||
            $this->hasExtension($allText, ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'])
        ) {
            return ['kind' => 'video', 'icon' => 'ri-video-line', 'label' => 'فيديو'];
        }

        if (preg_match('/https?:\/\//i', $content)) {
            return ['kind' => 'link', 'icon' => 'ri-links-line', 'label' => 'رابط خارجي'];
        }

        if ($lessonType === 'other-content' || !empty(trim($content))) {
            return ['kind' => 'text', 'icon' => 'ri-file-text-line', 'label' => 'محتوى نصي'];
        }

        return ['kind' => 'general', 'icon' => 'ri-book-open-line', 'label' => 'محتوى تعليمي'];
    }

    private function hasExtension(string $text, array $extensions): bool
    {
        foreach ($extensions as $ext) {
            if (preg_match('/\.' . preg_quote($ext, '/') . '(\?|#|\s|$)/i', $text)) {
                return true;
            }
        }

        return false;
    }

    private function isYoutubeUrl(?string $url): bool
    {
        if (!$url) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        $host = strtolower($host);
        return str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be');
    }

    private function getTopStudents(int $limit): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('role', 'student')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get();
    }
}
