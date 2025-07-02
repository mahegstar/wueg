<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'message',
    ];

    public static function get($type, $default = null)
    {
        $setting = self::where('type', $type)->first();
        
        return $setting ? $setting->message : $default;
    }

    public static function set($type, $message)
    {
        $setting = self::updateOrCreate(
            ['type' => $type],
            ['message' => $message]
        );
        
        return $setting;
    }
}