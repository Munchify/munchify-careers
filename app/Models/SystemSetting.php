<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'is_secret'];

    public static function get(string $key, $default = null): ?string
    {
        $setting = self::where('key', $key)->first();
        return ($setting && !is_null($setting->value)) ? $setting->value : $default;
    }

    public static function set(string $key, ?string $value, string $group = 'general', bool $isSecret = false): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'is_secret' => $isSecret,
            ]
        );
    }
}
