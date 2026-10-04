<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, $default = null)
    {
        try {
            $settings = Cache::rememberForever('app_settings_map', function () {
                return static::pluck('value', 'key')->toArray();
            });

            $val = $settings[$key] ?? null;
            if ($val === null || $val === '') {
                return $default;
            }

            return $val;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, string $group = 'system', string $type = 'text')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        Cache::forget('app_settings_map');
        return $setting;
    }

    /**
     * Get all settings grouped by their group.
     */
    public static function getAllGrouped(): array
    {
        return static::all()->groupBy('group')->toArray();
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('app_settings_map');
    }

    /**
     * Format a date according to system settings.
     */
    public static function formatDate($date, bool $includeTime = false): string
    {
        if (!$date) {
            return '—';
        }
        try {
            if (!$date instanceof \Carbon\Carbon && !$date instanceof \Carbon\CarbonInterface) {
                $date = \Carbon\Carbon::parse($date);
            }
            $format = static::get('date_format', 'd M, Y');
            if ($includeTime) {
                $format .= ', h:i A';
            }
            return $date->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    /**
     * Format currency according to system settings.
     */
    public static function formatCurrency($amount): string
    {
        $symbol = static::get('currency_symbol', '$');
        return $symbol . number_format((float) $amount, 2);
    }

    /**
     * Get the full URL for uploaded application logo.
     */
    public static function getLogoUrl(): ?string
    {
        $logo = static::get('app_logo');
        if ($logo && file_exists(public_path($logo))) {
            return asset($logo);
        }
        return null;
    }

    /**
     * Get the full URL for uploaded application favicon.
     */
    public static function getFaviconUrl(): string
    {
        $favicon = static::get('app_favicon');
        if ($favicon && file_exists(public_path($favicon))) {
            return asset($favicon);
        }
        return asset('favicon.ico');
    }
}

