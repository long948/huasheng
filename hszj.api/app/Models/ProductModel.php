<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    public $timestamps = false;

    public $table = 'Products';

    public static function GetOneByCond(array $cond){
        return self::where($cond)->first();
    }
}
