<?php
namespace App\Http\Controllers;
use App\Exceptions\ArException;
use App\Services\AdminLogService;
use App\Services\CoinService;
use App\Services\FinancingListService;
use App\Services\PHPGangsta_GoogleAuthenticator;
use App\Utils\HttpUtils;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Libraries\base;

class WithdrawController extends Controller
{

    //转入记录
    public function withdrawList(Request $request){
        $count = (int)trim($request->input('limit')) ? trim($request->input('limit')) : 10;
        $where = function ($query) use ($request) {
            if ($request->has('keywords') and $request->keywords != '') {
                $keywords=$request->keywords;
                $query->where('members.NickName', '=', $keywords);
            }
            if ($request->has('Status') and $request->Status != '') {
                $Status=$request->Status;
                $query->where('withdraw.Status', '=', $Status);
            }
        };
        $sql_str='withdraw.*,coin.EnName,members.NickName,members.Phone,coin.Protocol';
        $times =($request->input('times'));
        if(!empty($times)) {
            $start = $times[0];
            $end = $times[1];
            $sql_str='withdraw.*,coin.EnName,members.NickName,members.Phone,coin.Protocol';
            $list = DB::table('withdraw')
                ->select(
                    DB::raw($sql_str)
                )
                ->leftJoin('coin', function ($join) {
                    $join->on('withdraw.CoinId', '=', 'coin.Id');
                })
                ->leftJoin('members', function ($join) {
                    $join->on('withdraw.MemberId', '=', 'members.Id');
                })
                ->whereBetween('withdraw.created_at',[$start,$end])
                ->where($where)
                ->orderBy("withdraw.Id", "DESC")
                ->paginate($count);
            if($list){
                return self::returnMsg($list,'操作成功',20000);
            }else{
                return self::returnMsg([],'暂无数据',20000);
            }
        }else{
            $list = DB::table('withdraw')
                ->select(
                    DB::raw($sql_str)
                )
                ->leftJoin('coin', function ($join) {
                    $join->on('withdraw.CoinId', '=', 'coin.Id');
                })
                ->leftJoin('members', function ($join) {
                    $join->on('withdraw.MemberId', '=', 'members.Id');
                })
                ->where($where)
                ->orderBy("withdraw.Id", "DESC")
                ->paginate($count);
//        dd($list);
            if($list){
                return self::returnMsg($list,'操作成功',20000);
            }else{
                return self::returnMsg([],'暂无数据',20000);
            }
        }
    }


    //获取币种的记录
    public function getWithdrawCoin(Request $request){
        $Id = (int)$request->input('Id');

        if(empty($Id)){
            return self::returnMsg([],'id不能为空','20001');
        }
        $sql_str='withdraw.*,coin.EnName,members.NickName,members.Phone,coin.Protocol';
        $list = DB::table('withdraw')
            ->select(
                DB::raw($sql_str)
            )
            ->leftJoin('coin', function ($join) {
                $join->on('withdraw.CoinId', '=', 'coin.Id');
            })
            ->leftJoin('members', function ($join) {
                $join->on('withdraw.MemberId', '=', 'members.Id');
            })
            ->where('withdraw.Id','=',$Id)
            ->first();
        if($list){
            return self::returnMsg($list,'操作成功',20000);
        }else{
            return self::returnMsg([],'暂无数据',20000);
        }
    }



    //币种审核待处理
    public function waitProcess(Request $request, CoinService $service){
        $id   = (int)trim($request->input('id'));
        $type = (int)trim($request->input('type'));
        $withdraw = DB::table("Withdraw")->where("Id", $id)->first();
        if ($withdraw->Status != 0 && $withdraw->Status != 3)  return self::returnMsg([],'请勿重复操作',20003);
        //type: 1提交到区块  2 驳回 3直接处理
        $auth_remark = trim($request->input('auth_remark'));
        $service->withdrawAuth($id, $type, $auth_remark);
        return self::returnMsg([],'操作成功',20000);
    }

}
