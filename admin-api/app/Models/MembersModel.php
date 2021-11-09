<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MembersModel extends BaseModel
{
    public $table = 'Members';

    public $timestamps = false;

    public static function GetPageList(int $count, $where)
    {
        return self::where($where)
            ->leftJoin('Members as a', 'Members.ParentId', '=', 'a.Id')
            ->select('Members.*', 'a.Phone as ParentPhone','a.NickName as ParentNickName')
            ->orderBy('Members.Id', 'desc')->paginate($count);
    }


    /**
     * @func 根据$id查找数据
     * @param $id
     * @return mixed
     */
    public static function GetBId(int $id)
    {
        return self::where('Id', '=', $id)->first();
    }
    /**
     * 判断是否是邮箱
     * @param $Name
     * @return bool
     */
    public static function IsEmail($Name) {
        $IsEmail = preg_match("/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i", $Name);
        if ($IsEmail) {
            return true;
        } else {
            return false;
        }
    }


}
