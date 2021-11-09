<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SaplingPackageModel extends Model
{
    public $table = 'miner_sapling_package';
    public $timestamps = false;
    protected $keyType = 'string';
    /**
     * @func根据id删除数据
     * @param $id
     * @return mixed
     */
    public static function DelById (string $id)
    {
        $data=[
            'is_delete' => 1,
            'delete_time' =>  date("Y-m-d H:i:s"),
        ];
        return self::where('id', $id)->update($data);
    }

}
