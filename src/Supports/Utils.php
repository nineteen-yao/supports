<?php
/**
 * 通用类
 *
 * @author   姚飞<nineteen.yao@qq.com>
 * @date     2024-05-05 17:57:08
 */

namespace Ynineteen\Supports;

use Exception;

class Utils
{
    /**
     * @throws Exception
     */
    public static function throwIf($condition, $errors, $errCode = -1)
    {
        if (!$condition) {
            return;
        }

        if (is_string($errors)) {
            $errors = [$errors];
        }

        throw new Exception(implode(';', $errors), $errCode);
    }

    public static function makeTestErr($msg = 'Test Err',$code = -1)
    {
        throw new \Exception($msg, $code);
    }
}