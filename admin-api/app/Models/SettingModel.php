<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SettingModel extends BaseModel
{
    public $table = 'SettingModel';
    public $primaryKey = 'k';
    public $timestamps = false;

    public static function GetPageList()
    {
        return self::limit(1)->first();
    }


    /**
     * @func 根据$id查找数据
     * @param $id
     * @return mixed
     */
    public static function GetBId(int $id)
    {
        return self::where('Id', $id)->first();
    }


}
