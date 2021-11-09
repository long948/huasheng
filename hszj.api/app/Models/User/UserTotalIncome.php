<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use \App\Models\Model;

/**
 * Class UserIncome
 *
 * @property int $id
 * @property int $user_id
 * @property float|null $before_amount
 * @property float|null $amount
 * @property float|null $after_amount
 * @property bool|null $is_disable
 * @property Carbon|null $create_time
 * @property Carbon|null $update_time
 * @property string $sign
 *
 * @package App\Model\Other
 */
class UserTotalIncome extends Model
{
    protected $table = 'user_total_income';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'before_amount' => 'float',
        'amount' => 'float',
        'after_amount' => 'float',
        'is_disable' => 'bool'
    ];

    protected $dates = [
        'create_time',
        'update_time'
    ];

    protected $fillable = [
        'user_id',
        'before_amount',
        'amount',
        'after_amount',
        'is_disable',
        'create_time',
        'update_time',
        'sign'
    ];
}
