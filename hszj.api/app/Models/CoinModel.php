<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinModel extends Model
{
    public $table = 'Coin';

    public static function GetByEnName(string $name = 'PT')
    {
        return self::where('EnName', $name)->first();
    }


    public static function GetById($coinId)
    {
        return self::where('Id', $coinId)->first();
    }

    
    public static function getCoinAll()
    {
        return self::query()->where('Status', 1)->get();
    }

}
