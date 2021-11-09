<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinBill extends Model
{
    protected $table = 'coin_logs';
    /**
     * 可以分配的属性。
     *
     * @var array
     */
    protected $fillable = ['id', 'sn', 'user_id','amount','coin_name','remark','coin_id','type_id','foreign_key'];
}
