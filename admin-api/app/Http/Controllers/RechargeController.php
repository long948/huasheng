<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RechargeController extends Controller
{

    //转入记录
    public function rechargeList(Request $request){

        $count = (int)trim($request->input('limit')) ? trim($request->input('limit')) : 10;
        $where = function ($query) use ($request) {
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords=$request->keywords;
                $query->where('Members.NickName', '=', $keywords);
            }
        };
        $sql_str='recharge.*,coin.EnName,Members.NickName,Members.Phone,coin.Protocol';
        $times =($request->input('times'));
        if(!empty($times)) {
            $start = $times[0];
            $end = $times[1];
            $start=strtotime($start);//获取指定月份的第一天
            $end=strtotime($end); //获取指定月份的最后一天
            $list = DB::table('recharge')
                ->select(
                    DB::raw($sql_str)
                )
                ->leftJoin('coin', function ($join) {
                    $join->on('recharge.CoinId', '=', 'coin.Id');
                })
                ->leftJoin('Members', function ($join) {
                    $join->on('recharge.MemberId', '=', 'Members.Id');
                })
                ->whereBetween('recharge.AddTime',[$start,$end])
                ->where($where)
                ->orderBy("recharge.Id", "DESC")
                ->paginate($count);
            foreach ($list as $v){
                $v->AddTime=date('Y-m-d H:i:s',$v->AddTime);
            }
            if($list){
                return self::returnMsg($list,'操作成功',20000);
            }else{
                return self::returnMsg([],'暂无数据',20000);
            }
        }else{
            $list = DB::table('recharge')
                ->select(
                    DB::raw($sql_str)
                )
                ->leftJoin('coin', function ($join) {
                    $join->on('recharge.CoinId', '=', 'coin.Id');
                })
                ->leftJoin('Members', function ($join) {
                    $join->on('recharge.MemberId', '=', 'Members.Id');
                })
                ->where($where)
                ->orderBy("recharge.Id", "DESC")
                ->paginate($count);
            foreach ($list as $v){
                $v->AddTime=date('Y-m-d H:i:s',$v->AddTime);
            }
            if($list){
                return self::returnMsg($list,'操作成功',20000);
            }else{
                return self::returnMsg([],'暂无数据',20000);
            }
        }
    }

}
