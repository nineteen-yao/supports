<?php
/**
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2021/1/8 12:48
 */


namespace Ynineteen\Supports;


class Common
{
    /**
     * 在各种框架下的日志存储目录
     * @return string
     */
    public static function LogRoot()
    {
        if (defined('LOGS_PATH')) {
            return LOGS_PATH;
        }

        //适配laravel
        if (class_exists('\Illuminate\Support\Facades\App') && function_exists('storage_path')) {
            return storage_path();
        }
        //适配thinkphp6以上版本
        if (class_exists('\think\App') && function_exists('app')) {
            return app()->getRuntimePath();
        }

        //适配yii2
        if (class_exists('\Yii')) {
            return \Yii::$app->getRuntimePath();
        }

        return '';
    }
}