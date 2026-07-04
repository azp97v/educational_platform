<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Storage Disk
    |--------------------------------------------------------------------------
    |
    | Use an object storage disk (s3 or S3-compatible) in production:
    | MEDIA_DISK=s3
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
    | Signed URLs
    |--------------------------------------------------------------------------
    */
    'use_signed_urls' => (bool) env('MEDIA_USE_SIGNED_URLS', false),
    'signed_url_ttl_minutes' => (int) env('MEDIA_SIGNED_URL_TTL_MINUTES', 30),
];

