<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Storage Disk
    |--------------------------------------------------------------------------
    |
    | Set MEDIA_DISK=r2 in production to route all media uploads to
    | Cloudflare R2 instead of the local public disk.
    |
    */
    'disk' => env('MEDIA_DISK', env('FILESYSTEM_DISK', 'public')),

    /*
    |--------------------------------------------------------------------------
    | Public Visibility
    |--------------------------------------------------------------------------
    */
    'visibility' => env('MEDIA_VISIBILITY', 'public'),

    /*
    |--------------------------------------------------------------------------
    | CDN URL Override
    |--------------------------------------------------------------------------
    |
    | When set, MediaStorageService::resolveUrl() will replace the R2 endpoint
    | with this CDN URL for all public media. Example:
    |   MEDIA_CDN_URL=https://cdn.edu.ejlalmakkah.org.sa
    |
    */
    'cdn_url' => env('MEDIA_CDN_URL'),

    /*
    |--------------------------------------------------------------------------
    | Signed URLs
    |--------------------------------------------------------------------------
    |
    | Enable to protect private media with temporary signed URLs.
    | Keep false for public media served via CDN (faster, cacheable).
    |
    */
    'use_signed_urls' => (bool) env('MEDIA_USE_SIGNED_URLS', false),
    'signed_url_ttl_minutes' => (int) env('MEDIA_SIGNED_URL_TTL_MINUTES', 30),
];
