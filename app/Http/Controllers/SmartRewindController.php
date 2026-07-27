<?php

namespace App\Http\Controllers;

use App\Models\SmartRewind;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartRewindController extends Controller
{
    public function __construct(private StreakService $streakService) {}

    public function index()
    {
        $userId = Auth::id();

        $rewinds = SmartRewind::where('user_id', $userId)
            ->with(['question', 'exam', 'exam.lesson'])
            ->orderByRaw("FIELD(status, 'pending', 'watched', 'mastered')")
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $stats = [
            'total'    => SmartRewind::where('user_id', $userId)->count(),
            'pending'  => SmartRewind::where('user_id', $userId)->where('status', 'pending')->count(),
            'watched'  => SmartRewind::where('user_id', $userId)->where('status', 'watched')->count(),
            'mastered' => SmartRewind::where('user_id', $userId)->where('status', 'mastered')->count(),
        ];

        return view('student.smart-rewind.index', compact('rewinds', 'stats'));
    }

    public function show(SmartRewind $rewind)
    {
        abort_if($rewind->user_id !== Auth::id(), 403);

        $rewind->load(['question.answers', 'exam', 'exam.lesson']);

        $related = SmartRewind::where('user_id', Auth::id())
            ->where('exam_id', $rewind->exam_id)
            ->where('id', '!=', $rewind->id)
            ->where('status', '!=', 'mastered')
            ->with('question')
            ->limit(5)
            ->get();

        return view('student.smart-rewind.show', compact('rewind', 'related'));
    }

    public function recordWatch(SmartRewind $rewind)
    {
        abort_if($rewind->user_id !== Auth::id(), 403);

        $rewind->markAsWatched();

        return response()->json([
            'success'     => true,
            'message'     => 'تم تسجيل المشاهدة',
            'status'      => $rewind->status,
            'watch_count' => $rewind->watch_count,
        ]);
    }

    public function markMastered(SmartRewind $rewind)
    {
        abort_if($rewind->user_id !== Auth::id(), 403);

        if ($rewind->status === 'mastered') {
            return response()->json(['success' => true, 'message' => 'تم الإتقان مسبقاً', 'already' => true]);
        }

        $rewind->markAsMastered();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->streakService->addPoints($user, 5, 'smart_rewind_mastered');

        return response()->json([
            'success' => true,
            'message' => '🏆 أتقنت هذه النقطة! +5 نقاط',
            'status'  => 'mastered',
        ]);
    }

    public function statistics()
    {
        $userId = Auth::id();

        return response()->json([
            'total'        => SmartRewind::where('user_id', $userId)->count(),
            'pending'      => SmartRewind::where('user_id', $userId)->where('status', 'pending')->count(),
            'watched'      => SmartRewind::where('user_id', $userId)->where('status', 'watched')->count(),
            'mastered'     => SmartRewind::where('user_id', $userId)->where('status', 'mastered')->count(),
            'points_earned' => SmartRewind::where('user_id', $userId)->where('status', 'mastered')->count() * 5,
        ]);
    }

    public function getLessonRewinds(int $lessonId)
    {
        $rewinds = SmartRewind::whereHas('exam.lesson', fn($q) => $q->where('id', $lessonId))
            ->where('user_id', Auth::id())
            ->with('question')
            ->get();

        return response()->json($rewinds);
    }
}
