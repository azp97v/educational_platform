<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
            )
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->latest()
            ->paginate(25);

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->only('id', 'name', 'email', 'role', 'is_active', 'created_at'));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        // Only admin route is guarded, but add explicit check so tests catch regressions
        abort_unless($request->user()?->role === 'admin', 403);

        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'role'      => ['sometimes', 'in:admin,teacher,student'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user->update($validated);

        return response()->json($user->fresh()->only('id', 'name', 'email', 'role', 'is_active', 'created_at'));
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Cannot delete your own account.');
        $user->delete();

        return response()->json(['message' => 'deleted'], 200);
    }
}
