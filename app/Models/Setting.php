<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $cacheKey = 'setting_'.$key;

        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            // Try to decode JSON values
            $decoded = json_decode($setting->value, true);

            return $decoded !== null ? $decoded : $setting->value;
        });
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value)
    {
        // Encode arrays/objects as JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        // Convert boolean to string
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget('setting_'.$key);

        return $setting;
    }

    /**
     * Check if a setting exists
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Delete a setting
     */
    public static function remove($key)
    {
        Cache::forget('setting_'.$key);

        return self::where('key', $key)->delete();
    }
}
