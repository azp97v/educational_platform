<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    public function disk(): string
    {
        return (string) config('media.disk', 'public');
    }

    public function storeUploadedFile(UploadedFile $file, string $directory): array
    {
        $path = $file->store(
            trim($directory, '/'),
            [
                'disk' => $this->disk(),
                'visibility' => config('media.visibility', 'public'),
            ]
        );

        return [
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
            'url' => $this->resolveUrl($path),
        ];
    }

    public function resolveUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $value = trim($path);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//', 'data:', 'blob:'])) {
            return $value;
        }

        $disk = Storage::disk($this->disk());
        $useSigned = (bool) config('media.use_signed_urls', false);

        if ($useSigned && method_exists($disk, 'temporaryUrl')) {
            try {
                return $disk->temporaryUrl(
                    $value,
                    now()->addMinutes((int) config('media.signed_url_ttl_minutes', 30))
                );
            } catch (\Throwable) {
                // Fall back to standard URL when temporary URLs are unavailable.
            }
        }

        return $disk->url($value);
    }

    public function deleteIfExists(?string $path): void
    {
        if (!$path) {
            return;
        }

        $diskName = $this->disk();
        $disk = Storage::disk($diskName);
        if ($disk->exists($path)) {
            $disk->delete($path);
            return;
        }

        // Legacy cleanup fallback for old local-public stored files.
        if ($diskName !== 'public' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}

