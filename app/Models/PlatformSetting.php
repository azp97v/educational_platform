<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    protected $casts = [
        'value' => 'string',
    ];

    private static function cached(): \Illuminate\Support\Collection
    {
        return Cache::remember('platform_settings_all', 3600, fn () => static::all()->keyBy('key'));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::cached();

        if (!$settings->has($key)) {
            return $default;
        }

        $setting = $settings->get($key);

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general', ?string $label = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type, 'group' => $group, 'label' => $label ?? $key]
        );
        Cache::forget('platform_settings_all');
    }

    public static function getAllGrouped(): array
    {
        return static::cached()->groupBy('group')->toArray();
    }
}
