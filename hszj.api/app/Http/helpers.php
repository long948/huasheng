<?php

use App\Models\MemberCoinModel as MemberCoin;

/**
 * 替换手机号
 * @param $mobile 手机号
 * @param string $data 替换字符串
 * @return string|string[]|null
 */
function replaceMobile($mobile, $data = '****')
{
    $pattern = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = "$1$data$3";
    return preg_replace($pattern, $replacement, $mobile);
}

/**
 * 数字转型
 * @param int $digital 数字
 * @param int $number 转型保留个数
 * @return string
 */
function number($digital = 0, $number = 8)
{
    return sprintf('%.' . $number . 'F', $digital);
}

/**
 * 时区
 * @param $time
 * @param float|int $jetLag
 * @return false|float|int|string
 */
function timeZone($time, $jetLag = 60 * 60 * 8)
{
    if (is_numeric($time))
        return $time - $jetLag;
    else
        return date('Y-m-d H:i:s', strtotime($time) - $jetLag);
}

/**
 * 是否是手机号码
 * @param $mobile 手机号码
 * @return bool
 */
function isMobile($mobile)
{
    if (preg_match("/^1[3456789]\d{9}$/", $mobile))
        return true;
    else
        return false;
}


/**
 * 请求接口返回内容
 * @param string $url [请求的URL地址]
 * @param string $params [请求的参数]
 * @param int $ipost [是否采用POST形式]
 * @return  string
 */
function SendRequest($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

if (!function_exists('getDomain')) {
    /**
     * 获取七牛云域名
     * @return string
     */
    function getDomain(): string
    {
        $domain = DB::table('QiniuConfig')->first(['Domain']);
        return $domain->Domain ?? '';
    }
}
if (!function_exists('getDay')) {
    /**
     * @return array
     * 获取今日开始及结束日期
     */
    function getDay(): array
    {
        //php获取今日开始时间戳和结束时间戳
        $data['beginTime'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $data['endTime'] = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        return $data;
    }
}

if (!function_exists('dateFormat')) {
    /**
     * 时间格式化
     * @param $time
     * @param string $format
     * @return false|string
     */
    function dateFormat($time = '', $format = 'Y-m-d H:i:s')
    {
        if (empty($time)) {
            return date($format, time());
        }
        return date($format, $time);
    }
}

if (!function_exists('userParents')) {
    /**
     * 获取用户往上代数
     * @param $user_id 用户编号
     * @param $algebra 代数
     * @return array
     */
    function userParents($user_id, $algebra)
    {
        $user = DB::table('Members')->where('Id', $user_id)->first(['Root']);
        $parent = array_reverse(explode(',', substr($user->Root, 0, -1)));
        $length = count($parent);
        return array_slice($parent, 0, $algebra < $length ? $algebra : $length);
    }
}

if (!function_exists('userChild')) {
    /**
     * 获取我的团队
     * @param $user_id 用户本身
     * @param int $algebra 往下数代数
     * @return array
     */
    function userChild($user_id, $algebra = 6)
    {
        $user = \App\Models\MembersModel::find($user_id);
        $users = \App\Models\MembersModel::select(['IsAuth', 'Root', 'Id'])->where('id', '>=', $user_id)->get();
        $data = [];
        foreach ($users as &$u) {
            if ($u->IsAuth == 0) {
                continue;
            }
            $roots = array_reverse(explode(',', trim($u->Root, ',')));
            $length = count($roots);
            if ($length > 0) {

                $temp = array_slice($roots, 0, $algebra > $length ? $length : $algebra);
                if (in_array($user['Id'], $temp) && $user['Id'] != $u['Id']) {
                    $data[] = $u['Id'];
                }
            }
        }
        return $data;
    }
}

if (!function_exists('getCoinIdByName')) {
    /**
     * 根据名称获取币种编号
     * @param string $name
     * @return int|mixed
     */
    function getCoinIdByName($name = 'PT')
    {
        $coin = DB::table('Coin')->where('EnName', $name)->first();
        if ($coin) {
            return $coin;
        }
        return [];
    }
}

if (!function_exists('getUserAmountByCoin')) {
    /**
     * 获取用户账户信息
     * @param $user_id
     * @param int $coin_id
     * @return int|mixed
     */
    function getUserAmountByCoin($user_id, $coin_id = 0)
    {
        $memberCoin = MemberCoin::where('MemberId', $user_id)->where('CoinId', $coin_id)->first();
        //没有则添加
        if (empty($memberCoin)) {
            $coin = DB::table('Coin')->where('Id', $coin_id)->first();
            $newId = DB::table('MemberCoin')->insertGetId([
                'MemberId' => $user_id,
                'CoinId' => $coin_id,
                'CoinName' => $coin->EnName
            ]);
            return collect([
                'Id' => $newId,
                'CoinId' => $coin_id,
                'MemberId' => $user_id,
                'Money' => 0,
                'LockMoney' => 0,
                'Forzen' => 0,
                'IsWithDraw' => $coin->IsWithDraw,
                'IsRecharge' => $coin->IsRecharge
            ]);
        }
        $memberCoin->Money = bcsub($memberCoin->Money, 0, 4);
        return $memberCoin;
    }
}

if (!function_exists('userIsAuth')) {
    function userIsAuth($user_id)
    {
        $isAuth = DB::table('Members')->where('Id', $user_id)->value('isAuth');
        return $isAuth == 1 ? true : false;
    }
}

if (!function_exists('basieEvent')) {
    /**
     * 延长周期天数
     */
    function basieEvent()
    {
        //基础时间
        $basie_event = DB::table('setting')->where('k', 'basie_event')->value('v') ?? 0;
        //延长天数
        $delay_period = DB::table('setting')->where('k', 'delay_period')->value('v') ?? 0;
        $s = $basie_event;
        $e = date('Y-m', time());;
        $start = new \DateTime($s);
        $end = new \DateTime($e);
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($start, $interval, $end);
        $count = 0;
        foreach ($period as $dt) {
            $count++;
        }
        return $count * $delay_period;
    }
}


if (!function_exists('getUserConventionalLevelId')) {
    function getUserConventionalLevelId($userId)
    {
        return DB::table('miner_user_level')->where('user_id', $userId)->value('miner_level_id');
    }
}


if (!function_exists('getUserConventionalLevelName')) {
    function getConventionalLevelName($levelId)
    {
        return DB::table('miner_level')->where('level', $levelId)->value('nickname');
    }
}


if (!function_exists('getUserIsPrincipal')) {
    /**
     * 用户是否解锁本金
     * @param $userId
     * @return mixed
     */
    function getUserIsPrincipal($userId)
    {
        $is_principal = DB::table('Members')->where('Id', $userId)->value('is_principal');
        if ($is_principal) {
            return true;
        }
        return false;
    }
}
