<?php
/**
 * Created by PhpStorm.
 * User: ChenJulong
 * Date: 2019/8/27
 * Time: 10:28
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class FinancingListModel extends BaseModel
{

    public $timestamps = false;

    /**
     * Notes:写日志
     */
    public static function WriteLog($MemberId, $CoinId, $CoinName, $Money, $Balance, $Mold, $MoldTitle, $Remark)
    {
        $table = $MemberId % 20;
        if ($table < 10) $table = '0' . $table;
        $table  = 'FinancingList_' . $table;
        $sqlmap = [
            'MemberId'  => $MemberId,
            'CoinId'    => $CoinId,
            'CoinName'  => $CoinName,
            'Money'     => $Money,
            'Balance'   => $Balance,
            'Mold'      => $Mold,
            'MoldTitle' => $MoldTitle,
            'Remark'    => $Remark,
            'AddTime'   => time(),
        ];
        return DB::table($table)->insert($sqlmap);
    }

    public static function get_mold_by_call_index($callIndex)
    {
        $data = DB::table('financingmold')
            ->where('call_index', $callIndex)
            ->first();
        return $data;
    }
    public static function Lists(int $count, $where=[]){
//        dd($where);
        $list1 = DB::table('FinancingList_01')
            ->leftJoin('Members as m','m.Id','FinancingList_01.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_01.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list2 = DB::table('FinancingList_02')
            ->leftJoin('Members as m','m.Id','FinancingList_02.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_02.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list3 = DB::table('FinancingList_03')
            ->leftJoin('Members as m','m.Id','FinancingList_03.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_03.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list4 = DB::table('FinancingList_04')
            ->leftJoin('Members as m','m.Id','FinancingList_04.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_04.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list5 = DB::table('FinancingList_05')
            ->leftJoin('Members as m','m.Id','FinancingList_05.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_05.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list6 = DB::table('FinancingList_06')
            ->leftJoin('Members as m','m.Id','FinancingList_06.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_06.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list7 = DB::table('FinancingList_07')
            ->leftJoin('Members as m','m.Id','FinancingList_07.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_07.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list8 = DB::table('FinancingList_08')
            ->leftJoin('Members as m','m.Id','FinancingList_08.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_08.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list9 = DB::table('FinancingList_09')
            ->leftJoin('Members as m','m.Id','FinancingList_09.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_09.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list10 = DB::table('FinancingList_10')
            ->leftJoin('Members as m','m.Id','FinancingList_10.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_10.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list11 = DB::table('FinancingList_11')
            ->leftJoin('Members as m','m.Id','FinancingList_11.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_11.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list12 = DB::table('FinancingList_12')
            ->leftJoin('Members as m','m.Id','FinancingList_12.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_12.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list13 = DB::table('FinancingList_13')
            ->leftJoin('Members as m','m.Id','FinancingList_13.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_13.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list14 = DB::table('FinancingList_14')
            ->leftJoin('Members as m','m.Id','FinancingList_14.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_14.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list15 = DB::table('FinancingList_15')
            ->leftJoin('Members as m','m.Id','FinancingList_15.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_15.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list16 = DB::table('FinancingList_16')
            ->leftJoin('Members as m','m.Id','FinancingList_16.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_16.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list17 = DB::table('FinancingList_17')
            ->leftJoin('Members as m','m.Id','FinancingList_17.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_17.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list18 = DB::table('FinancingList_18')
            ->leftJoin('Members as m','m.Id','FinancingList_18.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_18.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $list19 = DB::table('FinancingList_19')
            ->leftJoin('Members as m','m.Id','FinancingList_19.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_19.Balance','Remark','m.Phone')
            ->where($where)
            ->where('Mold',8)
            ->orderBy('AddTime','desc');
        $data = DB::table('FinancingList_00')
            ->leftJoin('Members as m','m.Id','FinancingList_00.MemberId')
            ->select('Mold','MemberId','CoinName','AddTime','MoldTitle','Money','FinancingList_00.Balance','Remark','m.Phone')
            ->union($list1)->union($list2)->union($list3)->union($list4)->union($list5)->union($list6)->union($list7)->union($list8)
            ->union($list9)->union($list10)->union($list11)->union($list12)->union($list13)->union($list14)->union($list15)->union($list16)
            ->union($list17)->union($list18)->union($list19)
            ->where('Mold',8)
            ->where($where)
            ->orderBy('AddTime','desc')
            ->paginate($count);
        return $data;
    }
}
