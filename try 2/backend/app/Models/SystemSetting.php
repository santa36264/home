<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if registration is public
     */
    public static function isPublicRegistration(): bool
    {
        return self::get('registration_mode', 'public') === 'public';
    }

    /**
     * Check if registration is admin only
     */
    public static function isAdminOnlyRegistration(): bool
    {
        return self::get('registration_mode', 'public') === 'admin_only';
    }
}
