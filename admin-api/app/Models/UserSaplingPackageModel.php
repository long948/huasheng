<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/9/26
 * Time: 14:41
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserSaplingPackageModel extends Model
{
    public $table = 'mouse_user';
    public $timestamps = false;
    protected $keyType = 'string';
    /**
     * @func根据id删除数据
     * @param $id
     * @return mixed
     */
    public static function DelById (string $id)
    {
        return self::where('id', $id)->update(['is_delete' => 1]);
    }

}
