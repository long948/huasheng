<?php

declare (strict_types=1);

namespace App\Models\Shop;

use App\Models\Model;

/**
 * @property int $id
 * @property int $time_slot_id
 * @property int $team_activity_id
 * @property int $good_id
 * @property int $create_time
 * @property int $update_update
 * @property int $is_delete
 */
class TimeSlotActivity extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_time_slot_activity';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'time_slot_id' => 'integer',
        'team_activity_id' => 'integer',
        'good_id' => 'integer',
        'create_time' => 'integer',
        'update_update' => 'integer',
        'is_delete' => 'integer'
    ];
}
