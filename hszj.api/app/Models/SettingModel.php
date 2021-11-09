<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    public $table = 'Setting';

    public static function getValueByKey(string $key)
    {
        return self::where('k', $key)->value('v');
    }
}
