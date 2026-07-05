<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonNote;
use App\Models\LessonRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonInteractionController extends Controller
{
    public function saveRating(Request $request, Lesson $lesson)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        LessonRating::updateOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => Auth::id()],
            ['rating' => $request->rating]
        );

        $avg = LessonRating::where('lesson_id', $lesson->id)->avg('rating');

        return response()->json([
            'success' => true,
            'average' => round($avg, 1),
            'count'   => LessonRating::where('lesson_id', $lesson->id)->count(),
        ]);
    }

    public function getRating(Lesson $lesson)
    {
        $mine = LessonRating::where('lesson_id', $lesson->id)
            ->where('user_id', Auth::id())
            ->value('rating');

        $avg = LessonRating::where('lesson_id', $lesson->id)->avg('rating');

        return response()->json([
            'my_rating' => $mine,
            'average'   => $avg ? round($avg, 1) : null,
            'count'     => LessonRating::where('lesson_id', $lesson->id)->count(),
        ]);
    }

    public function getNotes(Lesson $lesson)
    {
        $notes = LessonNote::where('lesson_id', $lesson->id)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'text', 'created_at']);

        return response()->json(['notes' => $notes]);
    }

    public function addNote(Request $request, Lesson $lesson)
    {
        $request->validate(['text' => 'required|string|min:2|max:1000']);

        $note = LessonNote::create([
            'lesson_id' => $lesson->id,
            'user_id'   => Auth::id(),
            'text'      => trim($request->text),
        ]);

        return response()->json([
            'success' => true,
            'note'    => ['id' => $note->id, 'text' => $note->text, 'created_at' => $note->created_at],
        ]);
    }

    public function deleteNote(Lesson $lesson, LessonNote $note)
    {
        if ($note->user_id !== Auth::id() || $note->lesson_id !== $lesson->id) {
            return response()->json(['success' => false], 403);
        }

        $note->delete();
        return response()->json(['success' => true]);
    }

    public function updateNote(Request $request, Lesson $lesson, LessonNote $note)
    {
        if ($note->user_id !== Auth::id() || $note->lesson_id !== $lesson->id) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['text' => 'required|string|min:2|max:1000']);

        $note->update(['text' => trim($request->text)]);

        return response()->json([
            'success' => true,
            'note'    => ['id' => $note->id, 'text' => $note->text, 'created_at' => $note->created_at],
        ]);
    }

    public function teacherNotes(Lesson $lesson)
    {
        // Only the lesson's course teacher can see notes
        $teacher = Auth::user();
        $isOwner = $lesson->course && $lesson->course->user_id === $teacher->id;
        if (!$isOwner) abort(403);

        $notes = LessonNote::where('lesson_id', $lesson->id)
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'user_id', 'text', 'created_at', 'updated_at']);

        if (request()->wantsJson()) {
            return response()->json(['notes' => $notes]);
        }

        return view('teacher.lesson-notes', compact('lesson', 'notes'));
    }
}
