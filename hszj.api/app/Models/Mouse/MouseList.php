<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Mouse;

use App\Models\Model;


/**
 * Class MouseList
 *
 * @property int $id
 * @property string $nickname
 * @property string|null $background_image
 * @property string $icon
 * @property float $price
 * @property int $level
 * @property int $user_level
 * @property bool|null $is_healing
 * @property float|null $healing_time
 * @property int|null $max_hold
 * @property float|null $min_steal
 * @property float|null $max_steal
 * @property int|null $max_frequency
 * @property string|null $explanation
 * @property int|null $sort
 * @property int $create_time
 * @property bool|null $is_experience
 * @property bool|null $is_disable
 * @property bool $is_delete
 * @property int|null $update_time
 * @property int|null $delete_time
 *
 * @package App\Model\Other
 */
class MouseList extends Model
{
    protected $table = 'mouse_list';

    protected $casts = [
        'id' => 'string',
        'price' => 'float',
        'is_healing' => 'bool',
        'healing_time' => 'float',
        'max_hold' => 'int',
        'level' => 'int',
        'user_level' => 'int',
        'min_steal' => 'float',
        'max_steal' => 'float',
        'max_steal_amount' => 'float',
        'max_frequency' => 'int',
        'sort' => 'int',
        'create_time' => 'int',
        'is_experience' => 'bool',
        'is_disable' => 'bool',
        'is_delete' => 'bool',
        'update_time' => 'int',
        'delete_time' => 'int'
    ];

    protected $fillable = [
        'nickname',
        'background_image',
        'icon',
        'price',
        'level',
        'user_level',
        'is_healing',
        'healing_time',
        'max_hold',
        'min_steal',
        'max_steal',
        'max_frequency',
        'explanation',
        'sort',
        'create_time',
        'is_experience',
        'is_disable',
        'is_delete',
        'update_time',
        'delete_time'
    ];

    public function getIconAttribute($value)
    {
        if ($value) {
            return getDomain() . $value;
        }
        return $value;
    }
}
