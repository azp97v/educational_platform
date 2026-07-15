<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemError extends Model
{
    protected $fillable = [
        'type', 'message', 'file', 'line', 'url', 'method',
        'status_code', 'user_id', 'user_ip', 'trace', 'resolved',
        'resolved_at', 'resolved_by',
    ];

    protected $casts = [
        'resolved'    => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    public static function capture(Throwable $e, ?\Illuminate\Http\Request $request = null): void
    {
        try {
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

            // Skip 404s and 403s — not actionable production errors
            if (in_array($statusCode, [404, 403, 401, 419, 429])) {
                return;
            }

            static::create([
                'type'        => class_basename($e),
                'message'     => mb_substr($e->getMessage(), 0, 65535),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'url'         => $request?->fullUrl(),
                'method'      => $request?->method(),
                'status_code' => $statusCode,
                'user_id'     => $request ? optional($request->user())->id : null,
                'user_ip'     => $request?->ip(),
                'trace'       => mb_substr($e->getTraceAsString(), 0, 65535),
            ]);
        } catch (\Throwable) {
            // Never let error tracking crash the request
        }
    }
}
