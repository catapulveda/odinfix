<?php
namespace App\Api;

use Illuminate\Support\Facades\Storage;

class Config
{
    protected static $config_file = 'settings.json';

    public static function get($key = null)
    {
        $txt = Storage::disk('local')->get(self::$config_file);

        $data = json_decode($txt, true);

        if($key) return $data[$key];

        return $data;
    }

    public static function save($data)
    {
        Storage::disk('local')->put(self::$config_file, json_encode($data));

        return true;
    }
}