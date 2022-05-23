<?php

namespace Ynineteen\Supports;

class YStr
{
    /**
     * 字符串分割
     * @param string $str
     * @param string $seperator
     * @param int $limit 最大切割数据
     * @param bool $isIgnoreEmpty 是否删除空字符段
     * @return array
     */
    public static function split(string $str, string $seperator, int $limit = -1, bool $isIgnoreEmpty = false): array
    {
        $limit = ($limit < 1) ? -1 : $limit;
        $arr = explode($seperator, $str, $limit);
        if (!$isIgnoreEmpty) {
            return $arr;
        }

        $res = [];
        foreach ($arr as $value) {
            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $res[] = $value;
        }

        return $res;
    }

    /**
     * 字符串重组
     * @param string $str
     * @param string $seperator
     * @param int $limit
     * @param bool $isIgnoreEmpty
     * @return string
     */
    public static function reJoin(string $str, string $seperator, int $limit = -1, bool $isIgnoreEmpty = false): string
    {
        return implode($seperator, static::split($str, $seperator, $limit, $isIgnoreEmpty));
    }
}