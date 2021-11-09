<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRushModel extends Model
{
    public $table = 'ProductRush';

    public function product(){
        return $this->hasOne('App\Models\ProductModel', 'Id','ProductId');
    }
}
