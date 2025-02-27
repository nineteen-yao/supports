<?php
/**
 * 时间助手
 * @author: YaoFei<nineteen.yao@qq.com>
 * Datetime: 2020/12/8 16:27
 */


namespace Ynineteen\Supports;

use Exception;

class DTime
{
    const DAY_SECONDS = 86400;
    const HOUR_SECONDS = 3600;
    const MINUTE_SECONDS = 60;

    /**
     * @var int $exceCurrentTimestamp 当前执行的时间戳，默认time()
     */
    static private $exceCurrentTimestamp;

    /**
     * @var string $dateFormat 当前执行的日期 格式 0000-00-00
     */
    static private $dateFormat = 'Y-m-d';

    /**
     * @var string $dateDivision 日期分隔符
     */
    static private $dateDivision = '-';

    static private $timeFormat = 'H:i:s';

    static private $timeDivision = ':';

    /**
     * @var int|string $offset 时间偏移，可以为数字(+1,-1等)，字符串(+1 month ,-1 month等)
     */
    static private $offset = 0;

    /**
     * 初始化当前的执行时间
     */
    public static function init()
    {
        self::$exceCurrentTimestamp = time();

        self::$offset = 0;

        self::$dateFormat = str_replace('-', self::$dateDivision, self::$dateFormat);

        self::$timeFormat = str_replace('-', self::$timeDivision, self::$timeFormat);
    }

    /**
     * 返回日期使用的格式
     *
     * @param string $division
     *
     * @return mixed 默认格式：0000-00-00
     */
    public static function dateFormat($division = '-')
    {
        return str_replace('-', $division, self::$dateFormat);
    }

    /**
     * 返回时间使用的格式
     *
     * @param string $division
     *
     * @return mixed 默认格式：00:00:00
     */
    public static function timeFormat($division = ':')
    {
        return str_replace('-', $division, self::$timeFormat);
    }

    /**
     * 返回日期+时间使用的格式
     *
     * @param string $dateDivision
     * @param string $timeDivision
     *
     * @return string 默认格式：0000-00-00 00:00:00
     */
    public static function dateTimeFormat($dateDivision = '-', $timeDivision = ':')
    {
        return self::dateFormat($dateDivision) . ' ' . self::timeFormat($timeDivision);
    }

    /**
     * 将当前时间进行以天为单位的偏移,或者跳到指定日期内
     *
     * 使用DEMO：
     * offset(1);
     * offset(+1);
     * offset(-1);
     * offset('+1 month');
     * offset('2020-09-11');
     *
     * @param int $offset 时间偏移 单位 天
     */
    public static function offset($offset = 0)
    {
        self::init();

        if (empty($offset) || !$offset || $offset === 0) {
            return;
        }

        //当偏移值为一个整数，那么将时间戳偏移到指定日期的
        if (is_int($offset)) {
            self::$offset = $offset . ' day';
            self::$exceCurrentTimestamp = strtotime(self::$offset, self::$exceCurrentTimestamp);
            return;
        }

        $offset = trim($offset);
        $strlen = strlen($offset);
        if ($strlen < 5) {
            self::$offset = $offset . ' day';
            self::$exceCurrentTimestamp = strtotime(self::$offset, self::$exceCurrentTimestamp);
            return;
        }

        //offset写成了+1 day +1 month 2020-09-11等格式，直接使用
        self::$offset = $offset;
        self::$exceCurrentTimestamp = strtotime(self::$offset);

    }

    /**
     * 获取当前执行的时间戳
     * @return int
     */
    public static function time()
    {
        return !self::$offset ? time() : strtotime(self::$offset);
    }

    /**
     * 当前的时间，格式 2018-10-19 10:49:25
     * @return string
     */
    public static function now()
    {
        return date(self::dateTimeFormat(), self::time());
    }

    /**
     * 获取一个时间戳
     *
     * @param null $datetime
     *
     * @return false|int|null
     */
    public static function getTimestamp($datetime = null)
    {
        if (!$datetime) {
            return self::time();
        }

        //时间戳格式
        if (preg_match('/^[1-9]\d{8,}$/', $datetime)) {
            return $datetime;
        }

        return strtotime($datetime);
    }

    /**
     * 当天的零晨时间戳
     * @return int
     */
    public static function beginTime()
    {
        $today = self::today('-');

        return strtotime($today);
    }

    /**
     * 每个月第一天的凌晨时间戳
     *
     * @param $time
     *
     * @return false|int
     */
    public static function firstTimeOfMonth($time)
    {
        $date = self::firstDayOfMonth($time, '-');

        return strtotime($date);
    }

    /**
     * 获取一个偏移的时间戳，比如，当天为2020-10-10日，执行getOffsetTime('+1 day')后，得到 154....的值，为2020-10-11号的时间戳
     *
     * @param string $fomartString
     *
     * @return false|int
     */
    public static function getOffsetTime($fomartString = '+1 day')
    {
        $time = self::time();

        return strtotime($fomartString, $time);
    }

    /**
     * 获取一个偏移的日期 比如，当天为2020-10-10日，执行getOffsetTime('+1 day')后，得到2020-10-11日
     *
     * @param string $fomartString
     * @param string $division
     *
     * @return bool|string
     */
    public static function getOffsetDate($fomartString = '+1 day', $division = '-')
    {

        $offsetTime = self::getOffsetTime($fomartString);

        return self::day($offsetTime, $division);
    }

    /**
     * 把一个时间日期，截取日期部分 比如：2020-11-11 12:12:12 --> 20201111，1564471319 --> 2020-05-04
     *
     * @param null $datetime
     * @param string $division
     *
     * @return bool|string
     */
    public static function day($datetime = null, $division = '-')
    {
        if (!$datetime) {
            return self::today();
        }

        //时间戳格式
        if (preg_match('/^[1-9]\d{8,}$/', $datetime)) {
            $datetime = date(self::dateTimeFormat($division), $datetime);
        } else {
            //其它格式
            $datetime = str_replace('-', $division, $datetime);
        }

        $datetimeArr = explode(' ', $datetime);

        return $datetimeArr[0];
    }

    /**
     * 获取下一天 2020-11-11 --> 2020-11-12
     *
     * @param null $datetime
     * @param string $division
     *
     * @return bool|string
     */
    public static function nextDay($datetime = null, $division = '-')
    {
        $time = static::getTimestamp($datetime);
        $nextDateTime = strtotime('+1 day', $time);

        return static::day($nextDateTime, $division);
    }

    /**
     * 下一天的时间戳 2020-11-11 --> 2020-11-12时间戳1605110400
     *
     * @param null $datetime
     *
     * @return false|int
     */
    public static function nextDayTime($datetime = null)
    {
        $nextDay = static::nextDay($datetime);
        return strtotime($nextDay);
    }

    /**
     * 获取上一天 2020-11-11 --> 2020-11-10
     *
     * @param null $datetime
     * @param string $division
     *
     * @return bool|string
     */
    public static function prevDay($datetime = null, $division = '-')
    {
        $time = static::getTimestamp($datetime);
        $nextDateTime = strtotime('- day', $time);

        return static::day($nextDateTime, $division);
    }

    /**
     * 上一天的时间戳 2020-11-11 --> 2020-11-10时间戳1605110400
     *
     * @param null $datetime
     *
     * @return false|int
     */
    public static function prevDayTime($datetime = null)
    {
        $nextDay = static::prevDay($datetime);
        return strtotime($nextDay);
    }

    /**
     * 今天的日期
     *
     * @param mixed $division 分隔符
     *
     * @return string demo--> 2020-11-11
     */
    public static function today($division = '-')
    {
        return date(self::dateFormat($division), self::time());
    }

    /**
     * @param string $division
     *
     * @return bool|string demo --> 2020-11-12
     */
    public static function tomorrow($division = '-')
    {
        return self::getOffsetDate('+1 day', $division);
    }

    /**
     * @param string $division
     *
     * @return bool|string demo --> 2020-11-10
     */
    public static function yestoday($division = '-')
    {
        return self::getOffsetDate('-1 day', $division);
    }

    /**
     * 获取指定时间的当月的第一天 如：1565148206 --> 2020-05-01，2020-12-12 --> 2020-12-01
     *
     * @param string $division
     * @param null $time
     *
     * @return false|string
     */
    public static function firstDayOfMonth($time = null, $division = '-')
    {
        $date = static::day($time, '-');
        $time = strtotime($date);

        return date('Y' . $division . 'm' . $division . '01', $time);
    }

    /**
     * 判断一个时间是否一个月的第一天
     *
     * @param string|int|null $datetime
     *
     * @return bool
     */
    public static function isFirstDayOfMonth($datetime = null)
    {
        $date = static::day($datetime, '-');
        $time = strtotime($date);

        return date('d', $time) === '01';
    }

    /**
     * 获取指定时间的当月的最后一天 如：1565148206 --> 2020-05-31，2020-12-12 --> 2020-12-31
     *
     * @param string $division
     * @param null $time
     *
     * @return false|string
     */
    public static function lastDayOfMonth($time = null, $division = '-')
    {
        $timestamp = static::getTimestamp($time);
        $totalDaysOfMonth = static::maxDayOfMonth($timestamp);

        return date('Y' . $division . 'm' . $division . $totalDaysOfMonth, $timestamp);
    }

    /**
     * 获取指定时间的当月的最后一天最后一秒的时间戳 如：2020-12-12 --> 2020-12-31当天的时间戳1565148206
     *
     * @param $datetime
     *
     * @return false|int
     */
    public static function lastTimeOfMonth($datetime)
    {
        $lastDay = static::lastDayOfMonth($datetime);

        return strtotime($lastDay) + static::DAY_SECONDS - 1;
    }

    /**
     * 判断一个时间是否一个月的第一天
     *
     * @param string|int|null $datetime
     *
     * @return bool
     */
    public static function isLastDayOfMonth($datetime = null)
    {
        $timestamp = static::getTimestamp($datetime);
        $totalDaysOfMonth = static::maxDayOfMonth($timestamp);

        $date = getdate($timestamp);

        return $totalDaysOfMonth === $date['mday'];
    }

    /**
     * 根据时间，获取下一个月的第一天的日期 2020-10-19 ---> 2020-11-01
     *
     * @param        $datetime
     * @param string $division
     *
     * @return bool|string
     */
    public static function firstDayOfNextMonth($datetime, $division = '-')
    {
        $timestamp = static::lastTimeOfMonth($datetime) + 1;

        return static::day($timestamp, $division);
    }

    /**
     * 根据时间，获取下一个月的第一天的日期的时间戳  2020-10-19 ---> 2020-11-01当天0点整时间戳
     *
     * @param $datetime
     *
     * @return false|int
     */
    public static function firstTimeOfNextMonth($datetime)
    {
        return static::lastTimeOfMonth($datetime) + 1;
    }

    /**
     * 根据时间，获取下一个月的最后一天的日期  2020-10-19 ---> 2020-11-30
     *
     * @param        $datetime
     * @param string $division
     *
     * @return false|string
     */
    public static function lastDayOfNextMonth($datetime, $division = '-')
    {
        $firstDay = static::firstDayOfNextMonth($datetime);
        return static::lastDayOfMonth($firstDay, $division);
    }

    /**
     * 根据时间，获取下一个月的最后一天的日期的时间戳
     *
     * @param $datetime
     *
     * @return false|int
     */
    public static function lastTimeOfNextMonth($datetime)
    {
        $lastDay = static::lastDayOfNextMonth($datetime);

        return strtotime($lastDay) + self::DAY_SECONDS - 1;
    }


    /**
     * 根据时间，获取上一个月的第一天的日期 2020-10-19 ---> 2020-09-01
     *
     * @param        $datetime
     * @param string $division
     *
     * @return bool|string
     */
    public static function firstDayOfPrevMonth($datetime, $division = '-')
    {
        $timestamp = static::firstTimeOfMonth($datetime) - 1;

        return static::firstDayOfMonth($timestamp, $division);
    }

    /**
     * 根据时间，获取上一个月的第一天的日期的时间戳  2020-10-19 ---> 2020-09-01当天0点整时间戳
     *
     * @param $datetime
     *
     * @return false|int
     */
    public static function firstTimeOfPrevMonth($datetime)
    {
        return strtotime(static::firstDayOfPrevMonth($datetime));
    }

    /**
     * 根据时间，获取上一个月的最后一天的日期  2020-10-19 ---> 2020-11-30
     *
     * @param        $datetime
     * @param string $division
     *
     * @return false|string
     */
    public static function lastDayOfPrevMonth($datetime, $division = '-')
    {
        $firstDay = static::firstDayOfPrevMonth($datetime);
        return static::lastDayOfMonth($firstDay, $division);
    }

    /**
     * 根据时间，获取上一个月的最后一天的日期的时间戳
     *
     * @param $datetime
     *
     * @return false|int
     */
    public static function lastTimeOfPrevMonth($datetime)
    {
        $lastDay = static::lastDayOfPrevMonth($datetime);

        return strtotime($lastDay) + self::DAY_SECONDS - 1;
    }


    /**
     * 获取月份，---> 2020-01
     *
     * @param int|null|string $time
     * @param string $division
     *
     * @return false|string
     */
    public static function month($time = null, $division = '-')
    {
        $tomonth = static::firstDayOfMonth($time, $division);

        return substr($tomonth, 0, 6 + strlen($division));
    }

    /**
     * 两个日期比较大小
     * 结果为1 date1 > date2
     * 结果为0 date1 = date2
     * 结果为-1 date1 < date2
     *
     * @param $date1
     * @param $date2
     *
     * @return int
     */
    public static function dateCompare($date1, $date2)
    {
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);

        if ($time1 > $time2) {
            return 1;
        }

        if ($time1 === $time2) {
            return 0;
        }

        return -1;
    }

    /**
     * 查询两个时间之间距离
     * 值 > 0 那么compare < base 表示base在compare后面 如： diff('2020-11-11','2020-11-12') --> 1
     * 值 < 0 那么compare > base 表示compare还没到，base到compare还欠时间 如： diff('2020-11-13','2020-11-12') --> -1
     *
     * @param string $compare 查询的时间
     * @param null $base 参考的时间
     * @param string $unit 距离单位 day(默认)天，second秒，week周，month月，year年
     *
     * @return float|int
     * @throws Exception
     */
    public static function diff(string $compare, $base = null, $unit = 'day')
    {
        $base = ($base ? $base : static::today('-'));

        $baseTimestamp = strtotime($base);
        $compareTimestamp = strtotime($compare);

        if ($baseTimestamp === $compareTimestamp) {
            return 0;
        }

        $direction = ($baseTimestamp > $compareTimestamp ? 1 : -1);

        $baseTime = new \DateTime($base);
        $compareTime = new \DateTime($compare);

        $diff = $baseTime->diff($compareTime);

        $days = $diff->days;

        $secondsArray = [];
        $step = 1;
        foreach (['s', 'i', 'h', 'days'] as $u) {
            $secondsArray[] = $diff->{$u} * $step;

            $step *= 60;
        }

        switch ($unit) {
            //以秒为单位
            case 'second':
                $ret = array_sum($secondsArray);
                break;

            //以周为单位
            case 'week':
                $ret = intval($days / 7);
                break;

            case 'month':
                $ret = $diff->y * 12 + $diff->m;
                break;

            case 'year':
                $ret = $diff->y;
                break;

            //默认以天作为比较
            default:
                array_pop($secondsArray);
                if (array_sum($secondsArray) > 0) {
                    $days++;
                }
                $ret = $days;
                break;
        }

        return $ret * $direction;
    }

    /**
     * 判定一个时间，是否在指定时间范围内，比如是否属于夜间
     * 示例：查询一个时间是否处于 夜里22:00到第二天早上6:00  inRangeTime('2020-11-11 00:01','22:00-06:00');
     *
     * @param int|string $compare 指定的时间 支持时间戳和格式化时间
     * @param string $rangeStr 时间范围 格式： hh:mm - hh:mm
     * @param string $rangeSplit 时间范围分隔符
     *
     * @return bool
     */
    public static function inRangeTime($compare, $rangeStr, $rangeSplit = '-')
    {
        list($start, $end) = explode($rangeSplit, $rangeStr);
        $start = trim($start);
        $end = trim($end);

        $ranges = [
            [
                'start' => static::getTimestamp(static::yestoday() . ' ' . $start),
                'end' => static::getTimestamp(static::today() . ' ' . $end)
            ],
            [
                'start' => static::getTimestamp(static::today() . ' ' . $start),
                'end' => static::getTimestamp(static::nextDay() . ' ' . $end)
            ],
        ];

        $compare = static::getTimestamp($compare);
        foreach ($ranges as $range) {
            if ($compare > $range['start'] && $compare < $range['end']) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取一个时间范围
     * @param string $date 指定日期
     * @param int $dimension 统计维度 1-日期 2-月度 3-年度
     * @param bool $withTime 是否带回时间范围
     * @param bool $endPlusOneDay 截止日期往后+一天
     * @return array
     * @throws Exception
     */
    public static function dateRange($date, $dimension = 1, $withTime = false, $endPlusOneDay = false)
    {
        $date = self::day($date);
        if (!in_array($dimension, [1, 2, 3])) {
            throw new Exception('Dimension only support 1,2,or3,"' . $dimension . '" was given.');
        }
        $start = $date;
        $end = $date;

        //月度
        if ($dimension === 2) {
            $start = self::firstDayOfMonth($date);
            $end = self::lastDayOfMonth($date);
        }
        //年度
        if ($dimension === 3) {
            $year = date('Y', self::getTimestamp($date));
            $start = $year . '-01-01';
            $end = $year . '-12-31';
        }

        if ($withTime) {
            $start .= ' 00:00:00';
            $end .= ' 23:59:59';
        } else {
            if ($endPlusOneDay) {
                $end = self::nextDay($end);
            }
        }

        return [$start, $end];
    }

    public static function dateRangePlus($date, $dimension = 1)
    {
        return self::dateRange($date,$dimension,false,true);
    }

    public static function timeRange($date, $dimension = 1)
    {
        return self::dateRange($date,$dimension,true);
    }

    /**
     * 获取一个日期当月月份的天数
     *
     * @param null $datetime
     *
     * @return int
     */
    public static function maxDayOfMonth($datetime = null)
    {
        $timestamp = static::getTimestamp($datetime);

        $date = getdate($timestamp);

        $map = [
            1 => 31,
            2 => ($date['year'] % 4 === 0 ? 29 : 28),
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31
        ];

        return $map[$date['mon']];
    }
}
