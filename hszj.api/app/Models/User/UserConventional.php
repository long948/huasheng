<?php

/**
 * Created by Reliese Model.
 */

namespace App\Model\Other;

use \App\Models\Model;

/**
 * Class UserConventional
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $level_id
 * @property int|null $rate_of_return
 * @property int|null $create_time
 * @property int|null $update_time
 *
 * @package App\Model\Other
 */
class UserConventional extends Model
{
    protected $table = 'user_conventional';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'level_id' => 'int',
        'rate_of_return' => 'float',
        'create_time' => 'int',
        'update_time' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'level_id',
        'rate_of_return',
        'create_time',
        'update_time'
    ];
}
