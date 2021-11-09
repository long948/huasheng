<?php
namespace App\Services;

use App\Exceptions\ArException;
use App\Models\MembersModel as Members;
use App\Models\CoinModel as Coin;
use Illuminate\Support\Facades\DB;
use App\Models\MemberCoinModel as MemberCoin;
use zgldh\QiniuStorage\QiniuStorage;
use Firebase\JWT\JWT;
use App\Libraries\Verify;

/*
 * 财务清单
 */
class FinancingListService
{

    /**
     * 获取表名的key
     * @param $uid
     * @return int|string
     */
    public function getTableKey($uid) {
        $key = $uid % 20;
        if($key < 10) {
            $key = '0'.$key;
        }
        return $key;
    }

    /**
     * 获取table前缀
     * @return int|string
     */
    public function getPrefix() {
        return "FinancingList_";
    }

    /**
     * 获取table名称
     * @param $uid
     * @return int|string
     */
    public function getTable($uid) {
        return $this->getPrefix() . $this->getTableKey($uid);
    }

    /**
     * 添加财务清单
     * @param $install_list
     */
    public function add($install_list) {

        $table_list = [];
        // 分表
        foreach ($install_list as $v) {
            $key = $this->getTableKey($v["MemberId"]);
            $table_list[$key][] = $v;
        }

        foreach ($table_list as $k=>$arr) {
            $install_arr = [];
            foreach ($arr as $v) {
                $install_arr[] = $v;
            }
            $table = $this->getPrefix() . $k;
            DB::table($table)->insert($install_arr);
        }

    }

    /**
     * 查询列表
     * @param $uid
     * @param $CoinId
     * @param $Mold
     * @param $count
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function lists($uid, $CoinId, $Mold, $count) {
        $where = [];
        $where[] = ["MemberId", "=", $uid];
        if (!empty($CoinId)) {
            $where[] = ["CoinId", "=", $CoinId];
        }
        if (!empty($Mold)) {
            $where[] = ["Mold", "=", $Mold];
        }
        $table = $this->getTable($uid);
        $lists = DB::table($table)
            ->where($where)
            ->orderBy("Id", "DESC")
            ->paginate($count);

        return $lists;

    }

    /**
     * 查询明细
     * @param $id
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function detail($id, $uid) {
        $where = [];
        $where[] = ["Id", "=", $id];
        $where[] = ["MemberId", "=", $uid];
        $table = $this->getTable($uid);
        $detail = DB::table($table)
            ->where($where)
            ->first();

        return $detail;

    }

}
