<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Bill;

use App\Models\Model;

/**
 * Class BillUserLeadership
 *
 * @property int $id
 * @property int $user_id
 * @property int $coin_id
 * @property int $user_amount_id
 * @property int $business_id
 * @property int $user_child_id
 * @property int|null $type
 * @property int|null $method
 * @property float $amount
 * @property int|null $status
 * @property Carbon|null $create_time
 * @property Carbon|null $update_time
 * @property string|null $remarks
 *
 * @package App\Model\Other
 */
class BillUserTotalIncome extends Model
{
    protected $table = 'bill_user_total_income';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'coin_id' => 'string',
        'user_amount_id' => 'string',
        'business_id' => 'string',
        'user_child_id' => 'string',
        'type' => 'int',
        'method' => 'int',
        'amount' => 'string',
        'status' => 'int'
    ];

    protected $dates = [
        'create_time',
        'update_time'
    ];

    protected $fillable = [
        'user_id',
        'coin_id',
        'user_amount_id',
        'business_id',
        'user_child_id',
        'type',
        'method',
        'amount',
        'status',
        'create_time',
        'update_time',
        'remarks'
    ];

    private $type = ['花田收益', '花鼠偷取花生米', '被偷花生米', '转出钱包'];

    private $method = ['进账', '出账'];

    public function getTypeAttribute($value)
    {
        return $this->type[$value - 1];
    }

    public function getMethodAttribute($value)
    {
        return $this->method[$value - 1];
    }
}
