<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\UserMessagingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StickerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $stickers = Sticker::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Sticker $s) => $this->present($s));

        $setting = UserMessagingSetting::where('user_id', $user->id)->first();
        $media = $setting?->media ?? [];
        $favoriteIds = $media['sticker_favorites'] ?? [];
        $recentIds = $media['sticker_recent'] ?? [];

        $byId = $stickers->keyBy('id');

        return response()->json([
            'success' => true,
            'data' => [
                'stickers' => $stickers->values(),
                'favorites' => collect($favoriteIds)->map(fn ($id) => $byId->get($id))->filter()->values(),
                'recent' => collect($recentIds)->map(fn ($id) => $byId->get($id))->filter()->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'       => ['required', 'string', 'in:static,animated'],
            'file'       => ['required', 'file', 'max:8192', 'mimes:png,webp,webm'],
            'source_url' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        // If a source URL is provided, derive the original file path and check for duplicates.
        // We reference the same file instead of creating a copy — this prevents duplicate saves
        // and ensures findStickerByUrl matches by URL on any subsequent page load.
        if (!empty($data['source_url'])) {
            $urlPath = parse_url($data['source_url'], PHP_URL_PATH) ?? '';
            // Strip /storage/ prefix to get the relative disk path (e.g. stickers/xxx.png)
            $sourcePath = ltrim(preg_replace('#^/?storage/#', '', ltrim($urlPath, '/')), '/');

            if ($sourcePath && Storage::disk('public')->exists($sourcePath)) {
                // Check if user already has this sticker
                $existing = Sticker::where('user_id', $user->id)
                    ->where('file_path', $sourcePath)
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success'      => true,
                        'data'         => $this->present($existing),
                        'already_saved' => true,
                    ]);
                }

                // Create a record pointing to the same file — no duplication on disk
                $sticker = Sticker::create([
                    'user_id'   => $user->id,
                    'type'      => $data['type'],
                    'file_path' => $sourcePath,
                ]);

                return response()->json(['success' => true, 'data' => $this->present($sticker)]);
            }
        }

        // Fallback: save the uploaded file normally (user-created stickers, or unknown source)
        $file = $request->file('file');
        $path = $file->store('stickers', 'public');

        $sticker = Sticker::create([
            'user_id'   => $user->id,
            'type'      => $data['type'],
            'file_path' => $path,
        ]);

        return response()->json(['success' => true, 'data' => $this->present($sticker)]);
    }

    public function toggleFavorite(Request $request, Sticker $sticker)
    {
        $user = Auth::user();
        if ($sticker->user_id !== $user->id) {
            abort(403);
        }

        $setting = UserMessagingSetting::firstOrCreate(['user_id' => $user->id]);
        $media = $setting->media ?? [];
        $favorites = $media['sticker_favorites'] ?? [];

        if (in_array($sticker->id, $favorites, true)) {
            $favorites = array_values(array_filter($favorites, fn ($id) => $id !== $sticker->id));
            $isFavorite = false;
        } else {
            $favorites[] = $sticker->id;
            $isFavorite = true;
        }

        $media['sticker_favorites'] = $favorites;
        $setting->media = $media;
        $setting->save();

        return response()->json(['success' => true, 'isFavorite' => $isFavorite]);
    }

    public function markUsed(Request $request, Sticker $sticker)
    {
        $user = Auth::user();
        if ($sticker->user_id !== $user->id) {
            abort(403);
        }

        $setting = UserMessagingSetting::firstOrCreate(['user_id' => $user->id]);
        $media = $setting->media ?? [];
        $recent = $media['sticker_recent'] ?? [];

        $recent = array_values(array_filter($recent, fn ($id) => $id !== $sticker->id));
        array_unshift($recent, $sticker->id);
        $media['sticker_recent'] = array_slice($recent, 0, 12);
        $setting->media = $media;
        $setting->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Sticker $sticker)
    {
        $user = Auth::user();
        if ($sticker->user_id !== $user->id) {
            abort(403);
        }

        Storage::disk('public')->delete($sticker->file_path);

        $setting = UserMessagingSetting::where('user_id', $user->id)->first();
        if ($setting) {
            $media = $setting->media ?? [];
            $media['sticker_favorites'] = array_values(array_filter($media['sticker_favorites'] ?? [], fn ($id) => $id !== $sticker->id));
            $media['sticker_recent'] = array_values(array_filter($media['sticker_recent'] ?? [], fn ($id) => $id !== $sticker->id));
            $setting->media = $media;
            $setting->save();
        }

        $sticker->delete();

        return response()->json(['success' => true]);
    }

    private function present(Sticker $sticker): array
    {
        return [
            'id' => $sticker->id,
            'type' => $sticker->type,
            'url' => asset('storage/' . $sticker->file_path),
        ];
    }
}
