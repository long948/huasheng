<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MouseModel extends Model
{
    public $table = 'mouse_list';
    public $timestamps = false;
    protected $keyType = 'string';
    protected $casts = ['id' => 'string'];
    /**
     * @func根据id删除数据
     * @param $id
     * @return mixed
     */

    public static function GetList( $count, $where){
        return self::where($where)
            ->orderBy("id", "asc")
            ->paginate($count);
    }
    public static function DelById (string $id)
    {
        $data=[
            'is_disable'=>1,
            'is_delete'=>1,
        ];
        return self::where('id', $id)->update($data);
    }

}
