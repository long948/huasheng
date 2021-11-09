<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Shop;

use App\Models\Model;

/**
 * Class ShopGood
 *
 * @property int $goods_id
 * @property int $cat_id
 * @property string $goods_sn
 * @property string $goods_name
 * @property int $click_count
 * @property int $store_count
 * @property int|null $collect_sum
 * @property int|null $comment_count
 * @property int $weight
 * @property float $volume
 * @property float $market_price
 * @property float $shop_price
 * @property float $ordinary_price
 * @property int $exchange_integral
 * @property string $keywords
 * @property string $goods_remark
 * @property string|null $goods_content
 * @property string|null $mobile_content
 * @property string $original_img
 * @property bool $is_virtual
 * @property int|null $virtual_indate
 * @property int|null $virtual_limit
 * @property bool|null $virtual_refund
 * @property bool $is_on_sale
 * @property bool $is_free_shipping
 * @property int $on_time
 * @property int $sort
 * @property bool $is_recommend
 * @property bool $is_new
 * @property bool|null $is_hot
 * @property int $last_update
 * @property int $goods_type
 * @property int|null $give_integral
 * @property int|null $sales_sum
 * @property bool|null $prom_type
 * @property int|null $prom_id
 * @property int|null $store_id
 * @property bool|null $goods_state
 * @property bool|null $is_own_shop
 * @property string|null $video
 * @property string|null $tag_id
 * @property string|null $fabulous
 * @property string|null $step_on
 * @property string|null $share
 *
 * @package App\Model
 */
class Good extends Model
{
    protected $table = 'shop_good';
    protected $primaryKey = 'goods_id';
    public $timestamps = false;

    protected $casts = [
        'cat_id' => 'int',
        'click_count' => 'int',
        'store_count' => 'int',
        'collect_sum' => 'int',
        'comment_count' => 'int',
        'weight' => 'int',
        'volume' => 'float',
        'market_price' => 'string',
        'shop_price' => 'string',
        'ordinary_price' => 'string',
        'exchange_integral' => 'int',
        'is_virtual' => 'bool',
        'virtual_indate' => 'int',
        'virtual_limit' => 'int',
        'virtual_refund' => 'bool',
        'is_on_sale' => 'bool',
        'is_free_shipping' => 'bool',
        'on_time' => 'int',
        'sort' => 'int',
        'is_recommend' => 'bool',
        'is_new' => 'bool',
        'is_hot' => 'bool',
        'last_update' => 'int',
        'goods_type' => 'int',
        'give_integral' => 'int',
        'sales_sum' => 'int',
        'prom_type' => 'int',
        'prom_id' => 'int',
        'store_id' => 'int',
        'goods_state' => 'int',
        'is_own_shop' => 'bool',
        'tag_id' => 'string',
        'fabulous' => 'int',
        'step_on' => 'int',
        'share' => 'int',
        'video_height' => 'float',
        'video_width' => 'float',
    ];

    protected $fillable = [
        'cat_id',
        'goods_sn',
        'goods_name',
        'click_count',
        'store_count',
        'collect_sum',
        'comment_count',
        'weight',
        'volume',
        'market_price',
        'shop_price',
        'ordinary_price',
        'exchange_integral',
        'keywords',
        'goods_remark',
        'goods_content',
        'mobile_content',
        'original_img',
        'is_virtual',
        'virtual_indate',
        'virtual_limit',
        'virtual_refund',
        'is_on_sale',
        'is_free_shipping',
        'on_time',
        'sort',
        'is_recommend',
        'is_new',
        'is_hot',
        'last_update',
        'goods_type',
        'give_integral',
        'sales_sum',
        'prom_type',
        'prom_id',
        'store_id',
        'goods_state',
        'is_own_shop',
        'video',
        'tag_id',
        'fabulous',
        'step_on',
        'share'
    ];

}
