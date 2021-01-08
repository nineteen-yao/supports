<?php
/**
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2021/1/8 11:02
 */


namespace Ynineteen\Supports;

use Swoole\Coroutine\System;

class Lock
{
    /**
     * @var mixed $driver 外面注入的驱动
     */
    public static $driver;

    /** 解析一个key
     * @param array $traces
     * @param int $index
     * @return string
     */
    protected static function getKey($traces, $index = 0)
    {
        $key = __FILE__ . ':' . __LINE__ . ':' . $index;

        if (count($traces) > 1) {
            $trace = $traces[1];

            $key .= ':' . $trace['file'] . ':' . $trace['function'];

            if (isset($trace['class'])) {
                $key .= ':' . $trace['class'] . ':' . $trace['type'];
            }

            $key .= ':' . json_encode($trace['args'], JSON_UNESCAPED_UNICODE);
        }

        return 'lock-' . sha1($key);
    }

    /**
     * 识别驱动，指定的定义驱动有效
     * @return array[]|\string[][]
     */
    protected static function getDriver()
    {
        if (!empty(static::$driver)) {
            //常规redis
            if (static::$driver instanceof \Redis) {
                return static::redisDriver(static::$driver);
            }

            //swoole redis客户端
            if (class_exists('Swoole\Coroutine\Redis') && function_exists('\Co\run') && extension_loaded('swoole')) {
                return static::redisDriver(static::$driver);
            }

            //swoole redis
            return static::$driver;
        }

        //适配laravel
        $laravelRedis = '\Illuminate\Support\Facades\Redis';
        if (class_exists($laravelRedis)) {
            return static::redisDriver($laravelRedis);
        }

        //默认使用文件
        return [
            'get' => [
                __CLASS__,
                'read'
            ],
            'set' => [
                __CLASS__,
                'write'
            ],
            'del' => [
                __CLASS__,
                'del'
            ],
        ];
    }

    protected static function redisDriver($instance)
    {
        return [
            'get' => [
                $instance,
                'get'
            ],
            'set' => [
                $instance,
                'setex'
            ],
            'del' => [
                $instance,
                'del'
            ],
        ];
    }

    protected static function storagePath()
    {
        $ds = DIRECTORY_SEPARATOR;

        $storageRoot = Common::LogRoot();
        if (strlen($storageRoot) > 0) {
            return rtrim(LOGS_PATH, $ds) . '/logs/lock/';
        }

        //其它，框架或者系统，先定义LOGS_PATH路径
        die('先配置LOGS_PATH路径后再使用');
    }

    /**
     * 读取锁状态
     * @param $key
     * @return false|mixed
     */
    protected static function read($key)
    {
        $path = static::storagePath() . $key;

        if (!file_exists($path)) return false;

        if (function_exists('\Co\run') && extension_loaded('swoole')) {
            $contents = System::readFile($path);
        } else {
            $contents = file_get_contents($path);
        }
        
        $data = json_decode($contents, true);
        if (!$data) return false;

        $time = DTime::time();
        if (($data['time'] + $data['expires']) < $time) return false;

        return $data['value'];
    }

    /**
     * 锁写入
     * @param $key
     * @param $value
     * @param $expires
     * @return bool|int
     */
    protected static function write($key, $expires, $value)
    {
        $path = static::storagePath() . $key;

        $data = json_encode([
            'value' => $value,
            'time' => DTime::time(),
            'expires' => $expires
        ]);
        return Logger::write($path, $data);
    }

    /**
     * 删除锁
     * @param $key
     * @return bool
     */
    protected static function del($key)
    {
        $path = static::storagePath() . $key;

        return @unlink($path);
    }

    /**
     * 获取锁
     * @param int $expires
     * @param int $index
     * @return bool
     */
    public static function get($expires = 600, $index = 0)
    {
        $key = static::getKey(debug_backtrace(), $index);

        $value = call_user_func_array(static::getDriver()['get'], [$key]);

        if (!$value) {
            call_user_func_array(static::getDriver()['set'], [$key, $expires, 1]);
        }

        return !$value;
    }

    /**
     * @param int $index
     * @return false|mixed
     */
    public static function release($index = 0)
    {
        $key = static::getKey(debug_backtrace(), $index);

        return call_user_func_array(static::getDriver()['del'], [$key]);
    }
}