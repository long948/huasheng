<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Dog;


use App\Models\Model;

/**
 * Class DogAndMouse
 *
 * @property int $id
 * @property int|null $dog_id
 * @property int|null $mouse_id
 *
 * @package App\Model\Other
 */
class DogAndMouse extends Model
{
	protected $table = 'dog_and_mouse';

	public $timestamps = false;

	protected $casts = [
		'dog_id' => 'string',
		'mouse_id' => 'string'
	];

	protected $fillable = [
		'dog_id',
		'mouse_id'
	];
}
