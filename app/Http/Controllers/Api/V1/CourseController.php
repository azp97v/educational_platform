<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $courses = Course::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->select('id', 'title', 'description', 'thumbnail', 'created_at')
            ->latest()
            ->paginate(12);

        return response()->json($courses);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json($course->load('lessons'));
    }
}
