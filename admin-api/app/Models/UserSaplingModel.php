<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserSaplingModel extends Model
{
    public $table = 'miner_user_sapling';
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
            ->leftjoin('members as m','m.Id','=','miner_user_sapling.user_id')
            ->Leftjoin('miner_sapling as s','s.id','=','miner_user_sapling.sapling_id')
            ->select('miner_user_sapling.*','m.NickName','m.Phone','s.nickname')
            ->orderBy('id','desc')
            ->where($where)
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
