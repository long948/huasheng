<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Shop;



use App\Models\Model;

/**
 * Class BuyTeamActivity
 *
 * @property string $activity_id
 * @property string|null $act_name
 * @property int|null $team_type
 * @property int|null $time_limit
 * @property float|null $return_amount
 * @property string|null $team_price
 * @property string|null $super_group_price
 * @property int|null $needer
 * @property int|null $stock_limit
 * @property string|null $goods_id
 * @property int|null $buy_limit
 * @property int|null $sales_sum
 * @property int|null $virtual_num
 * @property string|null $share_title
 * @property string|null $share_desc
 * @property string|null $share_img
 * @property int $sort
 * @property bool $is_recommend
 * @property bool|null $status
 * @property int|null $create_time
 * @property int|null $update_time
 * @property bool $deleted
 * @property float $sub_commission
 * @property float $store_count
 * @property string $coin_id
 * @property string $luck_coin_id
 * @property string $luck_amount
 *
 * @package App\Model
 */
class TeamActivity extends Model
{
    protected $table = 'shop_team_activity';

    protected $primaryKey = 'activity_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'activity_id' => 'string',
        'team_type' => 'int',
        'time_limit' => 'int',
        'return_amount' => 'string',
        'team_price' => 'string',
        'super_group_price' => 'string',
        'needer' => 'int',
        'stock_limit' => 'int',
        'goods_id' => 'string',
        'buy_limit' => 'int',
        'sales_sum' => 'int',
        'virtual_num' => 'int',
        'sort' => 'int',
        'is_recommend' => 'bool',
        'status' => 'int',
        'create_time' => 'int',
        'update_time' => 'int',
        'deleted' => 'bool',
        'sub_commission' => 'float',
        'store_count' => 'float',
        'coin_id' => 'string',
        'luck_coin_id' => 'string',
        'luck_amount' => 'string',
    ];

    protected $fillable = [
        'act_name',
        'team_type',
        'time_limit',
        'return_amount',
        'team_price',
        'super_group_price',
        'needer',
        'stock_limit',
        'goods_id',
        'buy_limit',
        'sales_sum',
        'virtual_num',
        'share_title',
        'share_desc',
        'share_img',
        'sort',
        'is_recommend',
        'status',
        'create_time',
        'update_time',
        'deleted',
        'sub_commission',
        'store_count',
        'coin_id',
        'luck_coin_id',
        'luck_amount',
    ];

}
