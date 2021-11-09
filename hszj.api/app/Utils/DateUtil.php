<?php


namespace App\Utils;

/**
 * 时间工具类
 * Class DateUtil
 * @package App\Common\Util
 */
class DateUtil
{

    /**
     * @return array
     * 获取今日开始及结束日期
     */
    public static function getDay(): array
    {
        //php获取今日开始时间戳和结束时间戳
        $data['beginTime'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $data['endTime'] = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        return $data;
    }


    /**
     *
     * 获取指定年月的开始和结束时间戳
     * @param int $year 年份
     * @param int $month 月份
     * @return array(开始时间,结束时间)
     */
    public static function getMonthBeginAndEnd($year = 0, $month = 0)
    {
        $year = $year ? $year : date('Y');
        $month = $month ? $month : date('m');
        $d = date('t', strtotime($year . '-' . $month));
        return [
            'beginTime' => strtotime($year . '-' . $month),
            'endTime' => mktime(23, 59, 59, $month, $d, $year)
        ];
    }


    /**
     * 在范围内随机时间
     * @param $beginTime 开始时间
     * @param $endTime 结束时间
     * @param int $num 次数
     * @param bool $is 是否格式化
     * @return array
     */
    public static function randomDate($beginTime, $endTime, $num = 30, $is = true)
    {
        $begin = strtotime($beginTime);
        $end = strtotime($endTime);
        $array = array();
        $i = 0;
        while ($i < $num) {
            $date = rand($begin, $end);
            $array[] = $is ? date("Y-m-d H:i:s", $date) : $date;
            $i++;
        }
        return $array;
    }

}
