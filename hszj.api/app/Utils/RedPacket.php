<?php


namespace App\Utils;


/**
 * 根据总金额生成指定个数的随机红包
 * 1、 微信红包， 每人最少分得1分钱
 * 2、 每人分得到的金额是随机分配的
 * 3、 每次生成红包就生成了对应领取红包的结果
 * 生成随机红包
 * Class redPacket
 */
class RedPacket
{
    //总金额
    private $total = 0;
    //红包数量
    private $amount = 0;
    //最小红包金额
    private $min = 0.01;

    public function __construct($total, $amount, $min)
    {
        $this->total = $total;
        $this->amount = $amount;
        $this->min = $min;
    }

    /**
     * @return bool
     */
    public function getPacket()
    {
        $total = $this->total;
        $amount = $this->amount;
        $min = $this->min;
        if ($amount * $min > $total) {
            return false;
        }
        $money = 0;
        for ($i = 1; $i < $amount; $i++) {
            $safe_total = ($total - ($amount - $i) * $min) / ($amount - $i);//随机安全上限
            if ($min < $safe_total) {
                $money = mt_rand($min * 100, $safe_total * 100) / 100;
            } else {
                $money = $min;
            }

            $total = $total - $money;
            $redPacket[] = $money;
        }
        $redPacket[] = $total;
        return $redPacket;
    }
}
