<?php


namespace App\Utils;


use Illuminate\Support\Facades\DB;

/**
 * 用户帮助类
 * Class UserUtil
 * @package App\Common\Util
 */
class UserUtil
{

    /**
     * 获取用户父级编号
     * @param $userId
     * @return string
     */
    public static function getParentId($userId)
    {
        $parentId = DB::table('members')
            ->where('Id', $userId)
            ->value('ParentId');
        if (empty($parentId)) {
            return null;
        }
        return DB::table('members')->where('id', $parentId)->select(['id', 'NickName', 'Avatar'])->first();
    }


    /**
     * 获取所有上级线
     * @param $userId
     * @param int $length
     * @return array
     */
    public static function getRoots($userId, $length = -1)
    {
        $root = DB::table('members')
            ->where('Id', $userId)
            ->value('root');
        if (empty($root)) {
            return [];
        }

        return array_reverse(explode(',', substr($root, 0, $length)));
    }


    /**
     * 我的所有下线
     * @param $userId
     * @param int $algebra
     * @return array
     */
    public static function userChild($userId, $algebra = 6)
    {
        $users = Db::table(env('USER_DATABASE') . '.user_relation')->where('user_id', '>=', $userId)->get();
        $userChild = [];
        foreach ($users as &$u) {
            $roots = array_reverse(explode(',', trim($u->root, ',')));
            $length = count($roots);
            if ($length < 1) {
                continue;
            }
            if ($algebra > 0) {
                $roots = array_slice($roots, 0, $algebra > $length ? $length : $algebra);
            }
            foreach ($roots as $root) {
                if (in_array($userId, $roots) && $userId != $root) {
                    $userChild[] = $u->user_id;
                }
            }
        }
        return $userChild;
    }

    /**
     * 获取直推
     * @param $userId
     * @return Collection
     */
    public static function getDirectPush($userId)
    {
        return DB::table('members')->where('ParentId', '=', $userId)->select(['id', 'NickName', 'Avatar'])->get();
    }

}
