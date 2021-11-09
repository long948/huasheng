<?php


namespace App\Utils;

/**
 * 数字工具类
 * Class RandomUtil
 * @package App\Common\Util
 */
class RandomUtil
{

    /**
     * 权重随机数
     * @param $data
     * @param $number
     * @return array
     */
    public static function weightsRandom($data, $number)
    {
        //个数
        $size = count($data);
        //总的权重
        $accumulateVal = 0;
        foreach ($data as $datum) {
            $accumulateVal += $datum;
        }
        $result = [];
        for ($i = 0; $i < $number; $i++) {
            $tempSum = 0;
            $ranIndex = rand(0, $accumulateVal);
            for ($j = 0; $j < $size; $j++) {
                $tempSum += $data[$j];
                if ($ranIndex <= $tempSum + $data[$j]) {
                    $result[$i] = $j + 1;
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * 范围随机数
     * @param $min
     * @param $max
     * @return float
     */
    public static function rangeRandom($min, $max)
    {
        return bcadd($min + mt_rand() / mt_getrandmax() * ($max - $min), 0, 6);
    }


    /**
     * @return string
     * @throws \Exception
     */
    public static function nextId()
    {
        $prefix = date('Ymd');
        return $prefix . random_int(100000, 999999) . substr(microtime(true), -4);
    }

}
