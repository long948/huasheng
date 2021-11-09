<?php


namespace App\Services;

use App\Exceptions\Common\CommonException;
use App\Libraries\Thrift;
use App\Models\CoinModel as Coin;
use App\Models\CoinBill;
use App\Models\MembersCoinModel as UserCoin;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Self_;

class CoinService
{

    /**
     * 减少余额
     * @param $uid
     * @param $coinId
     * @param $money
     * @param $moldCallIndex
     * @param string $remark
     * @throws \Exception
     */
    public static function decMoney($uid, $coinId, $money, $moldCallIndex, $remark = "") {
        //检查余额
        $memberCoin = DB::table("MemberCoin")->where('MemberId', $uid)->where('CoinId', $coinId)->first();
        if (empty($memberCoin)) {
            throw new \Exception("未找到该币种");
        }
        $balanceMoney = !empty($memberCoin->Money) ? $memberCoin->Money : 0;
        // 余额不足
        if(bccomp($balanceMoney, $money, 10) < 0) {
            throw new \Exception("余额不足");
        }
        $coinName = $memberCoin->CoinName;
        $FinancingMold = DB::table("coin_logs_types")->where("key", $moldCallIndex)->first();
        if (empty($FinancingMold)) {
            throw new \Exception("没有查询到该币种模式");
        }
        $Mold = !empty($FinancingMold->id) ? $FinancingMold->id : 0;
        $MoldTitle = !empty($FinancingMold->name) ? $FinancingMold->name : 0;

//         追加到清单
        $BillData = [
            "sn" => '',
            "user_id" => $uid,
            "coin_id" => $coinId,
            "coin_name" => $coinName,
            "amount" => -$money,
            "type_id" => $Mold,
            "remark" => $MoldTitle,
            "tx_type" => '-',
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];
        DB::table("coin_logs")->insert($BillData);

        $res = DB::table('MemberCoin')
            ->where('MemberId', $uid)
            ->where('CoinId', $coinId)
            ->where("Money", ">=", $money)
            ->decrement("Money", $money);
        // 余额不够（利用 Money >= 处理并发）
        if (!$res) {
            throw new \Exception("余额不足");
        }

    }

    /**
     * 增加余额
     * @param $uid
     * @param $coinId
     * @param $money
     * @param $moldCallIndex
     * @param string $remark
     * @throws \Exception
     */
    public static function incMoney($uid, $coinId, $money, $moldCallIndex, $remark = "") {
        //检查余额
        $memberCoin = DB::table("MemberCoin")->where('MemberId', $uid)->where('CoinId', $coinId)->first();
        if (empty($memberCoin)) {
            throw new \Exception("未找到该币种");
        }
        $balanceMoney = !empty($memberCoin->Money) ? $memberCoin->Money : 0;
        $coinName = $memberCoin->CoinName;
        $FinancingMold = DB::table("coin_logs_types")->where("key", $moldCallIndex)->first();
        if (empty($FinancingMold)) {
            throw new \Exception("没有查询到该币种模式");
        }
        $Mold = !empty($FinancingMold->id) ? $FinancingMold->id : 0;
        $MoldTitle = !empty($FinancingMold->name) ? $FinancingMold->name : 0;

//         追加到清单
        $BillData = [
            "sn" => '',
            "user_id" => $uid,
            "coin_id" => $coinId,
            "coin_name" => $coinName,
            "amount" => $money,
            "type_id" => $Mold,
            "remark" => $MoldTitle,
            "tx_type" => '+',
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s"),
        ];
        DB::table("coin_logs")->insert($BillData);

        DB::table('MemberCoin')
            ->where('MemberId', $uid)
            ->where('CoinId', $coinId)
            ->increment("Money", $money);

    }

    /**
     * 锁定余额
     * @param $uid
     * @param $coinId
     * @param $money
     * @param $moldCallIndex
     * @param string $remark
     * @throws \Exception
     */
    public static function saveForzenMoney($uid, $coinId, $money, $moldCallIndex, $remark = "") {
        if (empty($money)) {
            throw new \Exception("金额错误");
        }

        //检查余额
        $memberCoin = DB::table("MemberCoin")->where('MemberId', $uid)->where('CoinId', $coinId)->first();
        if (empty($memberCoin)) {
            throw new \Exception("未找到该币种");
        }
        $balanceMoney = !empty($memberCoin->Money) ? $memberCoin->Money : 0;
        $forzenMoney = !empty($memberCoin->Forzen) ? $memberCoin->Forzen : 0;
        $coinName = $memberCoin->CoinName;
        $FinancingMold = DB::table("FinancingMold")->where("call_index", $moldCallIndex)->first();
        if (empty($FinancingMold)) {
            throw new \Exception("没有查询到该币种模式");
        }

        $where = [];
        $where[] = ["MemberId", "=", $uid];
        $where[] = ["CoinId", "=", $coinId];

        $smoney = -$money;
        $absMoney = abs($money);
        // 判断是加还是减
        if ($money < 0) {
            // 余额不足
            if(bccomp($forzenMoney, $absMoney, 10) < 0) {
                throw new \Exception("余额不足");
            }
            $where[] = ["Forzen", ">=", $absMoney];
        } else {
            // 锁定金额
            if(bccomp($balanceMoney, $absMoney, 10) < 0) {
                throw new \Exception("冻结余额不足");
            }
            $where[] = ["Money", ">=", $absMoney];
        }

        $upData = [
            "Forzen" => DB::raw("Forzen + {$money}"),
            "Money" => DB::raw("Money + {$smoney}"),
        ];

        $res = DB::table("MemberCoin")->where($where)->update($upData);
        // 余额不够（利用 Money >= 处理并发）
        if (!$res) {
            if ($money < 0) {
                throw new \Exception("冻结余额不足");
            } else {
                throw new \Exception("余额不足");
            }
        }

        $Mold = !empty($FinancingMold->id) ? $FinancingMold->id : 0;
        $MoldTitle = !empty($FinancingMold->title) ? $FinancingMold->title : 0;
        $install_financingList[] = [
            "MemberId" => $uid,
            "CoinId" => $coinId,
            "CoinName" => $coinName,
            "AddTime" => time(),
            "Mold" => $Mold,
            "MoldTitle" => $MoldTitle,
            "Money" => $smoney,
            "Balance" => bcadd($balanceMoney, $smoney, 10),
            "Remark" => $remark,
        ];

        $FinancingListService = new FinancingListService();
        $FinancingListService->add($install_financingList);

        // 追加到清单
        $BillData = [
            "MemberId" => $uid,
            "CoinId" => $coinId,
            "CoinName" => $coinName,
            "Money" => $smoney,
            "Type" => $money < 0 ? 1 : 2,
            "Mold" => $Mold,
            "MoldTitle" => $MoldTitle,
            "Balance" => bcadd($balanceMoney, $smoney, 10),
            "Remark" => $remark,
            "Address" => "",
            "Status" => 1,
            "ProcessType" => 2,
            "ProcessTime" => date("Y-m-d H:i:s"),
            "CreateTime" => date("Y-m-d H:i:s"),
            "UpdateTime" => date("Y-m-d H:i:s"),
        ];
        DB::table("MemberBill")->insert($BillData);

    }

    /**
     * 冻结余额
     * @param $uid
     * @param $coinId
     * @param $money
     * @param $moldCallIndex
     * @param string $remark
     * @throws \Exception
     */
    public static function saveLockMoney($uid, $coinId, $money, $moldCallIndex, $remark = "") {
        if (empty($money)) {
            throw new \Exception("金额错误");
        }

        //检查余额
        $memberCoin = DB::table("MemberCoin")->where('MemberId', $uid)->where('CoinId', $coinId)->first();
        if (empty($memberCoin)) {
            throw new \Exception("未找到该币种");
        }
        $balanceMoney = !empty($memberCoin->Money) ? $memberCoin->Money : 0;
        $LockMoney = !empty($memberCoin->LockMoney) ? $memberCoin->LockMoney : 0;
        $coinName = $memberCoin->CoinName;
        $FinancingMold = DB::table("FinancingMold")->where("call_index", $moldCallIndex)->first();
        if (empty($FinancingMold)) {
            throw new \Exception("没有查询到该币种模式");
        }

        $where = [];
        $where[] = ["MemberId", "=", $uid];
        $where[] = ["CoinId", "=", $coinId];

        $smoney = -$money;
        $absMoney = abs($money);
        // 判断是加还是减
        if ($money < 0) {
            // 余额不足
            if(bccomp($LockMoney, $absMoney, 10) < 0) {
                throw new \Exception("余额不足");
            }
            $where[] = ["LockMoney", ">=", $absMoney];
        } else {
            // 锁定金额
            if(bccomp($balanceMoney, $absMoney, 10) < 0) {
                throw new \Exception("冻结余额不足");
            }
            $where[] = ["Money", ">=", $absMoney];
        }

        $upData = [
            "LockMoney" => DB::raw("LockMoney + {$money}"),
            "Money" => DB::raw("Money + {$smoney}"),
        ];

        $res = DB::table("MemberCoin")->where($where)->update($upData);
        // 余额不够（利用 Money >= 处理并发）
        if (!$res) {
            if ($money < 0) {
                throw new \Exception("锁定余额不足");
            } else {
                throw new \Exception("余额不足");
            }
        }

        $Mold = !empty($FinancingMold->id) ? $FinancingMold->id : 0;
        $MoldTitle = !empty($FinancingMold->title) ? $FinancingMold->title : 0;
        $install_financingList[] = [
            "MemberId" => $uid,
            "CoinId" => $coinId,
            "CoinName" => $coinName,
            "AddTime" => time(),
            "Mold" => $Mold,
            "MoldTitle" => $MoldTitle,
            "Money" => $smoney,
            "Balance" => bcadd($balanceMoney, $smoney, 10),
            "Remark" => $remark,
        ];

        $FinancingListService = new FinancingListService();
        $FinancingListService->add($install_financingList);

        // 追加到清单
        $BillData = [
            "MemberId" => $uid,
            "CoinId" => $coinId,
            "CoinName" => $coinName,
            "Money" => $smoney,
            "Type" => $money < 0 ? 1 : 2,
            "Mold" => $Mold,
            "MoldTitle" => $MoldTitle,
            "Balance" => bcadd($balanceMoney, $smoney, 10),
            "Remark" => $remark,
            "Address" => "",
            "Status" => 1,
            "ProcessType" => 2,
            "ProcessTime" => date("Y-m-d H:i:s"),
            "CreateTime" => date("Y-m-d H:i:s"),
            "UpdateTime" => date("Y-m-d H:i:s"),
        ];
        DB::table("MemberBill")->insert($BillData);
    }
    /**
     * @method withdrawAuth
     * @param int $id
     * @param int $type
     * @param $auth_remark
     * @return bool
     * @throws CommonException
     */
    public static function withdrawAuth(int $id, int $type, $auth_remark){
        if($auth_remark){
            Withdraw::where('Id',$id)->update(['auth_remark'=>$auth_remark]);
        }
        switch ($type) {
            case 1://通过
                $result = self::agreeWithdraw($id);
                break;
            case 2://驳回
                $result = self::refuseWithdraw($id);
                break;
            case 3://直接处理
                $result = self::directAgreeWithdraw($id);
                break;
            default:
                throw new CommonException(CommonException::PARAM_ERROR);
        }
        return $result;
    }

    private static function agreeWithdraw($id)
    {
        $info = Withdraw::find($id);
        if(!$info) throw new CommonException(CommonException::UNKNOW,'参数错误或提现记录不存在！');
        if($info->Status != 0)
            throw new CommonException(CommonException::UNKNOW,'请勿重复审核');

        $thrift = new Thrift();
        $client = $thrift->GetClient();
        $main_address = Coin::where('Id',$info->CoinId)->value('MainAddress');

        //-1余额不足，-2手续费不足,1转帐成功 -3 缺少主地址 0失败
        // TransferResult {#687
        //     +status: 1
        //     +msg: "success"
        //     +hash: "0x5895f45de21e86c289eeb3b9ed8c470ab4a64f647e8849887889f89e66422569"
        //   }
        $gas = 80000;
        $gas_price = 12;

        $trans_result = $client->CoinTransferByAddress($info->CoinId, $main_address, $info->Real, $info->Address, $gas, $gas_price);
        if($trans_result->status != 1){
            $info->Status = 3;
            $info->Hash = $trans_result->hash;
            $info->WithdrawInfo = $trans_result->msg;
            $info->ProcessTime = time();
            $info->save();

            switch($trans_result->status){
                case -3:
                    throw new CommonException(CommonException::UNKNOW,'缺少主地址');
                    break;
                case -2:
                    throw new CommonException(CommonException::UNKNOW,'手续费不足');
                    break;
                case -1:
                    throw new CommonException(CommonException::UNKNOW,'余额不足');
                    break;
                case 0:
                    throw new CommonException(CommonException::UNKNOW,'失败');
                    break;
                case 1:
                    // 成功
                    break;
                default:
                    throw new CommonException(CommonException::UNKNOW,'未知错误');
                    break;
            }
        }
        DB::beginTransaction();
        try{
//            $result = DB::table('membercoin')->where('MemberId', $info->MemberId)
//                ->where('CoinId', $info->CoinId)
//                ->where('Forzen', '>=',$info->Money)
//                ->update([
//                    'Forzen' => DB::raw("Forzen - {$info->Money}")
//                ]);
//            if(!$result) throw new CommonException(CommonException::UNKNOW,'未知错误8');

            $result = Withdraw::where('Id',$id)->update([
                'Status' => 2,
                'ProcessMold' => 1,
                'ProcessTime' => time(),
                'Hash' => $trans_result->hash,
                'WithdrawInfo' => $trans_result->msg,
            ]);
            if(!$result) throw new CommonException(CommonException::UNKNOW,'返回信息写入失败');

            DB::commit();
            return true;
        }catch(CommonException $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }
    }

    private static function refuseWithdraw($id)
    {
        $info = Withdraw::find($id);
        if(!$info) throw new CommonException(CommonException::UNKNOW,'参数错误或提现记录不存在！');
        if($info->Status != 0)
            throw new CommonException(CommonException::UNKNOW,'请勿重复审核');

        DB::beginTransaction();
        try{
            $result = DB::table('membercoin')->where('MemberId', $info->MemberId)
                ->where('CoinId', $info->CoinId)
                ->update([
                    'Money' => DB::raw("Money + {$info->Money}"),
                ]);
            $balanceMoney = DB::table('membercoin')->where('MemberId', $info->MemberId)->where('CoinId',$info->CoinId)->value('Money');
            if(!$result) throw new CommonException(CommonException::UNKNOW,'未知错误');
            $result = Withdraw::where('Id',$id)->update([
                'Status' => -1,
                'ProcessMold' => 2,
                'ProcessTime' => time(),
            ]);
            if(!$result) throw new CommonException(CommonException::UNKNOW,'返回信息写入失败');
            $result = DB::table('membercoin')->where('MemberId', $info->MemberId)
                ->where('CoinId', $info->CoinId)
                ->where('Forzen', '>=',$info->Money)
                ->update([
                    'Forzen' => DB::raw("Forzen - {$info->Money}")
                ]);
            if(!$result) throw new CommonException(CommonException::UNKNOW,'未知错误8');
            $FinancingListService = new FinancingListService();
            $install_financingList[] = [
                "MemberId" => $info->MemberId,
                "CoinId" => $info->CoinId,
                "CoinName" => $info->CoinName,
                "AddTime" => time(),
                "Mold" => '42',
                "MoldTitle" => '提现驳回',
                "Money" => $info->Money,
                "Balance" => $balanceMoney,
                "Remark" => '提现驳回',
            ];
               $FinancingListService->add($install_financingList);
//            $result = CoinBill::create([
//                'user_id' => $info->MemberId,
//                'coin_id' => $info->CoinId,
//                'coin_name' => $info->CoinName,
//                'amount' => $info->Money,
//                'remark' => '提现驳回',
//                'type_id' => 1,
//                'foreign_key'=>$id,
//                'tx_type'=>'+',
//                'sn'=>''
//            ]);
//            if(!$result) throw new CommonException(CommonException::UNKNOW,'账单写入失败');

            DB::commit();
            return true;
        }catch(CommonException $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }
    }

    private static function directAgreeWithdraw($id)
    {
        $info = Withdraw::find($id);
        if(!$info) throw new CommonException(CommonException::UNKNOW,'参数错误或提现记录不存在！');
        if($info->Status != 0)
            throw new CommonException(CommonException::UNKNOW,'请勿重复审核！');

        DB::beginTransaction();
        try{
            $result = Withdraw::where('Id',$id)->update([
                'Status' => 2,
                'ProcessMold' => 2,
                'ProcessTime' => time(),
            ]);
            if(!$result) throw new CommonException(CommonException::UNKNOW,'未知错误');
            $balanceMoney = DB::table('membercoin')->where('MemberId', $info->MemberId)->where('CoinId',$info->CoinId)->value('Money');
            $FinancingListService = new FinancingListService();
            $install_financingList[] = [
                "MemberId" => $info->MemberId,
                "CoinId" => $info->CoinId,
                "CoinName" => $info->CoinName,
                "AddTime" => time(),
                "Mold" => '43',
                "MoldTitle" => '提现通过',
                "Money" => $info->Money,
                "Balance" => $balanceMoney,
                "Remark" => '提现通过',
            ];
            $FinancingListService->add($install_financingList);
            $result = DB::table('membercoin')->where('MemberId', $info->MemberId)
                ->where('CoinId', $info->CoinId)
                ->where('Forzen', '>=',$info->Money)
                ->update([
                    'Forzen' => DB::raw("Forzen - {$info->Money}")
                ]);
            if(!$result) throw new CommonException(CommonException::UNKNOW,'未知错误8');
            DB::commit();
            return true;
        }catch(CommonException $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }catch(\Exception $e){
            DB::rollback();
            throw new CommonException(CommonException::UNKNOW,$e->getMessage());
        }
    }

}
