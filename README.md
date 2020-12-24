# document

```php

    DTime::offset(-1);					       //默认值：0 将当前时间往前移到上一天，方便模拟时间测试

	//模拟时间

	DTime::time();						//获取模拟的当前时间戳
	DTime::beginTime();					//获取模拟的当日零晨时间戳
	DTime::getOffsetTime('+1 day');		                //获取模拟的指定偏移时间戳
	DTime::now();						//获取模拟的当前时间 如：2018-10-19 10:49:25
	DTime::today();						//获取模拟的当日日期	2020-11-11
	DTime::today('/');					//获取模拟的当日日期，使用指定日期分隔符	2020/11/11
	DTime::tomorrow();					//获取模拟明天日期	2020-11-12
	DTime::yestoday();					//获取模拟昨日日期	2020-11-10
	DTime::getOffsetDate('+1 day');		                //获取模拟的指定偏移日期	2020-11-11

	//指定时间转换，一下方法，参数既可以是datetime，也可以是timestamp

	DTime::getTimestamp('2020-11-10');		//获取一个时间的时间戳
	DTime::firstTimeOfMonth('2020-11-10');	        //获取指定时间，第一天的零晨的时间戳 --> 1604937600
	DTime::day('2020-11-11 12:12:12');		//根据时间，或者时间戳获取一个日期--> 20201111
	DTime::day(1607493682);				//时间戳获取一个日期  --> 2020-12-01
	DTime::month('2020-11-11 12:12:12');	        //时间获取月份 --> 2020-12
	DTime::month(1607493682);			//时间戳获取月份 --> 2020-12
	DTime::firstDayOfMonth('2020-11-10');	 	//月份第一天 --> 2020-11-01
    	DTime::isFirstDayOfMonth('2020-11-10');		//是否月份的第一天	false
    	DTime::lastDayOfMonth('2020-11-10');		//月份最后一天 --> 2020-11-30
    	DTime::isLastDayOfMonth('2020-11-10');		//是否月份的最后一天 false
	DTime::maxDayOfMonth('2020-11-10');		//计算指定日期当月月份的天数总数 --> 30

    	DTime::dateCompare('2020-11-10','2020-11-12');	//比较时间大小 1 date1 > date2,0 date1 = date2 , -1 date1 < date2
    	DTime::diff('2020-11-10','2020-12-13');	 	//计算两个时间的距离 

    DTime::nextDay($datetime=null, $division='-');          //下一天
    DTime::nextDayTime($datetime=null);                     //下一天时间戳
    
    DTime::prevDay($datetime=null, $division='-');          //上一天
    DTime::prevDayTime($datetime=null);                     //上一天时间戳

    DTime::firstDayOfNextMonth($datetime, $division='-');   //下个月的第一天
    DTime::firstTimeOfNextMonth($datetime);                 //下个月的第一天最小时间戳
    DTime::lastDayOfNextMonth($datetime, $division='-');    //下个月的最后一天
    DTime::lastTimeOfNextMonth($datetime);                  //下个月的最后一天的最后时间戳
    
    DTime::firstDayOfPrevMonth($datetime, $division='-');    //上个月的第一天
    DTime::firstTimeOfPrevMonth($datetime);                 //上个月的第一天最小时间戳
    DTime::lastDayOfPrevMonth($datetime, $division='-');    //上个月的最后一天
    DTime::lastTimeOfPrevMonth($datetime);                  //上个月的最后一天的最后时间戳


```

