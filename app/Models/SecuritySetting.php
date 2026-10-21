<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuritySetting extends Model
{
    use HasFactory;

    protected $table = 'security_settings';
    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, ?string $description = null)
    {
        return static::updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'description' => $description, 'updated_at' => now()]
        );
    }
}
