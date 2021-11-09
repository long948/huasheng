<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Shop;


use App\Models\Model;

/**
 * Class BuyTeamFound
 *
 * @property string $found_id
 * @property string $activity_id
 * @property string $user_id
 * @property int|null $found_time
 * @property int|null $found_end_time
 * @property int|null $open_found_time
 * @property int|null $join
 * @property int|null $need
 * @property int|null $stock_limit
 * @property string|null $return_amount
 * @property string|null $team_price
 * @property float|null $good_id
 * @property bool|null $status
 * @property bool $is_luck_draw
 * @property bool|null $is_end
 * @property float $sub_commission
 * @property int $team_type
 * @property string|null $spike_id
 * @property bool|null $is_super_group
 * @property string $coin_id
 * @property string $luck_coin_id
 * @property string $invitation_code
 * @property string $luck_amount
 * @package App\Model
 */
class TeamFound extends Model
{
    protected $table = 'shop_team_found';
    protected $primaryKey = 'found_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $casts = [
        'found_id' => 'string',
        'activity_id' => 'string',
        'user_id' => 'string',
        'found_time' => 'int',
        'found_end_time' => 'int',
        'open_found_time' => 'int',
        'join' => 'int',
        'need' => 'int',
        'stock_limit' => 'int',
        'return_amount' => 'string',
        'team_price' => 'string',
        'good_id' => 'string',
        'status' => 'int',
        'is_luck_draw' => 'bool',
        'is_end' => 'bool',
        'sub_commission' => 'float',
        'team_type' => 'int',
        'spike_id' => 'string',
        'is_super_group' => 'bool',
        'coin_id' => 'string',
        'luck_coin_id' => 'string',
        'invitation_code' => 'string',
        'luck_amount' => 'string',
    ];

    protected $fillable = [
        'found_time',
        'found_end_time',
        'open_found_time',
        'join',
        'need',
        'stock_limit',
        'return_amount',
        'team_price',
        'good_id',
        'status',
        'is_luck_draw',
        'is_end',
        'sub_commission',
        'team_type',
        'spike_id',
        'is_super_group',
        'coin_id',
        'luck_coin_id',
        'invitation_code',
        'luck_amount'
    ];
}
