<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Shop;

use App\Models\Model;

/**
 * Class BuyTeamFollow
 *
 * @property string $follow_id
 * @property string|null $follow_user_id
 * @property int|null $follow_time
 * @property string|null $found_id
 * @property string|null $activity_id
 * @property string|null $user_note
 * @property string|null $good_id
 * @property bool|null $status
 * @property int|null $address_id
 * @property bool|null $is_lock_draw
 * @property bool|null $is_refund
 * @property bool|null $is_end
 * @property string|null $refund_transaction_id
 * @property int|null $refund_time
 * @property int|null $end_pay_time
 * @property int|null $pay_time
 * @property bool|null $is_show
 * @property int|null $team_type
 *
 * @package App\Model
 */
class TeamFollow extends Model
{
    protected $table = 'shop_team_follow';
    protected $primaryKey = 'follow_id';
    
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'follow_id' => 'string',
        'follow_user_id' => 'string',
        'follow_time' => 'int',
        'found_id' => 'string',
        'good_id' => 'string',
        'activity_id' => 'string',
        'status' => 'int',
        'address_id' => 'string',
        'user_note' => 'string',
        'is_lock_draw' => 'bool',
        'is_end' => 'bool',
        'is_refund' => 'bool',
        'refund_transaction_id' => 'string',
        'refund_time' => 'int',
        'pay_time' => 'int',
        'end_pay_time' => 'int',
        'is_show' => 'bool',
        'team_type' => 'int',
    ];

    protected $fillable = [
        'follow_user_id',
        'follow_time',
        'found_id',
        'good_id',
        'activity_id',
        'user_note',
        'status',
        'address_id',
        'is_lock_draw',
        'is_end',
        'is_refund',
        'refund_trancation_id',
        'refund_time',
        'pay_time',
        'end_pay_time',
        'is_show',
        'team_type',
    ];
}
