<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Dog;

use Carbon\Carbon;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DogList
 *
 * @property int $id
 * @property string $nickname
 * @property string|null $background_image
 * @property string $icon
 * @property float $price
 * @property int $level
 * @property int $user_level
 * @property float|null $min_defense
 * @property float|null $max_defense
 * @property int|null $max_defense_count
 * @property bool|null $is_defense_interval
 * @property int|null $defense_interval_time
 * @property int|null $stand_guard_time_ltt
 * @property int|null $max_hold
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
class DogList extends Model
{
    protected $table = 'dog_list';

    protected $casts = [
        'id' => 'string',
        'price' => 'float',
        'min_defense' => 'float',
        'max_defense' => 'float',
        'max_defense_count' => 'int',
        'level' => 'int',
        'user_level' => 'int',
        'is_defense_interval' => 'bool',
        'defense_interval_time' => 'int',
        'stand_guard_time_ltt' => 'int',
        'max_hold' => 'int',
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
        'min_defense',
        'max_defense',
        'max_defense_count',
        'is_defense_interval',
        'defense_interval_time',
        'stand_guard_time_ltt',
        'max_hold',
        'explanation',
        'sort',
        'create_time',
        'is_experience',
        'is_disable',
        'is_delete',
        'update_time',
        'delete_time'
    ];


    protected static function boot(): void
    {
        parent::boot();
        //全局作用域
        static::addGlobalScope('delete', function (Builder $builder) {
            $builder->where('is_delete', '=', 0)->where('is_disable', 0);
        });
    }

    public function getIconAttribute($value)
    {
        if ($value) {
            return getDomain() . $value;
        }
        return $value;
    }
}
