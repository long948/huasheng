<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberProductModel extends Model
{
    public $timestamps = false;

    public $table = 'MemberProduct';

    public function product(){
        return $this->hasOne('App\Models\ProductModel', 'Id','ProductId');
    }

}
