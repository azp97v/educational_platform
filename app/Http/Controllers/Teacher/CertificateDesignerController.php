<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificatePdfJob;
use App\Mail\CertificateMail;
    use App\Models\Certificate;
    use App\Models\Certificates\CertificateStudent;
    use App\Models\Certificates\CustomTemplate;
    use App\Models\PdfGeneration;
    use App\Models\Course;
    use App\Models\User;
    use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class CertificateDesignerController extends Controller
{
    // ─── My Templates Overview (no student required) ─────────────

    public function myTemplates(Request $request)
    {
        $templates = CustomTemplate::where('user_id', auth()->id())
            ->with('certificateStudent')
            ->latest()
            ->paginate(12);

        $students = CertificateStudent::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('teacher.certificates.my-templates', compact('templates', 'students'));
    }

    // ─── Student CRUD ───────────────────────────────────────────

    public function students(Request $request)
    {
        $query = CertificateStudent::where('user_id', auth()->id());

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        // Filter by certificate status
        if ($request->filled('cert_status')) {
            if ($request->cert_status === 'has') {
                $studentIds = CertificateStudent::where('user_id', auth()->id())
                    ->whereHas('customTemplates', function ($q) {
                        $q->where('user_id', auth()->id());
                    })->pluck('id');
                $query->whereIn('id', $studentIds);
            } elseif ($request->cert_status === 'none') {
                $studentIds = CertificateStudent::where('user_id', auth()->id())
                    ->whereDoesntHave('customTemplates', function ($q) {
                        $q->where('user_id', auth()->id());
                    })->pluck('id');
                $query->whereIn('id', $studentIds);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        if (in_array($sortField, ['name', 'email', 'course', 'course_date', 'degree', 'created_at'])) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $students = $query->paginate(20)->withQueryString();
        $courses = auth()->user()->courses()->where('status', 'published')->pluck('name', 'id');
        $allCourses = CertificateStudent::where('user_id', auth()->id())
            ->select('course')->distinct()->pluck('course');

        return view('teacher.certificates.students', compact('students', 'courses', 'allCourses'));
    }

    public function createStudent()
    {
        $systemUsers = User::where('role', 'student')
            ->where(function ($q) {
                $q->where('teacher_id', auth()->id())
                  ->orWhereHas('enrollments', function ($e) {
                      $e->whereIn('course_id', auth()->user()->courses()->pluck('id'));
                  });
            })->orderBy('name')->get(['id', 'name', 'email', 'username', 'avatar_url']);

        $courses = auth()->user()->courses()
            ->where('status', 'published')
            ->orderBy('name')
            ->get(['id', 'name']);

        $addedEntries = CertificateStudent::where('user_id', auth()->id())
            ->pluck('email')
            ->map(fn ($email) => strtolower($email))
            ->values();

        return view('teacher.certificates.student-form', compact('systemUsers', 'courses', 'addedEntries'));
    }

    public function editStudent($id)
    {
        $student = CertificateStudent::where('user_id', auth()->id())->findOrFail($id);

        $systemUsers = User::where('role', 'student')
            ->where(function ($q) {
                $q->where('teacher_id', auth()->id())
                  ->orWhereHas('enrollments', function ($e) {
                      $e->whereIn('course_id', auth()->user()->courses()->pluck('id'));
                  });
            })->orderBy('name')->get(['id', 'name', 'email', 'username', 'avatar_url']);

        $courses = auth()->user()->courses()
            ->where('status', 'published')
            ->orderBy('name')
            ->get(['id', 'name']);

        $addedEntries = CertificateStudent::where('user_id', auth()->id())
            ->where('id', '!=', $student->id)
            ->pluck('email')
            ->map(fn ($email) => strtolower($email))
            ->values();

        return view('teacher.certificates.student-form', compact('student', 'systemUsers', 'courses', 'addedEntries'));
    }

    public function studentProfile(CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);

        // Find the actual system user by email (if exists)
        $systemUser = User::where('email', $student->email)->where('role', 'student')->first();

        // Courses the system user is enrolled in (approved)
        $enrolledCourses = collect();
        $systemCertificates = collect();
        if ($systemUser) {
            $enrolledCourses = Course::whereIn('id', $systemUser->enrollments()
                ->where('status', 'approved')
                ->pluck('course_id'))
                ->withCount('lessons')
                ->get();

            $systemCertificates = Certificate::where('user_id', $systemUser->id)
                ->with('course')
                ->get();
        }

        // All certificate designer templates for this student (only issued)
        $certTemplates = CustomTemplate::where('user_id', auth()->id())
            ->where('recipient_name', $student->name)
            ->where('is_issued', true)
            ->latest()
            ->get();

        // Courses completed (all lessons done) but no system certificate yet
        $completedNoCert = collect();
        if ($systemUser) {
            $completedCourseIds = $systemCertificates->pluck('course_id')->toArray();
            foreach ($enrolledCourses as $course) {
                $totalLessons = $course->lessons_count;
                $completedLessons = $systemUser->progress()
                    ->whereHas('lesson', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })->where('status', 'completed')->count();

                if ($totalLessons > 0 && $completedLessons >= $totalLessons && !in_array($course->id, $completedCourseIds)) {
                    $completedNoCert->push($course);
                }
            }
        }

        // Count stats
        $totalCerts = $certTemplates->count() + $systemCertificates->count();
        $pendingCerts = $completedNoCert->count();
        $totalCourses = $enrolledCourses->count();
        $completionRate = $totalCourses > 0
            ? round(($systemCertificates->count() / $totalCourses) * 100)
            : 0;

        return view('teacher.certificates.student-profile', compact(
            'student', 'systemUser', 'enrolledCourses', 'systemCertificates',
            'certTemplates', 'completedNoCert', 'totalCerts', 'pendingCerts',
            'totalCourses', 'completionRate'
        ));
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', function ($attribute, $value, $fail) {
                if (!empty($value)) {
                    if (User::where('username', $value)->exists()) {
                        $fail('اسم المستخدم مُستخدم بالفعل في النظام.');
                    } elseif (CertificateStudent::where('username', $value)->where('user_id', auth()->id())->exists()) {
                        $fail('اسم المستخدم مُستخدم بالفعل لأحد المستفيدين.');
                    }
                }
            }],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                $duplicate = CertificateStudent::where('user_id', auth()->id())
                    ->whereRaw('LOWER(email) = ?', [strtolower($value)])
                    ->exists();
                if ($duplicate) {
                    $fail('هذا المستفيد مُضاف بالفعل.');
                }
            }],
            'course' => 'nullable|string|max:255',
            'course_date' => 'required|date',
            'degree' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('certificate_students', 'public');
        }

        CertificateStudent::create([
            'user_id' => auth()->id(),
            'recipient_user_id' => $this->resolveRecipientUserId($validated['email']),
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'course' => $validated['course'],
            'course_date' => $validated['course_date'],
            'degree' => $validated['degree'],
            'image' => $imagePath,
        ]);

        return redirect()->route('teacher.certificates.students')
            ->with('success', 'تم إضافة الطالب بنجاح');
    }

    /**
     * يطابق بريد المستفيد بحساب طالب حقيقي مسجّل في النظام (نفس منطق المطابقة
     * المستخدم في صفحة ملف الطالب وقائمة المستفيدين، محفوظاً هنا في عمود حقيقي).
     */
    private function resolveRecipientUserId(string $email): ?int
    {
        return User::whereRaw('LOWER(email) = ?', [strtolower($email)])->value('id');
    }

    public function updateStudent(Request $request, $id)
    {
        $student = CertificateStudent::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/', function ($attribute, $value, $fail) use ($student) {
                if (!empty($value) && strtolower($value) !== strtolower($student->username ?? '')) {
                    if (User::where('username', $value)->exists()) {
                        $fail('اسم المستخدم مُستخدم بالفعل في النظام.');
                    } elseif (CertificateStudent::where('username', $value)->where('user_id', auth()->id())->where('id', '!=', $student->id)->exists()) {
                        $fail('اسم المستخدم مُستخدم بالفعل لأحد المستفيدين.');
                    }
                }
            }],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) use ($student) {
                $duplicate = CertificateStudent::where('user_id', auth()->id())
                    ->where('id', '!=', $student->id)
                    ->whereRaw('LOWER(email) = ?', [strtolower($value)])
                    ->exists();
                if ($duplicate) {
                    $fail('هذا المستفيد مُضاف بالفعل.');
                }
            }],
            'course' => 'nullable|string',
            'course_date' => 'required|date',
            'degree' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($student->image && Storage::disk('public')->exists($student->image)) {
                Storage::disk('public')->delete($student->image);
            }
            $validated['image'] = $request->file('image')->store('certificate_students', 'public');
        } elseif ($request->has('remove_image') && $student->image) {
            if (Storage::disk('public')->exists($student->image)) {
                Storage::disk('public')->delete($student->image);
            }
            $validated['image'] = null;
        }

        if (strtolower($validated['email']) !== strtolower($student->email)) {
            $validated['recipient_user_id'] = $this->resolveRecipientUserId($validated['email']);
        }

        $student->update($validated);
        return redirect()->route('teacher.certificates.students')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    /**
     * إشعار حقيقي للطالب المستلم عند إصدار/إنشاء شهادة فعلية له (قالب مخصص أو بريد).
     */
    private function notifyRecipientOfCertificate(CertificateStudent $student): void
    {
        if (!$student->recipient_user_id) {
            return;
        }

        $recipient = $student->recipient;
        if (!$recipient || !($recipient->notify_certificate ?? true)) {
            return;
        }

        $recipient->notify(new \App\Notifications\AppNotification(
            'تم إصدار شهادة جديدة لك',
            "أصدر لك معلمك شهادة إتمام لدورة \"{$student->course}\".",
            route('teacher.certificates.gallery', $student),
            'certificate',
            'ri-award-line'
        ));
    }

    public function destroyStudent($id)
    {
        $student = CertificateStudent::where('user_id', auth()->id())->findOrFail($id);
        $student->delete();
        return redirect()->route('teacher.certificates.students')
            ->with('success', 'تم حذف الطالب بنجاح');
    }

    // ─── Template Gallery ───────────────────────────────────────

    public function gallery(CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);
        $uploadedTemplates = auth()->user()->customTemplates()->latest()->get();

        // Check if the linked system user has completed the course (null = cannot determine)
        $courseCompleted = null;
        if ($student->recipient_user_id && $student->course) {
            $systemUser = User::find($student->recipient_user_id);
            if ($systemUser) {
                $course = Course::where('name', $student->course)->withCount('lessons')->first();
                if ($course && $course->lessons_count > 0) {
                    $completedLessons = $systemUser->progress()
                        ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
                        ->where('status', 'completed')
                        ->count();
                    $courseCompleted = $completedLessons >= $course->lessons_count;
                }
            }
        }

        return view('teacher.certificates.gallery', compact('student', 'uploadedTemplates', 'courseCompleted'));
    }

    // ─── Preset Preview ─────────────────────────────────────────

    /**
     * يسمح بالوصول للمعلم مالك السجل أو للطالب المستلم الفعلي للشهادة فقط.
     */
    private function canViewCertificateOf(CertificateStudent $student): bool
    {
        $userId = auth()->id();
        return $student->user_id === $userId || $student->recipient_user_id === $userId;
    }

    public function preview($templateNum, CertificateStudent $student)
    {
        abort_if(!$this->canViewCertificateOf($student), 403);

        $imageName = 'qw' . $templateNum . '.jpeg';
        $path = public_path('image/' . $imageName);
        $base64 = '';
        if (file_exists($path)) {
            try {
                $base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
            } catch (\Throwable $e) {
                // File may have been deleted between check and read — serve without background
            }
        }

        return view('teacher.certificates.preview', [
            'student' => $student,
            'backgroundImage' => $base64,
            'templateNum' => $templateNum,
        ]);
    }

    // ─── PDF Download (presets 1-9) ─────────────────────────────

    public function download($templateNum, CertificateStudent $student)
    {
        abort_if(!$this->canViewCertificateOf($student), 403);

        // إنشاء سجل PDF Generation وإطلاق Job في الخلفية
        $token = bin2hex(random_bytes(24)); // 48-char token

        $gen = PdfGeneration::create([
            'token'        => $token,
            'user_id'      => auth()->id(),
            'type'         => 'preset',
            'student_id'   => $student->id,
            'template_num' => (string) $templateNum,
            'status'       => 'pending',
            'expires_at'   => now()->addHours(3),
        ]);

        // أطلق المهمة في طابور الخلفية
        GenerateCertificatePdfJob::dispatch($gen->id);

        Log::info('[PDF-CTRL] Preset job dispatched: gen_id=' . $gen->id . ' token=' . $token);

        // إعادة توجيه لصفحة الانتظار
        return redirect()->route('pdf.wait', ['token' => $token]);
    }

    // ─── Custom Template Upload ─────────────────────────────────

    public function uploadView(CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);
        return view('teacher.certificates.upload-template', compact('student'));
    }

    public function uploadTemplate(Request $request, CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'template_image' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
        ]);

        $templateData = [
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'recipient_name' => $student->name,
            'title' => 'شهادة إتمام',
            'subtitle' => 'تقديراً لجهودكم',
            'body_text' => 'تمت الموافقة على استكمال البرنامج بنجاح وبتقدير عالٍ.',
            'primary_color' => '#4338ca',
            'secondary_color' => '#14b8a6',
            'accent_color' => '#f59e0b',
            'background_type' => 'image',
            'background_position_x' => 50,
            'background_position_y' => 50,
            'background_size' => 100,
            'font_family' => 'Cairo',
            'text_align' => 'center',
            'title_x' => 0, 'title_y' => 0, 'title_size' => 38,
            'subtitle_x' => 0, 'subtitle_y' => 0, 'subtitle_size' => 20,
            'name_x' => 0, 'name_y' => 0, 'name_size' => 32,
            'body_x' => 0, 'body_y' => 0, 'body_size' => 18,
            'logo_x' => 0, 'logo_y' => 0, 'logo_width' => 110,
            'stamp_size' => 120, 'overlay_opacity' => 15, 'border_radius' => 30,
            'show_logo' => true, 'show_stamp' => true,
            'title_color' => '#ffffff', 'subtitle_color' => '#ffffff',
            'name_color' => '#ffffff', 'body_color' => '#ffffff',
            'background_image' => $request->file('template_image')->store('custom_templates', 'public'),
        ];

        auth()->user()->customTemplates()->create($templateData + ['is_issued' => false]);

        return redirect()->route('teacher.certificates.gallery', $student)
            ->with('success', 'تم رفع القالب بنجاح');
    }

    // ─── Custom Template Create/Edit ────────────────────────────

    public function customCreate(CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);
        $templates = auth()->user()->customTemplates()->latest()->take(6)->get();
        $editingTemplate = null;
        $preset = request()->query('preset');
        $presetData = $preset ? $this->presetDefaults($preset) : [];
        return view('teacher.certificates.custom-create', compact('student', 'templates', 'editingTemplate', 'presetData'));
    }

    public function customEdit(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id(), 403);
        $templates = auth()->user()->customTemplates()->latest()->take(6)->get();
        $editingTemplate = $template;
        $presetData = [];
        return view('teacher.certificates.custom-create', compact('student', 'templates', 'editingTemplate', 'presetData'));
    }

    public function customStore(Request $request, CertificateStudent $student)
    {
        abort_if($student->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'recipient_name' => 'nullable|string|max:80',
            'title' => 'required|string|max:80',
            'subtitle' => 'nullable|string|max:120',
            'body_text' => 'nullable|string|max:500',
            'primary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'secondary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'accent_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'background_type' => 'nullable|in:gradient,solid,image',
            'background_position_x' => 'nullable|integer|min:0|max:100',
            'background_position_y' => 'nullable|integer|min:0|max:100',
            'background_size' => 'nullable|integer|min:70|max:220',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'remove_background' => 'nullable|boolean',
            'font_family' => 'nullable|string|max:40',
            'text_align' => 'nullable|in:center,right,left',
            'title_x' => 'nullable|integer',
            'title_y' => 'nullable|integer',
            'title_size' => 'nullable|integer',
            'title_rotation' => 'nullable|integer|min:-180|max:180',
            'subtitle_x' => 'nullable|integer',
            'subtitle_y' => 'nullable|integer',
            'subtitle_size' => 'nullable|integer',
            'subtitle_rotation' => 'nullable|integer|min:-180|max:180',
            'name_x' => 'nullable|integer',
            'name_y' => 'nullable|integer',
            'name_size' => 'nullable|integer',
            'name_rotation' => 'nullable|integer|min:-180|max:180',
            'body_x' => 'nullable|integer',
            'body_y' => 'nullable|integer',
            'body_size' => 'nullable|integer',
            'body_rotation' => 'nullable|integer|min:-180|max:180',
            'logo_x' => 'nullable|integer',
            'logo_y' => 'nullable|integer',
            'logo_width' => 'nullable|integer',
            'logo_rotation' => 'nullable|integer|min:-180|max:180',
            'stamp_size' => 'nullable|integer',
            'overlay_opacity' => 'nullable|integer',
            'border_radius' => 'nullable|integer',
            'show_logo' => 'nullable|boolean',
            'show_stamp' => 'nullable|boolean',
            'title_color' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'name_color' => 'nullable|string|max:20',
            'body_color' => 'nullable|string|max:20',
        ]);

        $templateData = $this->prepareTemplateData($validated, $student);

        if ($request->hasFile('logo_image')) {
            $templateData['logo_image'] = $request->file('logo_image')->store('custom_templates', 'public');
        }
        if ($request->hasFile('background_image')) {
            $templateData['background_image'] = $request->file('background_image')->store('custom_templates', 'public');
        }

        $template = auth()->user()->customTemplates()->create($templateData + ['is_issued' => false]);

        return redirect()->route('teacher.certificates.custom.show', [$student, $template])
            ->with('success', 'تم حفظ القالب بنجاح');
    }

    public function customUpdate(Request $request, CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'recipient_name' => 'nullable|string|max:80',
            'title' => 'required|string|max:80',
            'subtitle' => 'nullable|string|max:120',
            'body_text' => 'nullable|string|max:500',
            'primary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'secondary_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'accent_color' => ['nullable', 'regex:/^#([a-fA-F0-9]{6})$/'],
            'background_type' => 'nullable|in:gradient,solid,image',
            'background_position_x' => 'nullable|integer|min:0|max:100',
            'background_position_y' => 'nullable|integer|min:0|max:100',
            'background_size' => 'nullable|integer|min:70|max:220',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:4096',
            'remove_background' => 'nullable|boolean',
            'font_family' => 'nullable|string|max:40',
            'text_align' => 'nullable|in:center,right,left',
            'title_x' => 'nullable|integer',
            'title_y' => 'nullable|integer',
            'title_size' => 'nullable|integer',
            'title_rotation' => 'nullable|integer|min:-180|max:180',
            'subtitle_x' => 'nullable|integer',
            'subtitle_y' => 'nullable|integer',
            'subtitle_size' => 'nullable|integer',
            'subtitle_rotation' => 'nullable|integer|min:-180|max:180',
            'name_x' => 'nullable|integer',
            'name_y' => 'nullable|integer',
            'name_size' => 'nullable|integer',
            'name_rotation' => 'nullable|integer|min:-180|max:180',
            'body_x' => 'nullable|integer',
            'body_y' => 'nullable|integer',
            'body_size' => 'nullable|integer',
            'body_rotation' => 'nullable|integer|min:-180|max:180',
            'logo_x' => 'nullable|integer',
            'logo_y' => 'nullable|integer',
            'logo_width' => 'nullable|integer',
            'logo_rotation' => 'nullable|integer|min:-180|max:180',
            'stamp_size' => 'nullable|integer',
            'overlay_opacity' => 'nullable|integer',
            'border_radius' => 'nullable|integer',
            'show_logo' => 'nullable|boolean',
            'show_stamp' => 'nullable|boolean',
            'title_color' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'name_color' => 'nullable|string|max:20',
            'body_color' => 'nullable|string|max:20',
        ]);

        $templateData = $this->prepareTemplateData($validated, $student);

        if ($request->hasFile('logo_image')) {
            if ($template->logo_image) {
                Storage::disk('public')->delete($template->logo_image);
            }
            $templateData['logo_image'] = $request->file('logo_image')->store('custom_templates', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            $templateData['background_image'] = $request->file('background_image')->store('custom_templates', 'public');
        } elseif (($validated['remove_background'] ?? false) || ($validated['background_type'] ?? 'gradient') !== 'image') {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            $templateData['background_image'] = null;
        }

        $template->update($templateData);

        return redirect()->route('teacher.certificates.custom.show', [$student, $template])
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    // ─── Custom Template Show/Download/Destroy ──────────────────

    public function customShow(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id() && !$this->canViewCertificateOf($student), 403);

        // Preview never auto-issues — teacher must explicitly click "Issue to Student"
        return view('teacher.certificates.custom-preview', compact('student', 'template'));
    }

    public function customIssue(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id(), 403);

        if (!$template->is_issued) {
            $template->update(['is_issued' => true, 'issued_at' => now()]);
            $this->notifyRecipientOfCertificate($student);
        }

        return redirect()->route('teacher.certificates.custom.show', [$student, $template])
            ->with('success', 'تم إصدار الشهادة للطالب بنجاح.');
    }

    public function customDownload(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id() && !$this->canViewCertificateOf($student), 403);

        // إنشاء سجل PDF Generation وإطلاق Job في الخلفية
        $token = bin2hex(random_bytes(24));

        $gen = PdfGeneration::create([
            'token'       => $token,
            'user_id'     => auth()->id(),
            'type'        => 'custom',
            'student_id'  => $student->id,
            'template_id' => $template->id,
            'status'      => 'pending',
            'expires_at'  => now()->addHours(3),
        ]);

        GenerateCertificatePdfJob::dispatch($gen->id);

        Log::info('[PDF-CTRL] Custom job dispatched: gen_id=' . $gen->id . ' token=' . $token);

        return redirect()->route('pdf.wait', ['token' => $token]);
    }

    public function customDestroy(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($template->user_id !== auth()->id(), 403);

        if ($template->logo_image) {
            Storage::disk('public')->delete($template->logo_image);
        }
        if ($template->background_image) {
            Storage::disk('public')->delete($template->background_image);
        }
        $template->delete();

        return redirect()->route('teacher.certificates.gallery', $student)
            ->with('success', 'تم حذف القالب بنجاح');
    }

    // ─── Username Availability ──────────────────────────────────

    public function checkUsername($username): JsonResponse
    {
        $username = trim($username);
        if (empty($username)) {
            return response()->json(['available' => true]);
        }

        $inUsers = User::where('username', $username)->exists();
        $inStudents = CertificateStudent::where('username', $username)
            ->where('user_id', auth()->id())
            ->exists();

        return response()->json(['available' => !$inUsers && !$inStudents]);
    }

    // ─── Email ──────────────────────────────────────────────────

    public function sendEmail(CertificateStudent $student, $templateNum)
    {
        abort_if($student->user_id !== auth()->id(), 403);

        try {
            $imageName = 'qw' . $templateNum . '.jpeg';
            $imagePath = public_path('image/' . $imageName);
            if (!file_exists($imagePath)) {
                return back()->with('error', 'قالب الشهادة غير موجود');
            }

            $base64Image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath));

            Mail::to($student->email)->send(new \App\Mail\CertificateMail($student, $templateNum, $base64Image));

            return back()->with('success', 'تم إرسال الشهادة إلى البريد (' . $student->email . ') بنجاح');
        } catch (\Exception $e) {
            \Log::error('Certificate email send failed: ' . $e->getMessage());
            return back()->with('error', 'فشل إرسال الشهادة عبر البريد. يرجى المحاولة مرة أخرى.');
        }
    }

    public function sendCustomEmail(CertificateStudent $student, CustomTemplate $template)
    {
        abort_if($student->user_id !== auth()->id(), 403);

        try {
            @ini_set('memory_limit', '256M');
            @set_time_limit(60);

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'tempDir' => sys_get_temp_dir(),
            ]);

            $mpdf->SetDirectionality('rtl');

            $pdfBgImage = null;
            if ($template->background_type === 'image' && $template->background_image) {
                $bgPath = storage_path('app/public/' . $template->background_image);
                if (file_exists($bgPath)) {
                    $ext = pathinfo($bgPath, PATHINFO_EXTENSION);
                    $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
                    $pdfBgImage = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($bgPath));
                }
            }

            $html = view('teacher.certificates.custom-preview', [
                'student' => $student,
                'template' => $template,
                'preview' => false,
                'forPdf' => true,
                'pdfBgImage' => $pdfBgImage,
            ])->render();
            ini_set('pcre.backtrack_limit', '10000000');

            $mpdf->WriteHTML($html);
            $pdfOutput = $mpdf->Output('', 'S');

            Mail::send([], [], function ($message) use ($student, $pdfOutput, $template) {
                $message->to($student->email)
                    ->subject('شهادة إتمام - ' . $template->name)
                    ->attachData($pdfOutput, "certificate_{$student->name}.pdf", ['mime' => 'application/pdf'])
                    ->html(view('teacher.certificates.emails.certificate_notification', ['student' => $student])->render());
            });

            return back()->with('success', 'تم إرسال الشهادة إلى البريد (' . $student->email . ') بنجاح');
        } catch (\Exception $e) {
            \Log::error('Custom certificate email send failed: ' . $e->getMessage());
            return back()->with('error', 'فشل إرسال الشهادة عبر البريد. يرجى المحاولة مرة أخرى.');
        }
    }

    // ─── Bulk Email ─────────────────────────────────────────────

    public function bulkSendEmail(Request $request)
    {
        $request->validate([
            'course'       => 'required|string|max:200',
            'template_num' => 'required|in:1,2,3,4,5',
        ]);

        $students = CertificateStudent::where('user_id', auth()->id())
            ->where('course', $request->course)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'لا يوجد مستفيدون بعناوين بريد في هذا المسار.');
        }

        $imageName = 'qw' . $request->template_num . '.jpeg';
        $imagePath = public_path('image/' . $imageName);
        if (!file_exists($imagePath)) {
            return back()->with('error', 'قالب الشهادة المختار غير موجود.');
        }

        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        $base64Image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath));

        $sent = 0;
        $failed = 0;

        foreach ($students as $student) {
            try {
                Mail::to($student->email)->send(new \App\Mail\CertificateMail($student, $request->template_num, $base64Image));
                $sent++;
            } catch (\Exception $e) {
                Log::error("Bulk certificate email failed for {$student->email}: " . $e->getMessage());
                $failed++;
            }
        }

        $msg = "تم إرسال {$sent} شهادة بنجاح";
        if ($failed > 0) {
            $msg .= " — فشل إرسال {$failed}";
        }

        return back()->with('success', $msg);
    }

    // ─── Preset Defaults ────────────────────────────────────────

    private function presetDefaults($preset): array
    {
        return match ($preset) {
            '1' => ['template_label' => 'القالب الكلاسيكي', 'title' => 'شهادة إتمام', 'subtitle' => 'تقديراً لجهودكم', 'body_text' => 'تمت الموافقة على استكمال البرنامج بنجاح.', 'primary_color' => '#4338ca', 'secondary_color' => '#14b8a6', 'accent_color' => '#f59e0b', 'background_type' => 'gradient', 'font_family' => 'Cairo', 'text_align' => 'center'],
            '2' => ['template_label' => 'القالب العصري', 'title' => 'شهادة مشاركة', 'subtitle' => 'في دورة تدريبية مميزة', 'body_text' => 'تم منح هذه الشهادة تقديراً للمشاركة الفعالة.', 'primary_color' => '#0f766e', 'secondary_color' => '#2dd4bf', 'accent_color' => '#fbbf24', 'background_type' => 'solid', 'font_family' => 'Tajawal', 'text_align' => 'center'],
            '3' => ['template_label' => 'القالب الذهبي', 'title' => 'شهادة تقدير', 'subtitle' => 'لتفوقكم وتميزكم', 'body_text' => 'تم منح هذه الشهادة تقديراً لجهودكم المميزة.', 'primary_color' => '#a16207', 'secondary_color' => '#fde68a', 'accent_color' => '#f59e0b', 'background_type' => 'gradient', 'font_family' => 'Cairo', 'text_align' => 'center'],
            '4' => ['template_label' => 'الدرع الرقمي', 'title' => 'شهادة إنجاز رقمي', 'subtitle' => 'في مجال الابتكار والتقنية', 'body_text' => 'تم منح هذه الشهادة تقديراً لجهودكم المتميزة.', 'primary_color' => '#111827', 'secondary_color' => '#3b82f6', 'accent_color' => '#facc15', 'background_type' => 'solid', 'font_family' => 'Tajawal', 'text_align' => 'center'],
            '5' => ['template_label' => 'القالب الأكاديمي', 'title' => 'شهادة أكاديمية', 'subtitle' => 'بالتفوق والتميز', 'body_text' => 'تم منح هذه الشهادة تقديراً لجهودكم الأكاديمية.', 'primary_color' => '#1d4ed8', 'secondary_color' => '#38bdf8', 'accent_color' => '#f8fafc', 'background_type' => 'gradient', 'font_family' => 'Cairo', 'text_align' => 'center'],
            '6' => ['template_label' => 'الإبداع الهندسي', 'title' => 'شهادة هندسية', 'subtitle' => 'للتفوق في الإبداع الهندسي', 'body_text' => 'تم منح هذه الشهادة تقديراً للتميز الهندسي.', 'primary_color' => '#0f172a', 'secondary_color' => '#64748b', 'accent_color' => '#06b6d4', 'background_type' => 'solid', 'font_family' => 'Tajawal', 'text_align' => 'center'],
            '7' => ['template_label' => 'الوسام المهني', 'title' => 'شهادة احتراف', 'subtitle' => 'في مجال الخبرة والتدريب', 'body_text' => 'تم منح هذه الشهادة تقديراً للخبرة المهنية.', 'primary_color' => '#7c2d12', 'secondary_color' => '#fb923c', 'accent_color' => '#fef3c7', 'background_type' => 'gradient', 'font_family' => 'Cairo', 'text_align' => 'center'],
            '8' => ['template_label' => 'الطراز الأكاديمي', 'title' => 'شهادة تخرج', 'subtitle' => 'للنشاط العلمي والتميز', 'body_text' => 'تم منح هذه الشهادة تقديراً للإنجاز العلمي.', 'primary_color' => '#4c1d95', 'secondary_color' => '#c084fc', 'accent_color' => '#f5f3ff', 'background_type' => 'gradient', 'font_family' => 'Tajawal', 'text_align' => 'center'],
            '9' => ['template_label' => 'مودرن جرافيك', 'title' => 'شهادة إبداع', 'subtitle' => 'في مجال التصميم والابتكار', 'body_text' => 'تم منح هذه الشهادة تقديراً للإبداع والتميز.', 'primary_color' => '#be185d', 'secondary_color' => '#f472b6', 'accent_color' => '#fdf2f8', 'background_type' => 'solid', 'font_family' => 'Tajawal', 'text_align' => 'center'],
            default => ['template_label' => 'قالب مخصص', 'title' => 'شهادة إتمام', 'subtitle' => 'تقديراً لجهودكم', 'body_text' => 'تم إتمام البرنامج بنجاح.', 'primary_color' => '#4338ca', 'secondary_color' => '#14b8a6', 'accent_color' => '#f59e0b', 'background_type' => 'gradient', 'font_family' => 'Cairo', 'text_align' => 'center'],
        };
    }

    private function prepareTemplateData(array $validated, ?CertificateStudent $student = null): array
    {
        return [
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'recipient_name' => $validated['recipient_name'] ?? ($student?->name ?? 'اسم الطالب'),
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? 'تقديراً لجهودكم',
            'body_text' => $validated['body_text'] ?? 'تم إتمام البرنامج بنجاح.',
            'primary_color' => $validated['primary_color'] ?? '#4338ca',
            'secondary_color' => $validated['secondary_color'] ?? '#14b8a6',
            'accent_color' => $validated['accent_color'] ?? '#f59e0b',
            'background_type' => $validated['background_type'] ?? 'gradient',
            'background_position_x' => $validated['background_position_x'] ?? 50,
            'background_position_y' => $validated['background_position_y'] ?? 50,
            'background_size' => $validated['background_size'] ?? 100,
            'font_family' => $validated['font_family'] ?? 'Cairo',
            'text_align' => $validated['text_align'] ?? 'center',
            'title_x' => $validated['title_x'] ?? 0,
            'title_y' => $validated['title_y'] ?? 0,
            'title_size' => $validated['title_size'] ?? 38,
            'title_rotation' => $validated['title_rotation'] ?? 0,
            'subtitle_x' => $validated['subtitle_x'] ?? 0,
            'subtitle_y' => $validated['subtitle_y'] ?? 0,
            'subtitle_size' => $validated['subtitle_size'] ?? 20,
            'subtitle_rotation' => $validated['subtitle_rotation'] ?? 0,
            'name_x' => $validated['name_x'] ?? 0,
            'name_y' => $validated['name_y'] ?? 0,
            'name_size' => $validated['name_size'] ?? 32,
            'name_rotation' => $validated['name_rotation'] ?? 0,
            'body_x' => $validated['body_x'] ?? 0,
            'body_y' => $validated['body_y'] ?? 0,
            'body_size' => $validated['body_size'] ?? 18,
            'body_rotation' => $validated['body_rotation'] ?? 0,
            'logo_x' => $validated['logo_x'] ?? 0,
            'logo_y' => $validated['logo_y'] ?? 0,
            'logo_width' => $validated['logo_width'] ?? 110,
            'logo_rotation' => $validated['logo_rotation'] ?? 0,
            'stamp_size' => $validated['stamp_size'] ?? 120,
            'overlay_opacity' => $validated['overlay_opacity'] ?? 15,
            'border_radius' => $validated['border_radius'] ?? 30,
            'show_logo' => (bool) ($validated['show_logo'] ?? true),
            'show_stamp' => (bool) ($validated['show_stamp'] ?? true),
            'title_color' => $validated['title_color'] ?? '#ffffff',
            'subtitle_color' => $validated['subtitle_color'] ?? '#ffffff',
            'name_color' => $validated['name_color'] ?? '#ffffff',
            'body_color' => $validated['body_color'] ?? '#ffffff',
        ];
    }
}
