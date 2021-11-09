<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use \App\Models\Model;

/**
 * Class MinerUserSaplingTotalRelease
 *
 * @property int $id
 * @property int|null $user_id
 * @property float|null $amount
 * @property float|null $steal_amount
 * @property float|null $already_steal_amount
 * @property int|null $begin_receive_time
 * @property int|null $issue_time
 * @property bool $is_issue
 * @property bool $is_steal
 * @property int|null $create_time
 * @property int|null $update_time
 *
 * @package App\Model\Other
 */
class MinerUserSaplingTotalRelease extends Model
{
    protected $table = 'miner_user_sapling_total_release';

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'amount' => 'float',
        'steal_amount' => 'float',
        'already_steal_amount' => 'float',
        'begin_receive_time' => 'int',
        'is_issue' => 'bool',
        'is_steal' => 'bool',
        'issue_time' => 'int',
        'create_time' => 'int',
        'update_time' => 'int'
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'steal_amount',
        'already_steal_amount',
        'begin_receive_time',
        'is_issue',
        'is_steal',
        'issue_time',
        'create_time',
        'update_time'
    ];

}
