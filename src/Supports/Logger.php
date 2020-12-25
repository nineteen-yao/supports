<?php
/**
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2020/12/10 15:57
 */


namespace Ynineteen\Supports;

use Swoole\Coroutine\System;

class Logger
{
    protected static function filePath($type)
    {
        $ds = DIRECTORY_SEPARATOR;

        $rectivePath = $ds . 'logs' . $ds . $type . '-' . date('Ymd', time()) . '.log';

        if (defined('LOGS_PATH')) {
            return rtrim(LOGS_PATH, $ds) . $ds . ltrim($rectivePath, $ds);
        }

        //适配laravel
        if (class_exists('\Illuminate\Support\Facades\App') && function_exists('storage_path')) {
            return storage_path() . $rectivePath;
        }
        //适配thinkphp6以上版本
        if (class_exists('\think\App') && function_exists('app')) {
            return app()->getRuntimePath() . $rectivePath;
        }

        //适配yii2
        if (class_exists('\Yii')) {
            return \Yii::$app->getRuntimePath() . $rectivePath;
        }

        //其它，框架或者系统，先定义LOGS_PATH路径
        die('先配置LOGS_PATH路径后再使用');
    }

    /**
     * 将对象和数组类型转化为字符串
     * @param $arg
     * @return bool|string
     */
    public static function toString($arg)
    {
        if (is_object($arg)) {
            if ($arg instanceof \Throwable) {
                $errmsg = PHP_EOL;
                $errmsg .= '错误代码：' . $arg->getCode() . PHP_EOL;
                $errmsg .= '错误信息：' . $arg->getMessage() . PHP_EOL;
                $errmsg .= '错误行数：' . $arg->getLine() . PHP_EOL;
                $errmsg .= '错误文件：' . $arg->getFile() . PHP_EOL;
                $errmsg .= '错误追踪：' . $arg->getTraceAsString() . PHP_EOL;

                $arg = $errmsg;
            } else {
                $arg = (array)$arg;
            }
        }

        if (is_array($arg)) {
            $arg = json_encode($arg, JSON_UNESCAPED_UNICODE);
        }

        return $arg;
    }

    /**
     * @param $file
     * @param $data
     * @param null $flag
     * @return bool|int
     */
    public static function write($file, $data, $flag = null)
    {
        $data = self::toString($data);

        $dir = dirname($file);

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        //swoole的协程环境下
        if (function_exists('\Co\run') && extension_loaded('swoole')) {
            \Co\run(function () use ($file, $data, $flag) {
                System::writeFile($file, $data, FILE_APPEND);
            });

            return true;
        }
        return file_put_contents($file, $data, $flag);
    }

    /**
     * 解析数据题
     * @param array $argsList
     * @return false|string
     */
    public static function parse($argsList = [])
    {
        if (empty($argsList)) return false;

        $content = '[' . date('Y-m-d H:i:s', time()) . ']';
        if (count($argsList) === 1) {
            $content .= self::toString($argsList[0]) . PHP_EOL;
        } else {
            foreach ($argsList as $i => $arg) {
                $content .= ($i + 1) . ')';

                $arg = self::toString($arg);

                $content .= (string)$arg . '，';
            }

            $content = rtrim($content, '，');

            $content .= PHP_EOL . '########################################' . PHP_EOL;
        }

        return $content;
    }

    /**
     * 通用日志
     * @param mixed ...$data
     * @return bool|int|void
     */
    public static function record(...$data)
    {
        $content = static::parse(func_get_args());
        if ($content === false) return;

        $file = static::filePath(__FUNCTION__);

        return static::write($file, $content, FILE_APPEND);
    }

    public static function notice(...$data)
    {
        $content = static::parse(func_get_args());
        if ($content === false) return;

        $file = static::filePath(__FUNCTION__);

        return static::write($file, $content, FILE_APPEND);
    }

    public static function error(...$data)
    {
        $content = static::parse(func_get_args());
        if ($content === false) return;

        $file = static::filePath(__FUNCTION__);

        return static::write($file, $content, FILE_APPEND);
    }

    public static function debug(...$data)
    {
        $content = static::parse(func_get_args());
        if ($content === false) return;

        $file = static::filePath(__FUNCTION__);

        return static::write($file, $content, FILE_APPEND);
    }

    public static function info(...$data)
    {
        $content = static::parse(func_get_args());
        if ($content === false) return;

        $file = static::filePath(__FUNCTION__);

        return static::write($file, $content, FILE_APPEND);
    }
}