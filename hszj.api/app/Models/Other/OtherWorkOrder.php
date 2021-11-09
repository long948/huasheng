<?php
/**
 * Copyright (c) 2020. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * Created by Reliese Model.
 */

namespace App\Models\Other;

use \App\Models\Model;

/**
 * Class OtherConventional
 *
 * @property int $id
 * @property string|null $title
 * @property int|null $details
 * @property int|null $user_id
 * @property bool|null $is_hand
 * @property int|null $create_time
 * @property int|null $update_time
 *
 * @package App\Model\Other
 */
class OtherWorkOrder extends Model
{
    protected $table = 'other_work_order';

    protected $casts = [
        'id' => 'string',
        'title' => 'string',
        'details' => 'string',
        'user_id' => 'string',
        'reply' => 'string',
        'is_hand' => 'bool'
    ];

    protected $dates = [
        'create_time',
        'update_time'
    ];

    protected $fillable = [
        'id',
        'title',
        'details',
        'user_id',
        'reply',
        'is_hand',
        'create_time',
        'update_time'
    ];
}
