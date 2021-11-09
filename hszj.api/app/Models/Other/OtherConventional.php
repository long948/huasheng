<?php

/**
 * Created by Reliese Model.
 */

namespace App\Model\Other;

use \App\Models\Model;

/**
 * Class OtherConventional
 *
 * @property int $id
 * @property string|null $rule
 * @property int|null $level
 * @property int|null $rate_of_return
 * @property string|null $level_name
 * @property string|null $icon
 * @property int|null $create_time
 * @property int|null $update_time
 *
 * @package App\Model\Other
 */
class OtherConventional extends Model
{
	protected $table = 'other_conventional';

	protected $casts = [
		'id' => 'string',
		'level' => 'int',
		'rate_of_return' => 'float',
		'create_time' => 'int',
		'update_time' => 'int'
	];

	protected $fillable = [
		'rule',
		'level',
		'rate_of_return',
		'level_name',
		'icon',
		'create_time',
		'update_time'
	];
}
