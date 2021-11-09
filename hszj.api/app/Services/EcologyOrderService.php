<?php


namespace App\Services;


use App\Exceptions\ArException;
use App\Models\CoinModel as Coin;
use App\Utils\Enum\Enums;
use App\Utils\RedisLock;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class EcologyOrderService extends Service
{


    /**
     * 提交充值订单
     * @param $user_id
     * @param $type
     * @param $child_type
     * @param $nickname
     * @param $phone
     * @param $address
     * @param $card_num
     * @param $card_id
     * @param $amount
     * @return bool
     * @throws ArException
     */
    public function order($user_id, $type, $child_type, $nickname, $phone, $address, $card_num, $card_id, $amount)
    {

        if (!in_array($type, ['1', '2'])) {
            throw new ArException(ArException::SELF_ERROR, '充值类型错误');
        }
        $data = $this->info()[$type == 1 ? 'phone' : 'oil'];
        if (empty($amount)) {
            throw new ArException(ArException::SELF_ERROR, '选择的充值金额错误');
        }
        if (!in_array($amount, $data)) {
            throw new ArException(ArException::SELF_ERROR, '充值金额不存在');
        }
        return RedisLock::lock(Enums::REDIS_LOCK_KEY['USER_ECOLOGY_ORDER'] . $user_id, function ()
        use ($user_id, $type, $child_type, $nickname, $phone, $address, $card_num, $card_id, $amount) {

            if (empty($nickname) || strlen($nickname) > 20) {
                throw new ArException(ArException::SELF_ERROR, '姓名格式错误');
            }
            if (empty($card_num) || strlen($card_num) > 20) {
                throw new ArException(ArException::SELF_ERROR, '充值卡号格式错误');
            }
            if (empty($amount)) {
                throw new ArException(ArException::SELF_ERROR, '充值金额不能为空');
            }

            if ($type == 2) {
                if (!in_array($child_type, ['1', '2'])) {
                    throw new ArException(ArException::SELF_ERROR, '油卡类型错误');
                }
                if (empty($address) || strlen($address) > 100) {
                    throw new ArException(ArException::SELF_ERROR, '地址格式错误');
                }
                if (empty($card_id) || strlen($card_id) != 18) {
                    throw new ArException(ArException::SELF_ERROR, '身份证号码格式错误');
                }
            }

            $ct_price = DB::table('Setting')->where('k', 'tx_price')->value('v');
            $total_amount = bcdiv($amount, bcmul($ct_price, 7, 4), 4);

            //按照出售手续费重新计算
            $fee = DB::table('Members')->where('Id', $user_id)->value('Fee');
            if ($fee > 0) {
                $total_amount += bcmul($total_amount, $fee, 4);
            }

            //换算金额
            $coin = Coin::GetByEnName();
            $userAmount = getUserAmountByCoin($user_id, $coin->Id);
            if ($userAmount->Money < $total_amount) {
                throw new ArException(ArException::SELF_ERROR, '您的余额不足');
            }
            try {
                DB::beginTransaction();
                //扣钱
                DB::table('MemberCoin')->where('MemberId', $user_id)->where('CoinId', $coin->Id)->update([
                    'Money' => DB::raw("Money-$total_amount")
                ]);
                //添加账单记录
                self::AddLog($user_id, (-$total_amount), $coin, 'ecology_order');
                DB::table('ecology_order')->insert([
                    'user_id' => $user_id,
                    'type' => $type,
                    'child_type' => $child_type,
                    'nickname' => $nickname,
                    'phone' => $phone,
                    'address' => $address,
                    'card_num' => $card_num,
                    'card_id' => $card_id,
                    'amount' => $amount,
                    'deduction_amount' => $total_amount,
                    'price' => bcmul($ct_price, 7, 4),
                    'create_time' => dateFormat()
                ]);
                DB::commit();
                return true;
            } catch (Exception $e) {
                DB::rollBack();
                throw new ArException(ArException::SELF_ERROR, '网络繁忙,请稍后再试');
            }
        });
    }

    /**
     * 查询订单
     * @param $userId
     * @param $page
     * @param $count
     * @return Collection
     */
    public function orderList($userId, $page, $count)
    {
        $offset = $page <= 0 ? 1 : $page;
        $limit = $count > 20 || $count <= 0 ? 20 : $count;
        $offset = ($offset - 1) * $limit;

        return DB::table('ecology_order')->where('user_id', $userId)
            ->orderBy('create_time', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()->each(function (&$key, $val) {
                if ($key->type == 1) {
                    $key->type = '话费充值';
                } else {
                    $key->type = '油卡充值';
                    if ($key->child_type == 1) {
                        $key->child_type = '中石化';
                    } else {
                        $key->child_type = '中石油';
                    }
                }
                switch ($key->status) {
                    case 0:
                        $key->status = '已提交,正在处理';
                        break;
                    case 1:
                        $key->status = '处理成功,请查收';
                        break;
                    case 2:
                        $key->status = '处理失败';
                        break;
                    case 3:
                        $key->status = '已被系统驳回';
                        break;
                    default:
                        $key->status = '未知状态';
                }
            });
    }


    public function info()
    {
        $result['phone'] = [10, 30, 50, 100];
        $result['oil'] = [100, 300, 500];
        $result['oil_remarks'] = DB::table('Setting')->where('k', 'oil_remarks')->value('v');
        $result['phone_remarks'] = DB::table('Setting')->where('k', 'phone_remarks')->value('v');
        $result['price'] = bcmul(DB::table('Setting')->where('k', 'tx_price')->value('v'), '7', 4);
        return $result;
    }
}
