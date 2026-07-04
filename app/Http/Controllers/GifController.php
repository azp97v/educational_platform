<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GifController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['success' => true, 'data' => []]);
        }

        $giphyKey = config('services.giphy.key');
        $tenorKey = config('services.tenor.key');

        if (!$giphyKey && !$tenorKey) {
            return response()->json(['success' => false, 'message' => 'خدمة GIF غير مُفعّلة حالياً.', 'data' => []], 200);
        }

        $cacheKey = 'gif_search_' . ($giphyKey ? 'giphy_' : 'tenor_') . md5($query);

        $results = Cache::remember($cacheKey, 300, function () use ($query, $giphyKey, $tenorKey) {
            if ($giphyKey) {
                return $this->searchGiphy($query, $giphyKey);
            }

            return $this->searchTenor($query, $tenorKey);
        });

        return response()->json(['success' => true, 'data' => $results]);
    }

    private function searchGiphy(string $query, string $key): array
    {
        $response = Http::timeout(6)->get('https://api.giphy.com/v1/gifs/search', [
            'q' => $query,
            'api_key' => $key,
            'limit' => 24,
            'rating' => 'pg-13',
            'lang' => 'ar',
        ]);

        if (!$response->successful()) {
            return [];
        }

        return collect($response->json('data', []))->map(function ($item) {
            $images = $item['images'] ?? [];
            $full = $images['fixed_height']['url'] ?? ($images['original']['url'] ?? null);
            $preview = $images['fixed_height_small']['url'] ?? ($images['preview_gif']['url'] ?? $full);

            return [
                'id' => $item['id'] ?? null,
                'previewUrl' => $preview,
                'fullUrl' => $full,
            ];
        })->filter(fn ($g) => $g['fullUrl'] && $g['previewUrl'])->values()->all();
    }

    private function searchTenor(string $query, string $key): array
    {
        $response = Http::timeout(6)->get('https://tenor.googleapis.com/v2/search', [
            'q' => $query,
            'key' => $key,
            'client_key' => 'iglal_messaging',
            'limit' => 24,
            'media_filter' => 'gif',
            'contentfilter' => 'high',
        ]);

        if (!$response->successful()) {
            return [];
        }

        return collect($response->json('results', []))->map(function ($item) {
            $gif = $item['media_formats']['gif'] ?? null;
            $tiny = $item['media_formats']['tinygif'] ?? $gif;

            return [
                'id' => $item['id'] ?? null,
                'previewUrl' => $tiny['url'] ?? null,
                'fullUrl' => $gif['url'] ?? null,
            ];
        })->filter(fn ($g) => $g['fullUrl'] && $g['previewUrl'])->values()->all();
    }
}
