<?php


namespace App\Utils;


use App\Exceptions\ArException;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

/**
 * redis锁
 * Class RedisLock
 * @package App\Utils
 */
class RedisLock
{

    /**
     * 锁
     * @param $key 键
     * @param $callback
     * @param int $ltt 运行时间
     * @param int $timeOut 超时等待时间
     * @return mixed
     */
    public static function lock($key, $callback, $ltt = 5, $timeOut = 5)
    {
        return Cache::lock($key, $ltt)->block($timeOut, function () use ($callback) {
            if (!is_callable($callback)) {
                return null;
            }
            return $callback();
        });
    }
}
