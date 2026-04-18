<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function getValue($key, $default = null)
    {
        return self::where('key', $key)->first()?->value ?? $default;
    }

    public static function setValue($key, $value, $group = 'general')
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }
}
