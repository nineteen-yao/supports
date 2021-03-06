<?php
/**
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2020/12/25 9:40
 */


namespace Ynineteen\Supports;


class Csv
{
    /**
     * 将数据导出CSV
     * @param $rows
     * @param $fileName
     * @return false
     */
    public static function export($rows, $fileName)
    {
        if (empty($fileName) || empty($rows)) return false;

        $fileName = str_replace('.csv', '', strtolower($fileName)) . '.csv';
        header("Content-type:application/vnd.ms-excel; charset=gb18030");
        header("Content-Disposition:filename=" . iconv("UTF-8", "GB18030", $fileName));
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        foreach ($rows as $row) {
            //每个单元格都转为中文编码输出
            foreach ($row as &$cell) {
                $cell = iconv('utf-8', 'GB18030', $cell);
            }
            //一行一行的输出
            fputcsv($fp, $row);
        }

        //输出结束，关闭指针
        fclose($fp);
        ob_flush();
        flush();
    }

    /**
     * 大数据导出，仅支持在laravel框架内使用
     * @param $query
     * @param $file
     * @param array $head
     */
    public static function bigDataExport($query, $file, $head = [])
    {
        $fp = fopen($file, 'a');

        if ($head) {
            foreach ($head as &$val) {
                $val = iconv('utf-8', 'GB18030', $val);
            }
            fputcsv($fp, $head);
        }

        foreach ($query->cursor() as $customer) {
            $row = $customer->toArray();
            //每个单元格都转为中文编码输出
            foreach ($row as &$cell) {
                $cell = iconv('utf-8', 'GB18030', $cell);
            }
            //一行一行的输出
            fputcsv($fp, $row);
        }
        //输出结束，关闭指针
        fclose($fp);
    }

    /**
     * 消除单元格中的换行符号
     * @param string $file
     * @return mixed
     */
    public static function convert($file)
    {
        $content = file_get_contents($file);
        $content = mb_convert_encoding(trim(strip_tags($content)), 'utf-8', 'gbk');
        $content = str_replace('""', "'", $content);

        $r = preg_match_all('/"([^"]+)"/', $content, $M);
        if ($r > 0) {
            foreach ($M[1] as $value) {
                $replace = trim(str_replace(["\n", "\r"], ['', ''], $value));
                $content = str_replace($value, $replace, $content);
            }
        }

        file_put_contents($file . '-copy.csv', $content);

        return $file;
    }

    /**
     * CSV文件解析为二维数组
     * @param $file
     * @param false $containTitle
     * @return array|false
     */
    public static function parse($file, $containTitle = false)
    {
        $file = static::convert($file);
        $rows = file($file);
        if (empty($rows)) {
            return [];
        }

        //去掉表头
        if ($containTitle === false) {
            $rows = array_splice($rows, 1);
        }

        if (empty($rows)) {
            return [];
        }

        foreach ($rows as &$row) {

            //将CSV每行字符串转为数组
            //$row = mb_convert_encoding(trim(strip_tags($row)), 'utf-8', 'gbk');
            $row = static::encodingCast($row);
            $row = str_replace('"', '', $row);

            //处理每行数组的值，处理前后空格问题
            $row = array_map(function ($value) {
                return trim($value);
            }, explode(',', $row));
        }

        return $rows;
    }

    /**
     * 字符编码转化
     * @param $str
     * @return array|false|string|string[]|null
     */
    public static function encodingCast($str)
    {
        $encoding = mb_detect_encoding($str, ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"]);
        $str = trim(strip_tags($str));
        if ($encoding !== 'UTF-8') {
            return mb_convert_encoding($str, 'UTF-8', $encoding);
        }

        return $str;
    }
}