<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\StudentInquiry;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentInquiryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'lesson_id' => 'required|exists:lessons,id',
                'question_text' => 'required|string|min:2|max:2000',
                'inquiry_type' => 'nullable|in:question,note',
            ]);

            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولًا.',
                ], 401);
            }

            $lesson = Lesson::with('course.instructor')->find($validated['lesson_id']);
            if (!$lesson || !$lesson->course) {
                return response()->json([
                    'success' => false,
                    'message' => 'الدرس أو المسار غير موجود.',
                ], 404);
            }

            $inquiryType = $validated['inquiry_type'] ?? 'question';
            if ($inquiryType === 'question' && mb_strlen(trim((string) $validated['question_text'])) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'السؤال يجب أن يكون 10 أحرف على الأقل.',
                ], 422);
            }
            $inquiry = StudentInquiry::create([
                'lesson_id' => $validated['lesson_id'],
                'course_id' => $lesson->course_id,
                'student_id' => $user->id,
                'teacher_id' => $lesson->course->instructor_id,
                'inquiry_type' => $inquiryType,
                'question_text' => $validated['question_text'],
                'status' => 'pending',
            ]);

            $teacher = $lesson->course->instructor;
            if ($teacher && ($teacher->notify_inquiry ?? true)) {
                $label = $inquiryType === 'note' ? 'ملاحظة' : 'سؤال';
                $notificationText = Str::limit("{$user->name} أرسل {$label} على درس \"{$lesson->title}\".", 120);
                $teacher->notify(new AppNotification(
                    $inquiryType === 'note' ? 'ملاحظة جديدة' : 'سؤال جديد',
                    $notificationText,
                    route('teacher.inquiries'),
                    'inquiry',
                    'ri-question-answer-line'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال طلبك بنجاح.',
                'inquiry_id' => $inquiry->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الطلب غير صالحة.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('StudentInquiry store error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الإرسال.',
            ], 500);
        }
    }

    public function studentIndex()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $inquiries = StudentInquiry::where('student_id', $user->id)
            ->with(['lesson', 'teacher', 'course'])
            ->latest()
            ->paginate(10);

        return view('student.inquiries.index', compact('inquiries'));
    }

    public function teacherIndex()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $courseIds = $user->courses()->pluck('id');

        $pendingInquiries = StudentInquiry::whereIn('course_id', $courseIds)
            ->where('status', 'pending')
            ->with(['lesson', 'student', 'course'])
            ->latest()
            ->get();

        $answeredInquiries = StudentInquiry::whereIn('course_id', $courseIds)
            ->where('status', 'answered')
            ->with(['lesson', 'student', 'course'])
            ->latest()
            ->get();

        return view('teacher.inquiries_analytics', compact('pendingInquiries', 'answeredInquiries'));
    }

    public function answer(Request $request, StudentInquiry $inquiry)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $inquiry->teacher_id !== (int) $user->id) {
            abort(403, 'غير مصرح');
        }

        $validated = $request->validate([
            'answer_text' => 'required|string|min:3|max:3000',
        ]);

        $inquiry->update([
            'answer_text' => trim($validated['answer_text']),
            'status' => 'answered',
            'answered_at' => now(),
        ]);

        $student = $inquiry->student;
        if ($student && ($student->notify_inquiry ?? true)) {
            $label = $inquiry->inquiry_type === 'note' ? 'ملاحظتك' : 'سؤالك';
            $student->notify(new AppNotification(
                'تم الرد على طلبك',
                "تم الرد على {$label} في درس \"{$inquiry->lesson->title}\".",
                route('student.inquiries.index'),
                'inquiry',
                'ri-chat-smile-2-line'
            ));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الرد بنجاح.',
            ]);
        }

        return back()->with('success', 'تم الرد على الاستفسار بنجاح');
    }

    public function destroy(StudentInquiry $inquiry)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $inquiry->teacher_id !== (int) $user->id) {
            abort(403, 'غير مصرح');
        }

        $inquiry->delete();
        return back()->with('success', 'تم حذف الاستفسار');
    }
}
