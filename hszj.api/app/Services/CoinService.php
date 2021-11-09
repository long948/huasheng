<?php


namespace App\Services;

use App\Events\UserTransactionAmountEvent;
use App\Exceptions\ArException;
use App\Models\CoinModel;
use App\Models\CoinModel as Coin;
use App\Models\MemberCoinModel as MemberCoin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\WithdrawModel as Withdraw;
use App\Models\RechargeModel as Recharge;
use App\Libraries\Thrift;
use App\Models\SettingModel;

class CoinService extends Service
{

    //WithdrawResult WithDraw(1:i32 coin,2:i32 member,3:double money,4:string address,5:string memo)
    /**
     * @method 提现
     */
    public function Withdraw(int $uid, int $coinId, string $money, string $address, string $memo = '')
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($coinId <= 0) throw new ArException(ArException::PARAM_ERROR);
        if (!is_numeric($money)) throw new ArException(ArException::SELF_ERROR, '金额数量错误');
        if (empty($address)) throw new ArException(ArException::SELF_ERROR, '请填写地址');
        //地址 0x开头 42位
        if (strlen($address) != 42 || strstr(strtolower($address), '0x') != 0)
            throw new ArException(ArException::SELF_ERROR, '请填写正确的地址');
        DB::beginTransaction();
        try {
            $coin = Coin::find($coinId);
            if (empty($coin)) throw new ArException(ArException::COIN_NOT_FOUND);
            //是否可提现
            if (!$coin->IsWithDraw)
                throw new ArException(ArException::SELF_ERROR, '该币种不可提现');
            //最大提现数量 最小提现数量
            if (bccomp($money, $coin->MaxWithDraw, 10) > 0)
                throw new ArException(ArException::SELF_ERROR, '最大提现数量不得大于' . $coin->MaxWithDraw);
            if (bccomp($money, $coin->MinWithDraw, 10) < 0)
                throw new ArException(ArException::SELF_ERROR, '最低提现数量不得低于' . $coin->MinWithDraw);
            //检查余额
            $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
            if (empty($memberCoin)) throw new ArException(ArException::SELF_ERROR, '您未持有' . $coin->EnName);
            if (bccomp($memberCoin->Money, $money, 10) < 0) throw new ArException(ArException::COIN_NOT_ENOUGH);
            //计算手续费
            $fee = bcmul($money, $coin->WithDrawFee, 10);

            if (bccomp($fee, $coin->MinWithDrawFee, 10) < 0)
                $fee = $coin->MinWithDrawFee; //最低手续费

            $real = bcsub($money, $fee, 10);
            if (bccomp($real, 0, 10) <= 0)
                throw new ArException(ArException::SELF_ERROR, '提现数量错误');
            //Recharge Insert
            $recharge = [
                'Address' => $address,
                'Balance' => bcsub($memberCoin->Money, $money, 10),
                'MemberId' => $uid,
                'Mobile' => $memberCoin->member->Phone,
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'Money' => $money,
                'Remark' => $memo,
                'Real' => $real,
                'Status' => $coin->IsAutoWithDraw == 1 ?: 0,
                'AddTime' => time(),
                'Fee' => $fee,
                'FeeCoin' => $coin->Id,
                'FeeCoinEname' => $coin->EnName,
                'Hash' => '',
                'ProcessTime' => 0,
                'WithdrawInfo' => '',
                'ProcessMold' => 0
            ];
            $res = DB::table('Withdraw')->insert($recharge);
            if ($res !== true) throw new ArException(ArException::SELF_ERROR, '提现失败，请稍后再试');
            //MemberCoin Update
            DB::table('MemberCoin')->where('MemberId', $uid)->where('CoinId', $coin->Id)->update([
                'Money' => DB::raw("Money - {$money}"),
                'Forzen' => DB::raw("Forzen + {$money}")
            ]);
            DB::commit();
        } catch (ArException $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::NET_WORK_ERROR);
        }
    }

    /**
     * @method 获取充值地址
     */
    public function RechargeAddress(int $uid, int $coinId)
    {
        $coin = CoinModel::GetById($coinId);
        if (empty($coin)) {
            throw new ArException(ArException::COIN_NOT_FOUND);
        }

        if ($coin->is_platform == 1) {
            $memberService = new MemberService();
            $userAddress = MemberCoin::where('MemberId', $uid)->where('CoinId', $coinId)->value('Address') ?? '';
            if (!empty($userAddress)) {
                return $userAddress;
            }
            $address = $memberService->generate_str();
            MemberCoin::where('MemberId', $uid)->where('CoinId', $coinId)->update(['address' => $address]);
            return $address;
        }

        try {
            $thrift = $this->GetThrift();
            $address = $thrift->GetAddress($uid, $coinId);
            if ($address->status !== 1) throw new ArException(ArException::SELF_ERROR, '上行获取失败');
            $address = $address->address;
            return $address;
        } catch (ArException $e) {

        } catch (\Exception $e) {
            throw new ArException(ArException::SELF_ERROR, '上行获取失败');
        }

    }

    /**
     * @method 充值详情
     * @param int $uid 用户Id
     * @param int $id 充值ID
     */
    public function RechargeDetail(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $detail = Recharge::where('Id', $id)->where('MemberId', $uid)->first();
        if (empty($detail)) throw new ArException(ArException::PARAM_ERROR);
        $data = [
            'RecvAddress' => $detail->Address,
            'Hash' => $detail->Hash,
            'Status' => $detail->Status
        ];
        return $data;
    }

    /**
     * @method 提币详情
     * @param int $uid 用户Id
     * @param int $id 提币ID
     */
    public function WithdrawDetail(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $detail = Withdraw::where('Id', $id)->where('MemberId', $uid)->first();
        if (empty($detail)) throw new ArException(ArException::PARAM_ERROR);
        $data = [
            'RecvAddress' => $detail->Address,
            'Hash' => $detail->Hash,
            'Status' => $detail->Status,
            'Fee' => $detail->Fee
        ];
        return $data;
    }

    /**
     * @method 获取单个币种余额
     * @param int $uid 用户Id
     * @param int $id 币种Id
     */
    public function SingleBalance(int $uid, int $id)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);

        $coin = Coin::find($id);
        if (empty($coin)) throw new ArException(ArException::COIN_NOT_FOUND);
        $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $id)->first();
        //没有则添加
        if (empty($memberCoin)) {
            $newId = DB::table('MemberCoin')->insertGetId([
                'MemberId' => $uid,
                'CoinId' => $id,
                'CoinName' => $coin->EnName
            ]);
            return [
                'Id' => $newId,
                'CoinId' => $id,
                'MemberId' => $uid,
                'Money' => 0,
                'LockMoney' => 0,
                'Forzen' => 0,
                'IsWithDraw' => $coin->IsWithDraw,
                'IsRecharge' => $coin->IsRecharge
            ];
        }
        return [
            'Id' => $memberCoin->Id,
            'CoinId' => $memberCoin->CoinId,
            'MemberId' => $memberCoin->MemberId,
            'Money' => number($memberCoin->Money),
            'LockMoney' => number($memberCoin->LockMoney),
            'Forzen' => number($memberCoin->Forzen),
            'IsWithDraw' => $coin->IsWithDraw,
            'IsRecharge' => $coin->IsRecharge
        ];
    }

    /**
     * @method 获取币种余额
     * @param int $uid 用户Id
     */
    public function Balance(int $uid)
    {
        if ($uid <= 0) throw new ArException(ArException::UNKONW);
        $coins = Coin::where('Status', 1)->get();
        $list = [];
        foreach ($coins as $coin) {
            $memberCoin = MemberCoin::where('MemberId', $uid)->where('CoinId', $coin->Id)->first();
            if (empty($memberCoin)) {
                $list[] = [
                    'Id' => 0,
                    'CoinId' => $coin->Id,
                    'CoinName' => $coin->EnName,
                    'MemberId' => $uid,
                    'Money' => 0,
                    'LockMoney' => 0,
                    'Forzen' => 0,
                    'Logo' => $coin->Logo
                ];
            } else {
                $list[] = [
                    'Id' => $memberCoin->Id,
                    'CoinId' => $memberCoin->CoinId,
                    'CoinName' => $memberCoin->CoinName,
                    'MemberId' => $memberCoin->MemberId,
                    'Money' => number($memberCoin->Money),
                    'LockMoney' => number($memberCoin->LockMoney),
                    'Forzen' => number($memberCoin->Forzen),
                    'Logo' => $coin->Logo
                ];
            }
        }
        $memberCoin = MemberCoin::where('MemberId', $uid)->get();
        return $list;
    }

    /**
     * @method 根据Id获取币种
     * @param int $id 币种Id
     */
    public function Single(int $id)
    {
        if ($id <= 0) throw new ArException(ArException::PARAM_ERROR);
        $coin = Coin::find($id);
        if (empty($coin)) throw new ArException(ArException::PARAM_ERROR);
        $data = [
            'Id' => $coin->Id,
            'Name' => $coin->Name,
            'EnName' => $coin->EnName,
            "FullName" => $coin->FullName,
            'Logo' => $coin->Logo,
            'IsWithDraw' => $coin->IsWithDraw,
            'IsRecharge' => $coin->IsRecharge,
            'MinWithDraw' => $coin->MinWithDraw,
            'MaxWithDraw' => $coin->MaxWithDraw,
            'WithDrawFee' => $coin->WithDrawFee,
            'MinWithDrawFee' => $coin->MinWithDrawFee,
            'Fixed' => $coin->Fixed,
            'Status' => $coin->Status
        ];
        return $data;
    }

    /**
     * @method 获取币种列表
     */
    public function List($userId)
    {
        $list = Coin::query()->where('Status', 1)->get();
        $coins = [];
        foreach ($list as $item) {
            $coins[] = [
                'Id' => $item->Id,
                'Name' => $item->Name,
                'EnName' => $item->EnName,
                "FullName" => $item->FullName,
                'Price' => $item->Price,
                'Logo' => $item->Logo,
                'IsWithDraw' => $item->IsWithDraw,
                'IsRecharge' => $item->IsRecharge,
                'MinWithDraw' => $item->MinWithDraw,
                'MaxWithDraw' => $item->MaxWithDraw,
                'WithDrawFee' => $item->WithDrawFee,
                'MinWithDrawFee' => $item->MinWithDrawFee,
                'Fixed' => $item->Fixed,
                'Status' => $item->Status,
                //是否是平台币
                'isPlatform' => $item->is_platform,
                'RechargeInfo' => $item->RechargeInfo,
                'WithDrawInfo' => $item->WithDrawInfo,
                'PriceCny' => $item->PriceCny,
                'user' => getUserAmountByCoin($userId, $item->Id)
            ];
        }
        return $coins;
    }
    

    /**
     * @method 充提记录
     * @param int $uid 用户Id
     * @param int $id 币种Id
     * @param int $count 分页参数
     */
    public function RechargeAndWithdraw(int $uid, int $id, int $count, int $type)
    {
        if ($uid <= 0) {
            throw new ArException(ArException::UNKONW);
        }
        if ($count <= 0) {
            throw new ArException(ArException::PARAM_ERROR);
        }

        $r = DB::table('recharge')
            ->when($id, function ($query) use ($id) {
                return $query->where('CoinId', $id);
            })
            ->when($uid, function ($query) use ($uid) {
                return $query->where('MemberId', $uid);
            })
            ->select(['Id', 'Address', 'Status', 'Hash', DB::raw('2 as Type'), 'Money', 'CoinName', 'CoinId', 'AddTime', 'MemberId']);


        $log = DB::table('withdraw')
            ->when($id, function ($query) use ($id) {
                return $query->where('CoinId', $id);
            })
            ->when($uid, function ($query) use ($uid) {
                return $query->where('MemberId', $uid);
            })
            ->select(['Id', 'Address', 'Status', 'Hash', DB::raw('2 as Type'), 'Money', 'CoinName', 'CoinId', 'AddTime', 'MemberId']);

        if ($type == 0) {
            $log->union($r);
            $log = $log->orderBy('AddTime', 'desc')->paginate($count);
        } else if ($type == 1) {
            $log = $r->orderBy('AddTime', 'desc')->paginate($count);
        } else {
            $log = $log->orderBy('AddTime', 'desc')->paginate($count);
        }

        $list = [];
        foreach ($log as $item) {
            $temp = [
                'Id' => $item->Id,
                'Type' => $item->Type,
                'CoinId' => $item->CoinId,
                'CoinName' => $item->CoinName,
                'Money' => $item->Money,
                'AddTime' => Carbon::createFromTimestamp($item->AddTime)->addHours(8)->getTimestamp(),
                'Status' => $item->Status
            ];
            if ($item->Type == 2) {
                $temp['AddTime'] = $item->AddTime;
            }
            $list[] = $temp;
        }

        return ['list' => $list, 'total' => $log->total()];
    }


    //thrift 客户端
    protected $_thrift = null;

    //获取thrift客户端
    protected function GetThrift()
    {
        if ($this->_thrift !== null) return $this->_thrift;
        $thrift = new Thrift();
        $this->_thrift = $thrift->GetClient();
        return $this->_thrift;
    }

    /**
     * 转账
     */
    public function exchange(int $uid, int $coin_id, float $amount, string $address, string $remark)
    {
        if ($uid <= 0) throw new ArException(ArException::SELF_ERROR, '参数错误');
        if ($amount <= 0) throw new ArException(ArException::SELF_ERROR, '参数错误');
        if (empty($address)) throw new ArException(ArException::SELF_ERROR, '请填写地址');
        //地址 0x开头 42位
        if (strlen($address) != 42 || strstr(strtolower($address), '0x') != 0)
            throw new ArException(ArException::SELF_ERROR, '请填写正确的地址');
        $to_uid = MemberCoin::where([
                'Address' => $address,
                'CoinId' => $coin_id,
            ])->value('MemberId') ?? 0;
        if ($to_uid <= 0) throw new ArException(ArException::SELF_ERROR, '该地址不存在');
        if ($uid == $to_uid) throw new ArException(ArException::SELF_ERROR, '不能给自己转账');

        $money = MemberCoin::where([
                'MemberId' => $uid,
                'CoinId' => $coin_id,
            ])->value('Money') ?? 0;
        if ($money < $amount) throw new ArException(ArException::SELF_ERROR, '余额不足');

        $tx_fee = DB::table('Members')->where('Id', $uid)->value('Fee');
        // $tx_fee = SettingModel::getValueByKey('exchange_fee');
        //冻结数量
        $feeNumber = bcmul($tx_fee, $amount, 10);
        $tx_amount = bcadd($feeNumber, $amount, 10);
        if ($money < $tx_amount) throw new ArException(ArException::SELF_ERROR, '手续费不足');

        DB::beginTransaction();
        try {
            $res = DB::table('exchange')->insert([
                'from_uid' => $uid,
                'to_uid' => $to_uid,
                'coin_id' => $coin_id,
                'amount' => $amount,
                'fee' => $feeNumber,
                'remark' => $remark,
                'created_at' => time()
            ]);
            if (!$res) throw new Exception('转账失败，请稍后再试');
            $coin = Coin::where('Id', $coin_id)->first();
            $res = DB::table('MemberCoin')->where('MemberId', $uid)
                ->where('CoinId', $coin_id)
                ->where('Money', '>=', $tx_amount)
                ->decrement('Money', $tx_amount);
            if (!$res) throw new Exception('转账失败，请稍后再试');
            self::AddLog($uid, -$tx_amount, $coin, 'exchange_out');

            $res = DB::table('MemberCoin')->where('MemberId', $to_uid)
                ->where('CoinId', $coin_id)
                ->increment('Money', $amount);
            if (!$res) throw new Exception('转账失败，请稍后再试');
            self::AddLog($to_uid, $amount, $coin, 'exchange_in');

            //模拟提现 链上数据
            $recharge = [
                'Address' => $address,
                'Balance' => 0,
                'MemberId' => $uid,
                'Mobile' => '',
                'CoinId' => $coin->Id,
                'CoinName' => $coin->EnName,
                'Money' => $amount,
                'Remark' => '',
                'Real' => $amount,
                'Status' => 0,
                'AddTime' => time(),
                'Fee' => $feeNumber,
                'FeeCoin' => $coin->Id,
                'FeeCoinEname' => $coin->EnName,
                'Hash' => '',
                'ProcessTime' => 0,
                'WithdrawInfo' => '',
                'ProcessMold' => 0
            ];
            $res = DB::table('Withdraw')->insert($recharge);
            if (!$res) throw new Exception('转账失败，请稍后再试');
            DB::commit();

            event(new UserTransactionAmountEvent($to_uid, $amount));

        } catch (\Exception $e) {
            DB::rollBack();
            throw new ArException(ArException::SELF_ERROR, $e->getMessage());
        }

        return true;
    }
}
