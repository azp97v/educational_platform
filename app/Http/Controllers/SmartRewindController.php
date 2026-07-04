<?php

namespace App\Http\Controllers;

use App\Models\SmartRewind;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\Answer;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartRewindController extends Controller
{
    /**
     * عرض قائمة Smart Rewinds للطالب
     */
    public function index(): View
    {
        $rewinds = SmartRewind::where('user_id', Auth::id())
            ->with(['question', 'exam', 'exam.lesson'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $summary = SmartRewind::getUserSummary(Auth::id());

        return view('student.smart-rewind.index', compact('rewinds', 'summary'));
    }

    /**
     * عرض تفاصيل Smart Rewind واحد
     */
    public function show(SmartRewind $rewind): View
    {
        // التحقق من أن الطالب يملك هذا الـ Rewind
        if ($rewind->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $related = $rewind->getRelatedQuestions() ?? [];

        return view('student.smart-rewind.show', compact('rewind', 'related'));
    }

    /**
     * تحليل الإجابة الخاطئة وإنشاء Smart Rewind
     */
    public function detect(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_id' => 'required|exists:answers,id',
            'exam_id' => 'required|exists:exams,id'
        ]);

        $question = Question::find($validated['question_id']);
        $selectedAnswer = Answer::find($validated['answer_id']);
        $correctAnswer = Answer::where('question_id', $question->id)->where('is_correct', true)->first();

        // تحقق من أن الإجابة خاطئة
        if ($selectedAnswer->is_correct) {
            return response()->json(['correct' => true, 'message' => 'إجابة صحيحة!']);
        }

        // إنشاء Smart Rewind
        $rewind = SmartRewind::createFromWrongAnswer(
            Auth::id(),
            $question->id,
            $validated['exam_id'],
            $question->video_timestamp ?? 0,
            $correctAnswer->explanation ?? 'راجع هذا الجزء من الفيديو'
        );

        return response()->json([
            'correct' => false,
            'message' => 'إجابة خاطئة - Smart Rewind متاح',
            'rewind' => $rewind,
            'video_timestamp' => $question->video_timestamp,
            'explanation' => $correctAnswer->explanation
        ]);
    }

    /**
     * تسجيل مشاهدة الفيديو
     */
    public function recordWatch(SmartRewind $rewind)
    {
        if ($rewind->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rewind->markAsWatched();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المشاهدة',
            'status' => $rewind->status,
            'watch_count' => $rewind->watch_count
        ]);
    }

    /**
     * تسجيل إتقان المهارة
     */
    public function markMastered(SmartRewind $rewind)
    {
        if ($rewind->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rewind->markAsMastered();

        // إضافة نقاط مكافأة
        $user = Auth::user();
        if ($user->streak) {
            $user->streak->update([
                'total_points' => $user->streak->total_points + 5
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل إتقان المهارة! +5 نقاط',
            'status' => 'mastered'
        ]);
    }

    /**
     * الحصول على إحصائيات Smart Rewind
     */
    public function statistics()
    {
        $userId = Auth::id();

        $stats = [
            'total' => SmartRewind::where('user_id', $userId)->count(),
            'pending' => SmartRewind::where('user_id', $userId)->where('status', 'pending')->count(),
            'watched' => SmartRewind::where('user_id', $userId)->where('status', 'watched')->count(),
            'mastered' => SmartRewind::where('user_id', $userId)->where('status', 'mastered')->count(),
            'points_earned' => SmartRewind::where('user_id', $userId)
                ->where('status', 'mastered')
                ->count() * 5
        ];

        return response()->json($stats);
    }

    /**
     * دمج Smart Rewind مع الدرس
     */
    public function getLessonRewinds($lessonId)
    {
        $rewinds = SmartRewind::whereHas('exam.lesson', function ($query) use ($lessonId) {
            $query->where('id', $lessonId);
        })
        ->where('user_id', Auth::id())
        ->with('question')
        ->get();

        return response()->json($rewinds);
    }
}
