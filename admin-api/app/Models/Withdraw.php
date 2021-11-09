<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraw';
    /**
     * 可以分配的属性。
     *
     * @var array
     */
    protected $fillable = ['MemberId', 'Mobile', 'CoinId','CoinName','Address','Money','FeeCoin', 'FeeCoinEname',
        'Balance', 'ProcessMold', 'Hash', 'Status', 'WithdrawInfo', 'Remark', 'OrderSn', 'ProcessTime','Fee', 'Real','AddTime',
        'auth_remark'
    ];
}
