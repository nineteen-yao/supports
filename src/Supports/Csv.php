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
     * CSV文件解析为二维数组
     * @param $file
     * @param false $containTitle
     * @return array|false
     */
    public static function parse($file, $containTitle = false)
    {
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
            $row = mb_convert_encoding(trim(strip_tags($row)), 'utf-8', 'gbk');
            $row = str_replace('"', '', $row);

            //处理每行数组的值，处理前后空格问题
            $row = array_map(function ($value) {
                return trim($value);
            }, explode(',', $row));
        }

        return $rows;
    }
}