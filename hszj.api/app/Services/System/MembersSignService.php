<?php


namespace App\Services\System;


use App\Exceptions\ArException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MembersSignService
{

    /**
     * 签到
     * @param $userId
     * @param $key
     * @param int $type
     * @return bool|void
     */
    public function sign($userId, $key, $type = 1)
    {
        if ($this->dayIsSign($userId, $type)) {
            throw new ArException(ArException::SELF_ERROR, '您今日已经签到过了');
        }
        $url = "http://open-set-api.shenshiads.com/reward/check/$key";
        $result = json_decode($this->get_info($url), true);
        if ($result['code'] == 0 || true) {
            return DB::table('members_sign')->insert([
                'user_id' => $userId,
                'type' => $type,
                'create_time' => time()
            ]);
        }
    }


    private function get_info($url)
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        return $output;
    }


    /**
     * 今日是否签到
     * @param $userId
     * @param $type
     * @return bool
     */
    public function dayIsSign($userId, $type = 1)
    {
        return DB::table('members_sign')->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('create_time', [
                Carbon::now()->startOfDay()->toDateTime()->getTimestamp(),
                Carbon::now()->endOfDay()->toDateTime()->getTimestamp()
            ])
            ->exists();
    }


    /**
     * 昨日是否签到
     * @param $userId
     * @param $type
     * @return bool
     */
    public function yesterdayIsSign($userId, $type = 1)
    {
        return DB::table('members_sign')->where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('create_time', [
                Carbon::now()->subDays()->startOfDay()->toDateTime()->getTimestamp(),
                Carbon::now()->subDays()->endOfDay()->toDateTime()->getTimestamp()
            ])
            ->exists();
    }

}
