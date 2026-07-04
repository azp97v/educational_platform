<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\StudentQuestion;
use App\Models\StudentInquiry;
use App\Models\CourseEnrollment;
use App\Models\Streak;
use App\Notifications\AppNotification;
use App\Services\Media\MediaStorageService;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected MediaStorageService $mediaStorage;

    public function __construct(MediaStorageService $mediaStorage)
    {
        $this->mediaStorage = $mediaStorage;
    }

    public function dashboard()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // Check if user is teacher
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة - يجب أن تكون معلماً');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $myCourses = $user->courses()->get();
        $totalCourses = $myCourses->count();
        $courseIds = $myCourses->pluck('id');
        $lessonIds = Lesson::whereIn('course_id', $courseIds)->pluck('id');

        $totalStudents = CourseEnrollment::whereIn('course_id', $courseIds)
            ->approved()
            ->distinct('user_id')
            ->count('user_id');

        $pendingEnrollments = CourseEnrollment::whereIn('course_id', $courseIds)
            ->pending()
            ->count();

        $pendingQuestions = StudentQuestion::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->count();

        $pendingInquiries = StudentInquiry::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->count();

        $totalExams = Exam::whereIn('lesson_id', $lessonIds)->count();

        $activeStudents = UserProgress::whereIn('lesson_id', $lessonIds)
            ->where('status', 'started')
            ->distinct('user_id')
            ->count('user_id');

        $recentQuestions = StudentQuestion::with(['student', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('created_at')
            ->take(4)
            ->get();

        $recentInquiries = StudentInquiry::with(['student', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest('created_at')
            ->take(4)
            ->get();

        $pendingEnrollmentsList = CourseEnrollment::with(['student', 'course'])
            ->whereIn('course_id', $courseIds)
            ->pending()
            ->latest('created_at')
            ->take(4)
            ->get();

        $activeStudentsList = UserProgress::with(['user', 'lesson.course'])
            ->whereIn('lesson_id', $lessonIds)
            ->orderBy('progress_percentage', 'desc')
            ->get()
            ->unique('user_id')
            ->take(4);

        $recentCourses = $myCourses->take(4);

        return response()
            ->view('teacher.dashboard', compact(
                'myCourses',
                'totalCourses',
                'totalStudents',
                'pendingEnrollments',
                'pendingQuestions',
                'pendingInquiries',
                'totalExams',
                'activeStudents',
                'recentQuestions',
                'recentInquiries',
                'pendingEnrollmentsList',
                'activeStudentsList',
                'recentCourses'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Wed, 21 Oct 2015 07:28:00 GMT')
            ->header('X-Creative-Version', 'v2.0-' . time());
    }

    public function index()
    {
        // Redirect to dashboard for backward compatibility
        return redirect()->route('teacher.dashboard');
    }

    /**
     * Send a system notification to the teacher's students.
     */
    private function notifyStudents($students, string $title, string $message, string $url, string $category = 'system', string $icon = 'ri-notification-3-line')
    {
        foreach ($students as $student) {
            if ($student && ($student->notify_system ?? true)) {
                $student->notify(new AppNotification($title, $message, $url, $category, $icon));
            }
        }
    }

    public function courses()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // Check if user is teacher
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة - يجب أن تكون معلماً');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $myCourses = $user->courses()->get();

        return response()
            ->view('teacher.courses', compact('myCourses'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Wed, 21 Oct 2015 07:28:00 GMT')
            ->header('X-Creative-Version', 'v2.0-' . time());
    }

    public function createCourse()
    {
        return view('teacher.create-course');
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated['status'] = 'published';
        $course = $user->courses()->create($validated);

        $students = $user->students()->where('role', 'student')->get();
        if ($students->isNotEmpty()) {
            $this->notifyStudents(
                $students,
                'مسار جديد متاح',
                "المعلم {$user->name} أضاف مسارًا جديدًا: \"{$course->name}\". اطلع على البطاقة الآن.",
                route('student.course.card', $course),
                'course',
                'ri-book-open-line'
            );
        }

        return redirect()->route('teacher.show', $course)->with('success', 'تم إنشاء المسار');
    }

    public function show(Course $course)
    {
        // Check ownership
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $lessons = $course->lessons()->orderBy('order')->get();
        $exams = $course->exams()->get() ?? collect();
        $studentQuestions = StudentQuestion::with(['student', 'lesson'])
            ->where('course_id', $course->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $inquiryQuestions = StudentInquiry::with(['student', 'lesson'])
            ->where('course_id', $course->id)
            ->where('inquiry_type', 'question')
            ->orderBy('created_at', 'desc')
            ->get();

        $studentQuestions = $studentQuestions
            ->concat($inquiryQuestions)
            ->sortByDesc('created_at')
            ->values();

        $courseNotes = StudentInquiry::with(['student', 'lesson'])
            ->where('course_id', $course->id)
            ->where('inquiry_type', 'note')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.course', compact('course', 'lessons', 'exams', 'studentQuestions', 'courseNotes'));
    }

    public function showLessonForm(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $courseId = $request->query('course');
        $course = Course::findOrFail($courseId);

        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Get the lesson count for auto-incrementing order
        $nextOrder = $course->lessons()->count() + 1;

        return view('teacher.lesson-form', [
            'course' => $course,
            'nextOrder' => $nextOrder
        ]);
    }

    public function editLesson(Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $course = $lesson->course;
        return view('teacher.lesson-form', compact('lesson', 'course'));
    }

    public function showCreateExamPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get all lessons from teacher's courses
        $lessons = Lesson::whereIn(
            'course_id',
            $user->courses()->pluck('id')
        )->with('course')->orderBy('created_at', 'desc')->get();

        return view('teacher.create-exam', compact('lessons'));
    }

    public function showExamForm(Request $request)
    {
        $lessonId = $request->query('lesson');
        $lesson = Lesson::findOrFail($lessonId);

        if ($lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $exam = null;
        return view('teacher.exam-form', compact('lesson', 'exam'));
    }

    public function editExam(Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $lesson = $exam->lesson;
        return view('teacher.exam-form', compact('exam', 'lesson'));
    }

    public function updateExam(Request $request, Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($exam) {
                    $exists = Exam::where('lesson_id', $exam->lesson_id)
                        ->where('name', $value)
                        ->where('id', '!=', $exam->id)
                        ->exists();
                    if ($exists) {
                        $fail('اسم الاختبار مكرر بالفعل في هذا الدرس. الرجاء اختيار اسم آخر.');
                    }
                },
            ],
            'passing_score' => 'required|numeric|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'duration' => 'nullable|integer|min:1|max:600',
            'expires_at' => 'nullable|date',
            'instructions' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['duration'] = $validated['duration'] ?? 30;
        if (empty($validated['expires_at'])) {
            $validated['expires_at'] = null;
        }
        $exam->update($validated);
        return redirect()->route('teacher.exams')->with('success', 'تم تحديث الاختبار بنجاح!');
    }

    public function showQuestionsPage()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get all student questions for this teacher's courses
        $courseIds = $user->courses()->pluck('id');

        // Fetch Student Questions
        $pendingQuestions = StudentQuestion::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->with(['student', 'lesson', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'q_page');

        $answeredQuestions = StudentQuestion::whereIn('course_id', $courseIds)
            ->where('status', '!=', 'pending')
            ->with(['student', 'lesson', 'course'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Fetch Student Inquiries for unified display - now using course_id directly
        $pendingInquiries = StudentInquiry::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->with(['student', 'lesson', 'course'])
            ->orderBy('created_at', 'desc')
            ->get();

        $answeredInquiries = StudentInquiry::whereIn('course_id', $courseIds)
            ->where('status', 'answered')
            ->with(['student', 'lesson', 'course'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('teacher.questions-manage', compact(
            'pendingQuestions',
            'answeredQuestions',
            'pendingInquiries',
            'answeredInquiries'
        ));
    }

    public function answerStudentQuestion(Request $request, StudentQuestion $question)
    {
        // Check authorization - teacher can only answer questions from their courses
        $userCourses = Auth::user()->courses()->pluck('id');
        if (!$userCourses->contains($question->course_id)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'answer_text' => 'required|string|min:10',
        ]);

        $question->update([
            'answer_text' => $validated['answer_text'],
            'status' => 'answered',
            'teacher_id' => Auth::id(),
            'answered_at' => now(),
        ]);

        return redirect(route('teacher.questions.manage'))->with('success', 'تم الرد على السؤال بنجاح');
    }

    public function editCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        return view('teacher.edit-course', compact('course'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
        ]);

        $course->update($validated);
        return redirect()->route('teacher.show', $course)->with('success', 'تم تحديث المسار');
    }

    public function deleteCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $course->delete();
        return redirect()->route('teacher.courses')->with('success', 'تم حذف المسار بنجاح');
    }

    public function addLesson(Request $request, Course $course)
    {
        // Check ownership
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable', // Don't validate as file - will be uploaded via AJAX
            'video_file_path' => 'nullable|string', // Path from AJAX upload
            'audio_file' => 'nullable', // Don't validate as file - will be uploaded via AJAX
            'audio_file_path' => 'nullable|string', // Path from AJAX upload
            'content' => 'nullable|string',
            'duration' => 'nullable|string|regex:/^(\d{1,3}:\d{2})?$/',
            'lesson_type' => 'required|in:video-upload,audio-upload,youtube,other-content',
            'order' => 'required|integer|min:1',
        ]);

        // Enforce dynamic lesson order on create (server-side source of truth)
        $validated['order'] = ((int) $course->lessons()->max('order')) + 1;

        // Clear content fields that don't match the selected lesson type
        $lessonType = $validated['lesson_type'];

        if ($lessonType !== 'youtube') {
            $validated['video_url'] = null;
        }

        if ($lessonType !== 'video-upload') {
            $validated['video_file'] = null;
            $validated['video_file_path'] = null;
        } else {
            // AJAX uploaded path is the only valid source
            if (!$validated['video_file_path']) {
                return back()->withErrors(['video_file' => 'يجب رفع ملف فيديو'])->withInput();
            }
            $validated['video_file'] = $validated['video_file_path'];
        }

        if ($lessonType !== 'audio-upload') {
            $validated['audio_file'] = null;
            $validated['audio_file_path'] = null;
        } else {
            // AJAX uploaded path is the only valid source
            if (!$validated['audio_file_path']) {
                return back()->withErrors(['audio_file' => 'يجب رفع ملف صوتي'])->withInput();
            }
            $validated['audio_file'] = $validated['audio_file_path'];
        }

        if ($lessonType !== 'other-content') {
            $validated['content'] = null;
        }

        // Remove helper fields
        unset($validated['video_file_path']);
        unset($validated['audio_file_path']);

        $lesson = $course->lessons()->create($validated);

        $students = $course->enrolledStudents()->get();
        if ($students->isNotEmpty()) {
            $this->notifyStudents(
                $students,
                'درس جديد متاح',
                "تم إضافة الدرس \"{$lesson->name}\" في المسار \"{$course->name}\". يمكنك مشاهدته الآن.",
                route('student.lesson.show', $lesson),
                'system',
                'ri-play-circle-line'
            );
        }

        return redirect()->route('teacher.show', $course)->with('success', 'تم إضافة الدرس بنجاح مع المحتوى المرفوع');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable', // Don't validate as file - will be uploaded via AJAX
            'video_file_path' => 'nullable|string', // Path from AJAX upload
            'audio_file' => 'nullable', // Don't validate as file - will be uploaded via AJAX
            'audio_file_path' => 'nullable|string', // Path from AJAX upload
            'content' => 'nullable|string',
            'duration' => 'nullable|string|regex:/^(\d{1,3}:\d{2})?$/',
            'lesson_type' => 'required|in:video-upload,audio-upload,youtube,other-content',
            'order' => 'required|integer|min:1',
        ]);

        // Clear content fields that don't match the selected lesson type
        $lessonType = $validated['lesson_type'];

        // Clear unused content fields based on lesson type
        if ($lessonType !== 'youtube') {
            $validated['video_url'] = null;
        }

        if ($lessonType !== 'video-upload') {
            $validated['video_file'] = null;
            $validated['video_file_path'] = null;
            // Delete old video file if it exists
            $this->mediaStorage->deleteIfExists($lesson->video_file);
        } else {
            // AJAX uploaded path if provided
            if ($validated['video_file_path']) {
                // Delete old file
                $this->mediaStorage->deleteIfExists($lesson->video_file);
                $validated['video_file'] = $validated['video_file_path'];
            } else {
                // Keep existing file if no new upload
                $validated['video_file'] = $lesson->video_file;
            }
        }

        if ($lessonType !== 'audio-upload') {
            $validated['audio_file'] = null;
            $validated['audio_file_path'] = null;
            // Delete old audio file if it exists
            $this->mediaStorage->deleteIfExists($lesson->audio_file);
        } else {
            // AJAX uploaded path if provided
            if ($validated['audio_file_path']) {
                // Delete old file
                $this->mediaStorage->deleteIfExists($lesson->audio_file);
                $validated['audio_file'] = $validated['audio_file_path'];
            } else {
                // Keep existing file if no new upload
                $validated['audio_file'] = $lesson->audio_file;
            }
        }

        if ($lessonType !== 'other-content') {
            $validated['content'] = null;
        }

        // Remove helper fields
        unset($validated['video_file_path']);
        unset($validated['audio_file_path']);

        $lesson->update($validated);
        return redirect()->route('teacher.show', $lesson->course)->with('success', 'تم تحديث الدرس بنجاح.');
    }

    /**
     * Upload lesson file separately (AJAX endpoint)
     * This avoids POST Too Large errors by handling file uploads independently
     */
    public function uploadLessonFile(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate and handle file upload
        $validated = $request->validate([
            'file' => 'required|file|max:524288', // 500MB max
            'type' => 'required|in:video,audio', // video or audio
        ]);

        try {
            $file = $request->file('file');
            $type = $request->input('type');

            // Validate MIME before storage so rejected files never land on disk.
            if ($type === 'video') {
                $validMimes = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm'];
            } else {
                $validMimes = ['audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/aac', 'audio/flac', 'audio/ogg', 'audio/webm'];
            }

            if (!in_array($file->getMimeType(), $validMimes, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported file type.'
                ], 422);
            }

            $stored = $this->mediaStorage->storeUploadedFile(
                $file,
                $type === 'video' ? 'academy/lessons/videos' : 'academy/lessons/audio'
            );
            $path = $stored['path'];

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الملف بنجاح.',
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteLesson(Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $course = $lesson->course;
        $lesson->delete();
        return redirect()->route('teacher.show', $course)->with('success', 'تم حذف الدرس');
    }

    public function createExam(Request $request, Lesson $lesson)
    {
        if ($lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($lesson) {
                    $exists = Exam::where('lesson_id', $lesson->id)
                        ->where('name', $value)
                        ->exists();
                    if ($exists) {
                        $fail('اسم الاختبار مكرر بالفعل في هذا الدرس. الرجاء اختيار اسم آخر.');
                    }
                },
            ],
            'passing_score' => 'required|numeric|min:0|max:100',
            'attempts_allowed' => 'required|integer|min:1',
            'duration' => 'nullable|integer|min:1|max:600',
            'expires_at' => 'nullable|date|after:now',
            'instructions' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);
        $validated['published_at'] = $validated['is_published'] ? now() : null;
        $validated['duration'] = $validated['duration'] ?? 30;
        $exam = $lesson->exams()->create($validated);

        $students = $lesson->course->enrolledStudents()->get();
        if ($validated['is_published'] && $students->isNotEmpty()) {
            $this->notifyStudents(
                $students,
                'اختبار جديد متاح',
                "تم إنشاء الاختبار \"{$exam->name}\" للدروس في المسار \"{$lesson->course->name}\". يمكنك الدخول إليه الآن.",
                route('student.exam.show', $exam),
                'system',
                'ri-file-text-line'
            );
        }

        return redirect()->route('teacher.exams')->with('success', 'تم إنشاء الاختبار بنجاح! يمكنك الآن إضافة الأسئلة.');
    }

    public function toggleExamPublish(Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $exam->is_published = !$exam->is_published;
        $exam->published_at = $exam->is_published ? now() : null;
        $exam->save();

        if ($exam->is_published) {
            $students = $exam->lesson->course->enrolledStudents()->get();
            if ($students->isNotEmpty()) {
                $this->notifyStudents(
                    $students,
                    'تم نشر اختبار جديد',
                    "الاختبار \"{$exam->name}\" أصبح متاحًا الآن في المسار.",
                    route('student.exam.show', $exam),
                    'system',
                    'ri-file-list-line'
                );
            }
        }

        return back()->with('success', $exam->is_published ? 'تم نشر الاختبار بنجاح.' : 'تم إخفاء الاختبار بنجاح.');
    }

    public function deleteExam(Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $exam->delete();
        return redirect()->route('teacher.exams')->with('success', 'تم حذف الاختبار بنجاح');
    }

    public function addQuestion(Request $request, Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Get the next order number dynamically based on question count
        $nextOrder = $exam->questions()->count() + 1;

        $validated = $request->validate([
            'question_text' => 'required|string|min:5',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'video_timestamp' => 'nullable|integer',
            'order' => 'nullable|integer',
            'answers' => 'sometimes|array',
            'answers.*.text' => 'required_with:answers|string|min:1',
            'answers.*.is_correct' => 'sometimes',
        ]);

        // Validate max answers based on question type
        $questionType = $validated['question_type'];
        $answersCount = !empty($validated['answers']) ? count($validated['answers']) : 0;

        $maxAnswers = [
            'true_false' => 2,
            'multiple_choice' => 4,
            'short_answer' => 0,
        ];

        if ($answersCount > $maxAnswers[$questionType]) {
            return back()->withErrors([
                'answers' => "لا يمكن إضافة أكثر من {$maxAnswers[$questionType]} إجابات لأسئلة "
                    . ($questionType === 'true_false' ? 'الصح والخطأ' : 'الاختيار من متعدد')
            ])->withInput();
        }

        // Check for duplicate questions in this exam
        $existingQuestions = $exam->questions()->get();
        foreach ($existingQuestions as $existing) {
            if (strtolower(trim($existing->question_text)) === strtolower(trim($validated['question_text'])) &&
                $existing->question_type === $validated['question_type']) {
                // Check if answers also match
                if ($this->answersMatch($existing->answers, $validated['answers'] ?? [])) {
                    return back()->withErrors([
                        'question_text' => '⚠️ هذا السؤال موجود بالفعل في الاختبار! لا يمكن إضافة نفس السؤال بنفس الإجابات مرتين.'
                    ])->withInput();
                }
            }
        }

        // Use provided order or default to auto-generated
        $order = !empty($validated['order']) ? $validated['order'] : $nextOrder;

        $question = $exam->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'video_timestamp' => $validated['video_timestamp'] ?? null,
            'order' => $order,
        ]);

        // Add answers if provided
        if (!empty($validated['answers'])) {
            $answerOrder = 1;
            foreach ($validated['answers'] as $answer) {
                if (!empty($answer['text'])) {
                    $question->answers()->create([
                        'answer_text' => $answer['text'],
                        'is_correct' => !empty($answer['is_correct']),
                        'order' => $answerOrder++,
                    ]);
                }
            }
        }

        return back()->with('success', 'تم إضافة السؤال والإجابات بنجاح');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        if ($question->exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'question_text' => 'required|string|min:5',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'video_timestamp' => 'nullable|integer',
            'order' => 'required|integer|min:1',
            'answers' => 'sometimes|array',
            'answers.*.text' => 'required_with:answers|string|min:1',
            'answers.*.is_correct' => 'sometimes',
            'answers.*.id' => 'sometimes|integer',
        ]);

        // Validate max answers based on question type
        $questionType = $validated['question_type'];
        $answersCount = !empty($validated['answers']) ? count($validated['answers']) : 0;

        $maxAnswers = [
            'true_false' => 2,
            'multiple_choice' => 4,
            'short_answer' => 0,
        ];

        if ($answersCount > $maxAnswers[$questionType]) {
            return back()->withErrors([
                'answers' => "لا يمكن إضافة أكثر من {$maxAnswers[$questionType]} إجابات لأسئلة "
                    . ($questionType === 'true_false' ? 'الصح والخطأ' : 'الاختيار من متعدد')
            ])->withInput();
        }

        // Check for duplicate questions in this exam (excluding current question)
        $existingQuestions = $question->exam->questions()->where('id', '!=', $question->id)->get();
        foreach ($existingQuestions as $existing) {
            if (strtolower(trim($existing->question_text)) === strtolower(trim($validated['question_text'])) &&
                $existing->question_type === $validated['question_type']) {
                // Check if answers also match
                if ($this->answersMatch($existing->answers, $validated['answers'] ?? [])) {
                    return back()->withErrors([
                        'question_text' => '⚠️ يوجد سؤال آخر بنفس المحتوى والإجابات! لا يمكن تحديث السؤال لنفس محتوى سؤال موجود.'
                    ])->withInput();
                }
            }
        }

        $question->update([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'video_timestamp' => $validated['video_timestamp'] ?? null,
            'order' => $validated['order'],
        ]);

        // Update/Create answers if provided
        if (!empty($validated['answers'])) {
            // Delete existing answers if question type changed to short_answer
            if ($validated['question_type'] === 'short_answer') {
                $question->answers()->delete();
            } else {
                // Update existing and create new answers
                $existingIds = [];
                $answerOrder = 1;

                foreach ($validated['answers'] as $answer) {
                    if (!empty($answer['text'])) {
                        if (!empty($answer['id'])) {
                            // Update existing answer
                            $existingAnswer = $question->answers()->find($answer['id']);
                            if ($existingAnswer) {
                                $existingAnswer->update([
                                    'answer_text' => $answer['text'],
                                    'is_correct' => !empty($answer['is_correct']),
                                    'order' => $answerOrder,
                                ]);
                                $existingIds[] = $answer['id'];
                            }
                        } else {
                            // Create new answer
                            $question->answers()->create([
                                'answer_text' => $answer['text'],
                                'is_correct' => !empty($answer['is_correct']),
                                'order' => $answerOrder,
                            ]);
                        }
                        $answerOrder++;
                    }
                }

                // Delete answers not in the update
                $question->answers()->whereNotIn('id', $existingIds)->delete();
            }
        }

        return back()->with('success', 'تم تحديث السؤال والإجابات بنجاح');
    }

    public function deleteQuestion(Question $question)
    {
        if ($question->exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $question->delete();
        return back()->with('success', 'تم حذف السؤال');
    }

    /**
     * Helper function to check if two answer arrays match
     */
    private function answersMatch($existingAnswers, $newAnswers)
    {
        // Convert existing answers to array format for comparison
        $existingArray = $existingAnswers->map(function($answer) {
            return [
                'text' => strtolower(trim($answer->answer_text)),
                'is_correct' => (bool) $answer->is_correct
            ];
        })->toArray();

        // Convert new answers to array format
        $newArray = [];
        if (!empty($newAnswers)) {
            foreach ($newAnswers as $answer) {
                if (!empty($answer['text'])) {
                    $newArray[] = [
                        'text' => strtolower(trim($answer['text'])),
                        'is_correct' => !empty($answer['is_correct'])
                    ];
                }
            }
        }

        // If both are empty, they match
        if (empty($existingArray) && empty($newArray)) {
            return true;
        }

        // If lengths don't match, they're different
        if (count($existingArray) !== count($newArray)) {
            return false;
        }

        // Check if all answers match (order matters)
        for ($i = 0; $i < count($existingArray); $i++) {
            if ($existingArray[$i]['text'] !== $newArray[$i]['text'] ||
                $existingArray[$i]['is_correct'] !== $newArray[$i]['is_correct']) {
                return false;
            }
        }

        return true;
    }

    public function showExamQuestions(Exam $exam)
    {
        if ($exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $questions = $exam->questions()->with('answers')->orderBy('order')->get();
        return view('teacher.exam-questions', compact('exam', 'questions'));
    }

    public function addAnswer(Request $request, Question $question)
    {
        if ($question->exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'answer_text' => 'required|string|min:3',
            'is_correct' => 'required|boolean',
            'explanation' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        $question->answers()->create($validated);
        return back()->with('success', 'تم إضافة الإجابة');
    }

    public function updateAnswer(Request $request, Answer $answer)
    {
        if ($answer->question->exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'answer_text' => 'required|string|min:3',
            'is_correct' => 'required|boolean',
            'explanation' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        $answer->update($validated);
        return back()->with('success', 'تم تحديث الإجابة');
    }

    public function deleteAnswer(Answer $answer)
    {
        if ($answer->question->exam->lesson->course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $answer->delete();
        return back()->with('success', 'تم حذف الإجابة');
    }

    public function analytics()
    {
        $user = Auth::user();
        $courseIds = $user->courses()->pluck('id');
        $lessonIds = Lesson::whereIn('course_id', $courseIds)->pluck('id');

        $progressBase = UserProgress::whereIn('lesson_id', $lessonIds);
        $totalProgressEntries = (clone $progressBase)->count();
        $completedEntries = (clone $progressBase)->where('status', 'completed')->count();
        $averageProgress = round((clone $progressBase)->avg('progress_percentage') ?? 0, 2);
        $completionRatio = $totalProgressEntries ? round($completedEntries / $totalProgressEntries * 100) : 0;
        $studentCount = UserProgress::whereIn('lesson_id', $lessonIds)->distinct('user_id')->count('user_id');

        $courseStats = Course::whereIn('id', $courseIds)
            ->with(['lessons.userProgress'])
            ->get()
            ->map(function ($course) {
                $lessonCount = $course->lessons->count();
                $lessonAttempts = $course->lessons->sum(function ($lesson) {
                    return $lesson->userProgress->count();
                });
                $completedAttempts = $course->lessons->sum(function ($lesson) {
                    return $lesson->userProgress->where('status', 'completed')->count();
                });
                $courseRatio = $lessonAttempts ? round($completedAttempts / $lessonAttempts * 100) : 0;
                return [
                    'name' => $course->name,
                    'lessons' => $lessonCount,
                    'attempts' => $lessonAttempts,
                    'completed' => $completedAttempts,
                    'ratio' => $courseRatio,
                ];
            });

        return view('teacher.analytics', compact(
            'totalProgressEntries',
            'completedEntries',
            'averageProgress',
            'completionRatio',
            'studentCount',
            'courseStats'
        ));
    }

    public function questions()
    {
        $user = Auth::user();
        $questions = Question::whereHas('exam.lesson.course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })
        ->with(['exam.lesson.course', 'answers'])
        ->orderByDesc('created_at')
        ->get();

        return view('teacher.questions', compact('questions'));
    }

    public function exams()
    {
        $user = Auth::user();
        $exams = Exam::whereHas('lesson.course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })
        ->with(['lesson.course', 'questions'])
        ->orderByDesc('created_at')
        ->get();

        return view('teacher.exams', compact('exams'));
    }

    /**
     * عرض نتائج اختبار معين مع جميع محاولات الطلاب
     */
    public function examResults(Exam $exam)
    {
        try {
            $user = Auth::user();

            if (!$exam->lesson || !$exam->lesson->course) {
                return redirect()->route('teacher.exams')
                    ->with('error', 'الاختبار غير مرتبط بمسار صالح.');
            }

            if ($exam->lesson->course->instructor_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            $attempts = \DB::table('exam_attempts')
                ->where('exam_id', $exam->id)
                ->join('users', 'exam_attempts.user_id', '=', 'users.id')
                ->select(
                    'exam_attempts.*',
                    'users.name as student_name',
                    'users.email as student_email',
                    'users.id as student_id'
                )
                ->orderBy('users.name')
                ->orderBy('exam_attempts.attempt_number')
                ->get();

            $totalAttempts = $attempts->count();
            $uniqueStudents = $attempts->pluck('student_id')->unique()->count();
            $passedAttempts = $attempts->where('passed', true)->count();
            $avgScore = $totalAttempts > 0 ? $attempts->avg('score') : 0;
            $avgPercentage = $totalAttempts > 0 ? $attempts->avg('percentage') : 0;
            $highestScore = $totalAttempts > 0 ? $attempts->max('score') : 0;

            $studentStats = [];
            foreach ($attempts as $attempt) {
                $studentId = $attempt->student_id;
                if (!isset($studentStats[$studentId])) {
                    $studentStats[$studentId] = [
                        'name' => $attempt->student_name,
                        'email' => $attempt->student_email,
                        'attempts' => [],
                        'best_score' => 0,
                        'best_percentage' => 0,
                        'passed' => false
                    ];
                }

                $studentStats[$studentId]['attempts'][] = $attempt;
                if ($attempt->score > $studentStats[$studentId]['best_score']) {
                    $studentStats[$studentId]['best_score'] = $attempt->score;
                    $studentStats[$studentId]['best_percentage'] = $attempt->percentage;
                    $studentStats[$studentId]['passed'] = $attempt->passed;
                }
            }

            $passRate = $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0;
            $failedAttempts = $totalAttempts - $passedAttempts;
            $avgDuration = $totalAttempts > 0 ? ($attempts->avg('duration_seconds') ?: 0) : 0;
            $lowestScore = $totalAttempts > 0 ? ($attempts->min('score') ?? 0) : 0;

            $scoreDistribution = ['excellent' => 0, 'good' => 0, 'average' => 0, 'poor' => 0];
            foreach ($attempts as $attempt) {
                $pct = (float) $attempt->percentage;
                if ($pct >= 85) $scoreDistribution['excellent']++;
                elseif ($pct >= 65) $scoreDistribution['good']++;
                elseif ($pct >= 50) $scoreDistribution['average']++;
                else $scoreDistribution['poor']++;
            }

            return view('teacher.exam-results', compact(
                'exam',
                'attempts',
                'totalAttempts',
                'uniqueStudents',
                'passedAttempts',
                'avgScore',
                'avgPercentage',
                'highestScore',
                'studentStats',
                'passRate',
                'failedAttempts',
                'avgDuration',
                'lowestScore',
                'scoreDistribution'
            ));
        } catch (\Exception $e) {
            \Log::error('examResults failed: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('teacher.exams')
                ->with('error', 'حدث خطأ أثناء تحميل إحصائيات الاختبار.');
        }
    }

    public function students()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ الحصول على جميع مسارات المعلم
        $courses = $user->courses()
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        $courseIds = $courses->pluck('id')->toArray();

        // ✅ إذا لم توجد مسارات، أرجع قائمة فارغة
        if (empty($courseIds)) {
            return view('teacher.students', ['students' => collect(), 'courses' => $courses]);
        }

        // ✅ الحصول على جميع الطلاب الموافق عليهم في مسارات المعلم (استخدام raw query لتجنب bug distinct)
        $students = User::where('role', 'student')
            ->where(function ($q) use ($courseIds, $user) {  // ✅ Added $user to use()
                // الطلاب الموافق عليهم في مسارات المعلم
                $q->whereIn('id', function ($subQuery) use ($courseIds) {
                    $subQuery->select('user_id')
                        ->from('course_enrollments')
                        ->whereIn('course_id', $courseIds)
                        ->where('status', 'approved')
                        ->distinct();
                })
                // أو الطلاب المرتبطين بالمعلم مباشرة عبر teacher_id
                ->orWhere('teacher_id', $user->id);
            })
            ->orderBy('name')
            ->get();

        // ✅ إذا لم يوجد أي طلاب
        if ($students->isEmpty()) {
            return view('teacher.students', ['students' => collect(), 'courses' => $courses]);
        }

        // ✅ حساب بيانات كل طالب
        $students = $students->map(function ($student) use ($courseIds) {
            $studentCourseIds = \Illuminate\Support\Facades\DB::table('course_enrollments')
                ->where('user_id', $student->id)
                ->whereIn('course_id', $courseIds)
                ->where('status', 'approved')
                ->pluck('course_id')
                ->toArray();

            // ✅ حساب نسبة الإنجاز بكفاءة
            $totalLessons = \App\Models\Lesson::whereIn('course_id', $studentCourseIds)->count();
            $completedLessons = \App\Models\UserProgress::where('user_id', $student->id)
                ->whereIn('lesson_id', function ($q) use ($studentCourseIds) {
                    $q->select('id')->from('lessons')->whereIn('course_id', $studentCourseIds);
                })
                ->where('status', 'completed')
                ->count();

            $student->completion_percentage = $totalLessons ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
            $student->completed_count = $completedLessons;
            $student->total_count = $totalLessons;
            $student->course_ids = array_map('strval', $studentCourseIds);

            // ✅ إضافة معلومات Streak لكل طالب
            $streakService = new StreakService();
            $streakData = $streakService->getStreakData($student);
            $student->current_streak = !empty($streakData['current_streak']) ? $streakData['current_streak'] : 0;
            $student->longest_streak = !empty($streakData['longest_streak']) ? $streakData['longest_streak'] : 0;
            $student->student_points = $student->points ?? 0;

            // Check if student is currently online (active session within last 5 minutes)
            $activeSession = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $student->id)
                ->where('last_activity', '>', now()->subMinutes(5)->timestamp)
                ->first();

            $student->is_online = $activeSession ? true : false;

            // Get last activity time
            $lastSession = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $student->id)
                ->orderBy('last_activity', 'desc')
                ->first();

            if ($lastSession) {
                $student->last_activity_timestamp = $lastSession->last_activity;
                $student->last_activity_readable = $this->formatActivityTime($lastSession->last_activity);
            } else {
                $student->last_activity_readable = 'لم يسجل';
            }

            return $student;
        });

        return view('teacher.students', compact('students', 'courses'));
    }

    private function formatActivityTime($timestamp)
    {
        $activityTime = \Carbon\Carbon::createFromTimestamp($timestamp);
        $now = now();
        $diffSeconds = max(0, $now->timestamp - $activityTime->timestamp);

        if ($diffSeconds < 1) {
            return 'نشط الآن';
        }

        if ($diffSeconds < 60) {
            return "آخر ظهور قبل {$diffSeconds} ثانية";
        }

        if ($diffSeconds < 3600) {
            $minutes = $now->diffInMinutes($activityTime);
            return "آخر ظهور قبل {$minutes} دقيقة";
        }

        if ($diffSeconds <= 10800) {
            $hours = $now->diffInHours($activityTime);
            return "آخر ظهور قبل {$hours} ساعة";
        }

        if ($activityTime->isToday()) {
            return "آخر ظهور اليوم في " . $activityTime->format('H:i');
        }

        if ($activityTime->isYesterday()) {
            return "آخر ظهور أمس في " . $activityTime->format('H:i');
        }

        $daysAgo = $now->diffInDays($activityTime);
        if ($daysAgo < 7) {
            return "آخر ظهور منذ {$daysAgo} يوم";
        }

        return "آخر ظهور في " . $activityTime->format('d/m/Y H:i');
    }
    public function showStudentProfile($student)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Get all course IDs for this teacher
            $courseIds = $user->courses()->pluck('id')->toArray();

            // Get the student
            $student = \App\Models\User::where('id', $student)
                ->where('role', 'student')
                ->firstOrFail();

            // IDOR guard: a teacher may only view profiles of students enrolled
            // or pending in one of their own courses. Prevents enumerating
            // arbitrary student records by guessing IDs.
            $isOwnStudent = ($student->teacher_id === $user->id) || CourseEnrollment::where('user_id', $student->id)
                ->whereIn('course_id', $courseIds)
                ->exists();

            if (!$isOwnStudent) {
                abort(403, 'لا تملك الصلاحية لعرض ملف هذا الطالب.');
            }

            // Resolve presence using sessions first (more accurate), then fallback to cache.
            $lastSession = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $student->id)
                ->orderBy('last_activity', 'desc')
                ->first();

            $lastActivityTimestamp = $lastSession?->last_activity ? (int) $lastSession->last_activity : null;
            $isOnline = $lastActivityTimestamp
                ? $lastActivityTimestamp > now()->subMinutes(5)->timestamp
                : false;

            if (!$lastActivityTimestamp) {
                $lastActivityTimestamp = $this->getLastActivityTimestamp($student);
            }

            if (!$isOnline) {
                $isOnline = $this->isUserOnline($student);
            }

            $lastSeenText = $isOnline
                ? 'متصل الآن'
                : $this->formatMessagingActivityTime($lastActivityTimestamp ?: optional($student->updated_at)->timestamp);

            // Get student's progress in teacher's courses
            $studentProgress = \App\Models\UserProgress::where('user_id', $student->id)
                ->whereHas('lesson', function ($q) use ($courseIds) {
                    $q->whereIn('course_id', $courseIds);
                })
                ->with('lesson.course')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get exam attempts data (from user progress records for exams)
            $examAttempts = \App\Models\UserProgress::where('user_id', $student->id)
                ->whereHas('lesson', function ($q) use ($courseIds) {
                    $q->whereIn('course_id', $courseIds)
                        ->whereHas('exams');
                })
                ->with('lesson')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get actual enrolled and approved course IDs for this student from the teacher's courses
            $enrolledCourseIds = \App\Models\CourseEnrollment::where('user_id', $student->id)
                ->where('status', 'approved')
                ->whereIn('course_id', $courseIds)
                ->pluck('course_id')->toArray();

            // Calculate statistics
            $totalLessons = \App\Models\Lesson::whereIn('course_id', $enrolledCourseIds)->count();
            $completedLessons = $studentProgress->where('status', 'completed')->count();
            $completionPercentage = $totalLessons ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

            // Get enrolled courses with lessons
            $enrolledCourses = \App\Models\Course::whereIn('id', $enrolledCourseIds)
                ->with(['lessons' => function ($q) use ($student) {
                    $q->with(['userProgress' => function ($qq) use ($student) {
                        $qq->where('user_id', $student->id);
                    }]);
                }])
                ->get();

            // Calculate average completion percentage
            $averageScore = 0;
            if ($enrolledCourses->count() > 0) {
                $total = 0;
                foreach ($enrolledCourses as $course) {
                    $courseProgress = $studentProgress->filter(function ($item) use ($course) {
                        return $item->lesson->course_id == $course->id;
                    });
                    if ($courseProgress->count() > 0) {
                        $total += $courseProgress->avg('progress_percentage');
                    }
                }
                $averageScore = $enrolledCourses->count() > 0 ? round($total / $enrolledCourses->count(), 2) : 0;
            }

            // Get streak (consecutive days with activity)
            $streak = $this->calculateStudentStreak($student->id, $courseIds);

            // Get registration date
            $registrationDate = $student->created_at;

            // Get last activity
            $lastActivity = $studentProgress->first()?->updated_at ?? $student->updated_at ?? null;

            return view('teacher.student-profile', compact(
                'student',
                'studentProgress',
                'examAttempts',
                'completionPercentage',
                'completedLessons',
                'totalLessons',
                'enrolledCourses',
                'averageScore',
                'streak',
                'registrationDate',
                'lastActivity',
                'isOnline',
                'lastSeenText'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'الطالب غير موجود');
        }
    }

    private function isUserOnline(User $user): bool
    {
        return Cache::has('user-is-online-' . $user->id);
    }

    private function getLastActivityTimestamp(User $user): ?int
    {
        $lastActivity = Cache::get('last-activity-' . $user->id);
        if (!$lastActivity) {
            return null;
        }

        if ($lastActivity instanceof \Carbon\Carbon) {
            return $lastActivity->timestamp;
        }

        return \Carbon\Carbon::parse($lastActivity)->timestamp;
    }

    private function formatMessagingActivityTime(?int $timestamp): string
    {
        if (!$timestamp) {
            return 'غير متاح';
        }

        $activityTime = \Carbon\Carbon::createFromTimestamp($timestamp);
        $diffInSeconds = max(0, now()->timestamp - $activityTime->timestamp);

        if ($diffInSeconds < 1) {
            return 'الآن';
        }

        if ($diffInSeconds < 60) {
            return 'منذ ' . $diffInSeconds . ' ثانية';
        }

        if ($diffInSeconds < 3600) {
            return 'منذ ' . floor($diffInSeconds / 60) . ' دقيقة';
        }

        if ($diffInSeconds < 86400) {
            return 'منذ ' . floor($diffInSeconds / 3600) . ' ساعة';
        }

        if ($diffInSeconds < 172800) {
            return 'أمس';
        }

        if ($diffInSeconds < 604800) {
            return 'منذ ' . floor($diffInSeconds / 86400) . ' أيام';
        }

        return $activityTime->copy()->setTimezone('Asia/Riyadh')->format('Y-m-d H:i');
    }
    private function calculateStudentStreak($studentId, $courseIds)
    {
        $progress = \App\Models\UserProgress::where('user_id', $studentId)
            ->whereHas('lesson', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->toArray();

        if (empty($progress)) {
            return 0;
        }

        $streak = 1;
        $today = \Carbon\Carbon::today();
        $lastDate = \Carbon\Carbon::parse($progress[0])->startOfDay();

        if ($lastDate < $today) {
            return 0; // No activity today or yesterday
        }

        for ($i = 1; $i < count($progress); $i++) {
            $currentDate = \Carbon\Carbon::parse($progress[$i])->startOfDay();
            $expectedDate = $lastDate->copy()->subDay();

            if ($currentDate->eq($expectedDate)) {
                $streak++;
                $lastDate = $currentDate;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get YouTube video duration
     * استخراج مدة فيديو YouTube
     */
    public function getYouTubeDuration(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');

        // Extract video ID from YouTube URL - supports all formats
        // https://youtube.com/watch?v=xxxx
        // https://www.youtube.com/watch?v=xxxx
        // https://youtu.be/xxxx
        // https://www.youtube.com/embed/xxxx
        // https://youtube.com/watch?v=xxxx&t=10s (with timestamps)
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $url, $matches)) {
            $videoId = $matches[1];
        } else {
            return response()->json([
                'success' => false,
                'error' => 'رابط YouTube غير صالح. استخدم رابطًا مباشرًا مثل:\n- https://youtube.com/watch?v=xxxxx\n- https://youtu.be/xxxxx\n- https://www.youtube.com/embed/xxxxx'
            ], 400);
        }

        try {
            // Try to get duration using youtube-dl if available
            $durationSeconds = $this->extractYouTubeDurationWithYoutubeDl($videoId);

            if ($durationSeconds === null) {
                // Fallback: try with direct API call
                $durationSeconds = $this->extractYouTubeDurationViaAPI($videoId);
            }

            if ($durationSeconds === null) {
                return response()->json([
                    'success' => false,
                    'error' => 'تعذر استخراج مدة فيديو YouTube تلقائيًا.',
                    'hint' => 'تأكد من أن الرابط صحيح ومتاح للعامة، أو أدخل المدة يدويًا.'
                ], 400);
            }

            // Convert seconds to MM:SS format
            $durationFormatted = $this->formatDuration($durationSeconds);

            return response()->json([
                'success' => true,
                'duration' => $durationFormatted,
                'durationSeconds' => $durationSeconds,
                'message' => "تم استخراج مدة الفيديو بنجاح: {$durationFormatted}"
            ]);

        } catch (\Exception $e) {
            \Log::error('YouTube duration extraction failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'حدث خطأ أثناء قراءة بيانات الفيديو'
            ], 500);
        }
    }

    /**
     * Try to extract duration using youtube-dl command
     */
    private function extractYouTubeDurationWithYoutubeDl($videoId)
    {
        try {
            // Check if youtube-dl or yt-dlp is installed
            $command = $this->getYoutubeDlCommand();
            if (!$command) {
                return null;
            }

            $url = "https://www.youtube.com/watch?v={$videoId}";
            $output = [];
            $returnVar = 0;

            exec("{$command} --dump-json -q " . escapeshellarg($url), $output, $returnVar);

            if ($returnVar === 0 && !empty($output)) {
                $json = json_decode(implode('', $output), true);
                if (isset($json['duration']) && is_numeric($json['duration'])) {
                    return (int)$json['duration']; // Return duration in seconds
                }
            }
        } catch (\Exception $e) {
            // Silently fail and try next method
        }

        return null;
    }

    /**
     * Get the correct youtube-dl command
     */
    private function getYoutubeDlCommand()
    {
        // Determine command based on OS
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            // Try yt-dlp first on Windows
            $output1 = shell_exec('where yt-dlp 2>nul');
            if (!empty($output1)) {
                return 'yt-dlp';
            }

            // Try youtube-dl on Windows
            $output2 = shell_exec('where youtube-dl 2>nul');
            if (!empty($output2)) {
                return 'youtube-dl';
            }
        } else {
            // Try yt-dlp first on Linux/Mac
            $output1 = shell_exec('which yt-dlp 2>/dev/null');
            if (!empty($output1)) {
                return 'yt-dlp';
            }

            // Try youtube-dl on Linux/Mac
            $output2 = shell_exec('which youtube-dl 2>/dev/null');
            if (!empty($output2)) {
                return 'youtube-dl';
            }
        }

        return null;
    }

    /**
     * Try to extract duration via APIs
     */
    private function extractYouTubeDurationViaAPI($videoId)
    {
        try {
            // Try YouTube innertube API (most reliable without API key)
            $duration = $this->extractDurationViaInnertube($videoId);
            if ($duration !== null) {
                return $duration;
            }

            // Fallback to oEmbed API
            $duration = $this->extractDurationViaOEmbed($videoId);
            if ($duration !== null) {
                return $duration;
            }

            // Last resort: return a default duration (15 minutes = 900 seconds as estimate)
            return 900;

        } catch (\Exception $e) {
            // Silently fail and return default
            return 900;
        }
    }

    /**
     * Format duration from seconds to MM:SS format
     */
    private function formatDuration($seconds)
    {
        $seconds = intval($seconds);
        $minutes = intdiv($seconds, 60);
        $secs = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * Extract duration via YouTube innertube API (most reliable, no key needed)
     */
    private function extractDurationViaInnertube($videoId)
    {
        try {
            $ch = curl_init();
            $url = "https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO90d0o_cimZSvsC8m5HTLKLm-a53LsNw";

            $postData = json_encode([
                "videoId" => $videoId,
                "context" => [
                    "client" => [
                        "clientName" => "WEB",
                        "clientVersion" => "2.20220801.00.00"
                    ]
                ]
            ]);

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);

                // Try to extract duration from videoDetails
                if (isset($data['videoDetails']['lengthSeconds']) && is_numeric($data['videoDetails']['lengthSeconds'])) {
                    $seconds = intval($data['videoDetails']['lengthSeconds']);
                    return $seconds; // Return duration in seconds
                }
            }
        } catch (\Exception $e) {
            // Silently fail and try next method
        }

        return null;
    }

    /**
     * Extract duration via oEmbed API (fallback)
     */
    private function extractDurationViaOEmbed($videoId)
    {
        try {
            // Use YouTube's official oEmbed endpoint
            $url = "https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$videoId}&format=json";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // oEmbed API doesn't return duration directly, but validates video exists
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                // Video exists and is accessible - return reasonable estimate in seconds
                return 600; // 10 minutes (600 seconds) default for valid videos
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }

    /**
     * عرض طلبات الالتحاق بالمسارات
     */
    public function enrollmentRequests()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // الحصول على جميع طلبات الالتحاق في مسارات هذا المعلم
        $enrollments = CourseEnrollment::whereHas('course', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })
        ->with(['student', 'course'])
        ->orderByDesc('created_at')
        ->get();

        // تصنيف حسب الحالة
        $pending = $enrollments->where('status', 'pending');
        $approved = $enrollments->where('status', 'approved');
        $rejected = $enrollments->where('status', 'rejected');

        return view('teacher.enrollment-requests', compact('pending', 'approved', 'rejected'));
    }

    /**
     * قبول طلب التحاق
     */
    public function approveEnrollment(CourseEnrollment $enrollment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن المسار يتبع لهذا المعلم
        if ($enrollment->course->instructor_id !== $user->id) {
            abort(403, 'لا تملك الصلاحية لقبول هذا الطلب');
        }

        // ✅ التحقق من أن الطالب موجود
        if (!$enrollment->student) {
            return redirect()->back()
                ->with('error', 'الطالب غير موجود');
        }

        // ✅ تحديث حالة الالتحاق
        $updated = $enrollment->update([
            'status' => 'approved',
            'enrolled_at' => now()
        ]);

        if ($updated) {
            $student = $enrollment->student;
            if ($student && ($student->notify_enrollment ?? true)) {
                $student->notify(new AppNotification(
                    'تم قبول طلبك',
                    "طلبك في المسار \"{$enrollment->course->name}\" تمت الموافقة عليه الآن.",
                    route('student.course.show', $enrollment->course->id),
                    'enrollment',
                    'ri-check-line'
                ));
            }

            return redirect()->back()
                ->with('success', "✅ تم قبول طلب التحاق {$enrollment->student->name} بنجاح. يمكنه الآن الوصول إلى المسار.");
        } else {
            return redirect()->back()
                ->with('error', 'حدث خطأ في تحديث الطلب. يرجى المحاولة لاحقاً');
        }
    }

    /**
     * رفض طلب التحاق
     */
    public function rejectEnrollment(Request $request, CourseEnrollment $enrollment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن المسار يتبع لهذا المعلم
        if ($enrollment->course->instructor_id !== $user->id) {
            abort(403, 'لا تملك الصلاحية لرفض هذا الطلب');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $updated = $enrollment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null
        ]);

        if ($updated) {
            return redirect()->back()
                ->with('success', "✅ تم رفض طلب التحاق {$enrollment->student->name}. يمكنه إعادة التقديم لاحقاً.");
        } else {
            return redirect()->back()
                ->with('error', 'حدث خطأ في تحديث الطلب. يرجى المحاولة لاحقاً');
        }
    }

    /**
     * إزالة طالب من مسار مقبول
     */
    public function removeEnrolledStudent(CourseEnrollment $enrollment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ✅ التحقق من أن المسار يتبع لهذا المعلم
        if ($enrollment->course->instructor_id !== $user->id) {
            abort(403, 'لا تملك الصلاحية لإزالة هذا الطالب');
        }

        // ✅ التحقق من أن الطالب موجود
        if (!$enrollment->student) {
            return redirect()->back()
                ->with('error', 'الطالب غير موجود');
        }

        $studentName = $enrollment->student->name;
        $courseName = $enrollment->course->name;

        // ✅ حذف نهائي (forceDelete) بدلاً من soft delete للسماح بإعادة التسجيل
        $deleted = $enrollment->forceDelete();

        if ($deleted) {
            return redirect()->back()
                ->with('success', "✅ تم إزالة {$studentName} من مسار '{$courseName}'. يمكنه إعادة التقديم إذا أراد.");
        } else {
            return redirect()->back()
                ->with('error', 'حدث خطأ في إزالة الطالب. يرجى المحاولة لاحقاً');
        }
    }

    /**
     * Logout teacher
     */
    public function teacherLogout(Request $request)
    {
        $teacherId = Auth::id();
        if ($teacherId) {
            \Illuminate\Support\Facades\Cache::forget('user-is-online-' . $teacherId);
            \Illuminate\Support\Facades\Cache::put('last-activity-' . $teacherId, now(), now()->addDays(7));
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $teacherId)->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}





