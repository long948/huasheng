<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Mouse;


use App\Models\Dog\DogList;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MouseUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $mouse_list_id
 * @property float $price
 * @property bool|null $is_healing
 * @property bool|null $is_in_healing
 * @property int|null $healing_time_ltt
 * @property int|null $healing_time
 * @property int|null $max_hold
 * @property float|null $min_steal
 * @property float|null $max_steal
 * @property int|null $frequency
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
class MouseUser extends Model
{
    protected $table = 'mouse_user';


    public $timestamps = false;

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'mouse_list_id' => 'string',
        'price' => 'float',
        'is_healing' => 'bool',
        'is_in_healing' => 'bool',
        'healing_time' => 'int',
        'healing_time_ltt' => 'int',
        'max_hold' => 'int',
        'min_steal' => 'float',
        'max_steal' => 'float',
        'frequency' => 'int',
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
        'user_id',
        'mouse_list_id',
        'price',
        'is_healing',
        'is_in_healing',
        'healing_time',
        'healing_time_ltt',
        'max_hold',
        'min_steal',
        'max_steal',
        'steal_amount',
        'max_steal_amount',
        'frequency',
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


    protected static function boot(): void
    {
        parent::boot();
        //全局作用域
        static::addGlobalScope('delete', function (Builder $builder) {
            $builder->where('is_delete', '=', 0)->where('is_disable', 0);
        });
    }


    public function mouseType()
    {
        return $this->hasOne(MouseList::class,'id','mouse_list_id');
    }
}
