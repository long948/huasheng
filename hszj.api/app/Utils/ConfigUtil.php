<?php


namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 配置工具类
 * Class ConfigUtil
 * @package App\Common\Util
 */
class ConfigUtil
{

    /**
     * 配置缓存过期时间
     * @var int
     */
    private static $timeOut = 7200;


    /**
     * 获取配置
     * @param $configName
     * @return mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function get($configName)
    {
        if (empty($configName)) {
            return false;
        }
        $key = "config:{$configName}";
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            $val = DB::table('config')->where('k', $configName)->value('v');
            if ($val) {
                Cache::set($key, $val, self::$timeOut);
            }
            return $val;
        }
    }
}
