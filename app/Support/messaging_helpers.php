<?php

if (!function_exists('guessMessagingMimeType')) {
    function guessMessagingMimeType(string $path, ?string $fallback = null): string
    {
        if (!empty($fallback)) {
            return $fallback;
        }

        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'application/octet-stream',
            'bmp' => 'image/bmp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'm4a' => 'audio/mp4',
            'ogg' => 'audio/ogg',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }
}

if (!function_exists('isSafeInlineMessagingMimeType')) {
    function isSafeInlineMessagingMimeType(string $mimeType): bool
    {
        $mimeType = strtolower(trim($mimeType));

        return $mimeType === 'application/pdf'
            || str_starts_with($mimeType, 'audio/')
            || str_starts_with($mimeType, 'video/')
            || (str_starts_with($mimeType, 'image/') && $mimeType !== 'image/svg+xml');
    }
}
