<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Dog;

use App\Models\Model;
use App\Models\Mouse\MouseList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;


/**
 * Class DogUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $dog_list_id
 * @property float $price
 * @property bool|null $is_stand_guard
 * @property bool|null $is_defense
 * @property float|null $min_defense
 * @property float|null $max_defense
 * @property int|null $defense_count
 * @property int|null $max_defense_count
 * @property bool|null $is_defense_interval
 * @property int|null $defense_interval_time
 * @property int|null $stand_guard_time_ltt
 * @property int|null $defense_time_ltt
 * @property int|null $max_hold
 * @property string|null $explanation
 * @property int|null $sort
 * @property bool|null $is_experience
 * @property bool|null $is_disable
 * @property bool $is_delete
 * @property int $create_time
 * @property int|null $update_time
 * @property int|null $delete_time
 *
 * @package App\Model\Other
 */
class DogUser extends Model
{
    protected $table = 'dog_user';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'dog_list_id' => 'string',
        'price' => 'float',
        'is_stand_guard' => 'bool',
        'is_defense' => 'bool',
        'min_defense' => 'float',
        'max_defense' => 'float',
        'defense_count' => 'int',
        'max_defense_count' => 'int',
        'is_defense_interval' => 'bool',
        'defense_interval_time' => 'int',
        'stand_guard_time_ltt' => 'int',
        'defense_time_ltt' => 'int',
        'max_hold' => 'int',
        'sort' => 'int',
        'is_experience' => 'bool',
        'is_disable' => 'bool',
        'is_delete' => 'bool',
        'create_time' => 'int',
        'update_time' => 'int',
        'delete_time' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'dog_list_id',
        'price',
        'is_stand_guard',
        'is_defense',
        'min_defense',
        'max_defense',
        'defense_count',
        'max_defense_count',
        'is_defense_interval',
        'defense_interval_time',
        'defense_time_ltt',
        'max_hold',
        'explanation',
        'sort',
        'is_experience',
        'is_disable',
        'is_delete',
        'create_time',
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


    public function dogType()
    {
        return $this->hasOne(DogList::class, 'id', 'dog_list_id');
    }
}
