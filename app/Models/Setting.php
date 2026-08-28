<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("settings.{$key}");
    }

    public static function getDayList(string $key, array $default = []): array
    {
        $raw = static::get($key);

        if ($raw === null) {
            return $default;
        }

        $days = collect(explode(',', $raw))
            ->map(fn ($item) => trim($item))
            ->filter(fn ($item) => is_numeric($item) && (int) $item > 0)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return $days;
    }
}
