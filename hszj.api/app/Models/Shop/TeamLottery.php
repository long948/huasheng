<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Shop;

use App\Models\Model;

/**
 * Class BuyTeamLottery
 *
 * @property string $id
 * @property string|null $user_id
 * @property string|null $good_id
 * @property string|null $order_id
 * @property string|null $order_sn
 * @property int|null $activity_id
 * @property int|null $found_id
 * @property int|null $follow_id
 * @property int|null $create_time
 * @property int|null $update_time
 *
 * @package App\Model
 */
class TeamLottery extends Model
{
    protected $table = 'shop_team_lottery';
    public $incrementing = true;

    public $timestamps = true;
    protected $dateFormat = 'U';

    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = 'update_time';

    protected $casts = [
        'id' => 'string',
        'good_id' => 'string',
        'user_id' => 'string',
        'order_id' => 'string',
        'activity_id' => 'string',
        'follow_id' => 'string',
        'found_id' => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'good_id',
        'order_id',
        'order_sn',
        'activity_id',
        'follow_id',
        'found_id',
        'create_time',
        'update_time'
    ];
}
