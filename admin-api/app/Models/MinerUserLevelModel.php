<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MinerUserLevelModel extends Model
{
    public $table = 'miner_user_level';
    public $timestamps = false;
    protected $keyType = 'string';
    protected $casts = ['id' => 'string'];

    public static function GetPageList( $count, $where)
    {
        return self::where($where)
            ->leftjoin('Members as m','m.Id','=','miner_user_level.user_id')
            ->leftjoin('miner_level as p','p.id','=','miner_user_level.miner_level_id')
            ->where($where)
            ->select('miner_user_level.*','m.NickName','m.Phone','p.nickname')
            ->orderBy('id','desc')
            ->paginate($count);
    }
    /**
     * @func根据id修改数据
     * @param $id
     * @return mixed
     */
    public static function updatedById (string $Id)
    {
        $data=[
            'is_audit'=>0
        ];
        return self::where('id', $Id)->update($data);
    }

}
