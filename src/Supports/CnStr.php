<?php
/**
 * 含有汉字的字符串处理
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2021-01-12 12:09
 */


namespace Ynineteen\Supports;


class CnStr
{
    /**
     * 字符串补齐
     * @param string $input 输入的字符
     * @param int $length 长度
     * @param int $padPosition 补全位置 0 左边 1 右边 2 两边
     * @param string $padChar 仅仅支持一个字符，多出来自动截取第一个
     * @return string
     */
    public static function pad($input, $length, $padPosition = STR_PAD_RIGHT, $padChar = ' ')
    {
        $chineseCount = static::chineseCount($input);
        $width = mb_strlen($input) + $chineseCount * 1.53 - $chineseCount;
        $padnum = max(0, $length - $width);

        //处理补齐的字符
        $padCharLength = mb_strlen($padChar);
        if ($padCharLength > 0) {
            $padChar = mb_substr($padChar, 0, 1);
        }

        //补齐字符为中文
        if (static::hasChinese($padChar)) {
            $padnum = ceil($padnum / 1.53);
        }

        $padStr = '';
        for ($i = 0; $i < $padnum; $i++) {
            $padStr .= $padChar;
        }

        if ($padPosition === STR_PAD_RIGHT) {
            return $input . $padStr;
        }

        if ($padPosition === STR_PAD_LEFT) {
            return $padStr . $input;
        }

        $halfNum = ceil($padnum / 2);
        $padStr = substr($padStr, 0, $halfNum);

        return $padStr . $input . $padStr;
    }

    /**
     * 统计汉字总数
     * @param $str
     * @return int
     */
    public static function chineseCount($str)
    {
        $num = 0;
        foreach (mb_str_split($str) as $word) {
            if (ord($word) > 0xa0) {
                $num++;
            }
        }

        return $num;
    }

    /**
     * 判断是否含有中文
     * @param string $str
     * @return bool
     */
    public static function hasChinese($str)
    {
        return static::chineseCount($str) > 0;
    }
}