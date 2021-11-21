/**
 * 10 進位數字轉換為 62 進制
 *
 * @param  {number} number 待轉換為 62 進制的 10 進位數字
 * @return {string}        轉換完畢的 62 進位數字
 */
function base10to62(number)
{
    let chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.split(''),
        radix = chars.length,
        dividend = number,
        arr = [];

    do
    {
        mod = dividend % radix;
        dividend = (dividend - mod) / radix;
        arr.unshift(chars[mod]);
    }
    while (dividend);

    return arr.join('');
}


/**
 * 62 進位數字轉換為 10 進制
 *
 * @param  {string|number} number 待轉換為 10 進制的 62 進位數字
 * @return {number}               轉換完畢的 10 進位數字
 */
function base62to10(number)
{
    let chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
        radix = chars.length,
        numStr = String(number),
        len = numStr.length,
        i = 0,
        originNumber = 0;

    while (i < len)
    {
        originNumber += Math.pow(radix, i++) * chars.indexOf(numStr.charAt(len - i) || 0);
    }

    return originNumber;
}


/**
 * 指定元素，壓縮內部 HTML 碼
 *
 * @param  {string} element 待壓縮的元素
 * @return {string}         壓縮後的 HTML 碼
 */
function compressHTML(element)
{
    let elHtml = document.querySelector(element).innerHTML;

    elHtml = elHtml.replace(/> /g, '>&nbsp;');
    elHtml = elHtml.replace(/ \n/g, '&nbsp;');
    elHtml = elHtml.replace(/&nbsp; +/g, '&nbsp;');
    elHtml = elHtml.replace(/^\s+/g, '');
    elHtml = elHtml.replace(/>\s+</g, '><');
    elHtml = elHtml.replace(/>\s+([^\s<])/g, '>$1');
    elHtml = elHtml.replace(/([^\s>])\n\s*/g, '$1');
    elHtml = elHtml.replace(/\s$/g, '');
    elHtml = elHtml.replace(/\n\s+$/g, '');

    document.querySelector(element).innerHTML = elHtml;

    return elHtml;
}


/**
 * 依據年、月、日格式計算日數，月、日部分使用一般曆法風格
 *
 * @param  {number} d 日數
 * @return {string}   符合年、月、日格式的日期字串
 */
function countDateCalendarManner(d)
{
    /* 強制轉換輸入日數為整數，並令日期從 0 開始計數，以利後續換算 */
    let day = parseInt(d - 1);

    /* 日數為負時返回空值 */
    if (day < 0)
    {
        return null;
    }

    /* 宣告大、中、小 3 種週期的日數 */
    let DayInGrandCycle = day % 146097;                 // 大週期：400 年 97 閏，以 146,097 日為一循環
    let DayInMiddleCycle;
    let DayInNormalCycle;

    /* 中週期日數規則 */
    // 前 99 年正常 4 年 1 閏
    if (DayInGrandCycle < 36525)
    {
        DayInMiddleCycle = DayInGrandCycle % 1461;      // 中週期：4 年 1 閏，以 1,461 日為一循環
    }
    // 第 100 年不閏，故從第 101 年起計算中週期日數時，令大週期日數前推 1 天
    else if (DayInGrandCycle < 73049)
    {
        DayInMiddleCycle = (DayInGrandCycle + 1) % 1461;
    }
    // 第 200 年不閏，故從第 201 年起計算中週期日數時，令大週期日數再往前推 1 天（累計 2 天）
    else if (DayInGrandCycle < 109573)
    {
        DayInMiddleCycle = (DayInGrandCycle + 2) % 1461;
    }
    // 第 300 年不閏，故從第 301 年起計算中週期日數時，令大週期日數再往前推 1 天（累計 3 天）
    else
    {
        DayInMiddleCycle = (DayInGrandCycle + 3) % 1461;
    }

    /* 小週期日數規則 */
    // 中週期日數 = 1460，即 4 年週期的最後一天
    if (DayInMiddleCycle == 1460)
    {
        // 大週期日數等於第 101、201、301 年的第一天時，令小週期日數歸零（跳到一年的第一天）
        if (DayInGrandCycle == 36524 || DayInGrandCycle == 73048 || DayInGrandCycle == 109572)
        {
            DayInNormalCycle = 0;
        }
        // 否則將小週期日數特別標定為 365（從 0 開始的第 366 天），代表其為閏年的最後一天
        else
        {
            DayInNormalCycle = 365;
        }
    }
    // 中週期日數不等於 1460 時，小週期日數等於中週期日數對 365 取模
    else
    {
        DayInNormalCycle = DayInMiddleCycle % 365;
    }

    /* 簡化小週期變數名稱 */
    let Day = DayInNormalCycle;

    /* 宣告大、中、小 3 種週期的年數 */
    let YearByGrandCycle = Math.floor(day / 146097) * 400;      // 大週期年數 = 日數 ÷ 大週期日數 × 400 年
    let YearByMiddleCycle;
    let YearByNormalCycle;

    /* 中週期年數規則 */
    // 中週期日數 = 1460 且小週期日數 = 0，表示為第 101、201、301 年的第 1 天，
    // 此時令中週期年數 = 大週期日數除以 1461 取整乘以 4 後再加 1
    if (DayInMiddleCycle == 1460 && DayInNormalCycle == 0)
    {
        YearByMiddleCycle = Math.floor(DayInGrandCycle / 1461) * 4 + 1;
    }
    // 中、小週期日數均為 0，且位於大週期的後 300 年，表示為每 4 年中週期的第 1 天，
    // 此時令中週期年數 = 大週期日數除以 1461 取整加 1 後再乘以 4
    // （前 100 年沒有「世紀年不能被 400 整除則不閏」的狀況，故每 4 年第 1 天不須如此處理）
    else if (DayInMiddleCycle == 0 && DayInNormalCycle == 0 && DayInGrandCycle >= 36525)
    {
        YearByMiddleCycle = (Math.floor(DayInGrandCycle / 1461) + 1) * 4;
    }
    // 其他狀況下，令中週期年數 = 大週期日數除以 1461 取整再乘以 4
    else
    {
        YearByMiddleCycle = Math.floor(DayInGrandCycle / 1461) * 4;
    }

    /* 小週期年數：若中週期日數 = 1460，代表為 4 年 1 閏週期的最後一天，為避免對 1460 取模後年數被加 1，強制令小週期年數 = 3 */
    YearByNormalCycle = (DayInMiddleCycle == 1460) ? 3 : Math.floor(DayInMiddleCycle / 365);

    /* 大、中、小週期年數相加後存為 Year 變數 */
    let Year = YearByGrandCycle + YearByMiddleCycle + YearByNormalCycle;

    /* 日數 log */
    // console.log('Day in grand cycle (400 years) = ' + DayInGrandCycle);
    // console.log('Day in middle cycle (4 years) = ' + DayInMiddleCycle);
    // console.log('Day in normal cycle (1 year) = ' + DayInNormalCycle);
    // console.log('Day = ' + Day);

    /* 年數 log */
    // console.log('Year by grand cycle = ' + YearByGrandCycle);
    // console.log('Year by middle cycle = ' + YearByMiddleCycle);
    // console.log('Year by normal cycle = ' + YearByNormalCycle);
    // console.log('Year = ' + Year);

    /* 宣告年數字串及閏年旗標 */
    let YearStr = '';
    let LeapYear = 0;

    /* 日數超過 365 時賦值給年數字串 */
    if (day >= 365) {
        YearStr = Year + ' 年 ';
    }

    /* 符合 400 年 97 閏規則時，令閏年旗標 = 1 */
    if (Year % 4 == 3 && Year % 400 != 99 && Year % 400 != 199 && Year % 400 != 299) {
        LeapYear = 1;
    }

    /* 律定平年及閏年的各月日數陣列 */
    let DaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if (LeapYear == 1) {
        DaysInMonth[1] = 29;        // 閏年單獨將 2 月的天數改為 29
    }
    /* 將全年各月累計日數儲存為陣列 */
    let AddUpDays = [0];            // 全年各月累計日數陣列，先律定第 0 項 = 0
    let addUp = 0;                  // 各月日數累加變數
    for (let i = 0; i < DaysInMonth.length; i++) {
        addUp += DaysInMonth[i];
        AddUpDays[i + 1] = addUp;
    }
    /* 各月日數及累計日數陣列印出 log */
    // console.log('DaysInMonth = [' + DaysInMonth + ']');
    // console.log('AddUpDays = [' + AddUpDays + ']');

    /* 應用 switch case 的範圍判斷語法，輸出日數相對應的年月日計數 */
    switch (true) {
        case Day < AddUpDays[0]:
        default:
            break;

        case Day < AddUpDays[1]:
            return YearStr + '1 月 ' + (Day + 1) + ' 日';

        case Day < AddUpDays[2]:
            return YearStr + '2 月 ' + (Day - AddUpDays[1] + 1) + ' 日';

        case Day < AddUpDays[3]:
            return YearStr + '3 月 ' + (Day - AddUpDays[2] + 1) + ' 日';

        case Day < AddUpDays[4]:
            return YearStr + '4 月 ' + (Day - AddUpDays[3] + 1) + ' 日';

        case Day < AddUpDays[5]:
            return YearStr + '5 月 ' + (Day - AddUpDays[4] + 1) + ' 日';

        case Day < AddUpDays[6]:
            return YearStr + '6 月 ' + (Day - AddUpDays[5] + 1) + ' 日';

        case Day < AddUpDays[7]:
            return YearStr + '7 月 ' + (Day - AddUpDays[6] + 1) + ' 日';

        case Day < AddUpDays[8]:
            return YearStr + '8 月 ' + (Day - AddUpDays[7] + 1) + ' 日';

        case Day < AddUpDays[9]:
            return YearStr + '9 月 ' + (Day - AddUpDays[8] + 1) + ' 日';

        case Day < AddUpDays[10]:
            return YearStr + '10 月 ' + (Day - AddUpDays[9] + 1) + ' 日';

        case Day < AddUpDays[11]:
            return YearStr + '11 月 ' + (Day - AddUpDays[10] + 1) + ' 日';

        case Day < AddUpDays[12]:
            return YearStr + '12 月 ' + (Day - AddUpDays[11] + 1) + ' 日';
    }
}


/**
 * 將時間戳轉換為 `Y-m-d H:i:s (ms = false)` 或 `Y-m-d H:i:s.u (ms = true)` 格式
 *
 * @param  {number}  time 時間戳
 * @param  {boolean} ms   是否輸出毫秒（預設值 = false）
 * @return {string}       時間字串
 */
function dateFormat(time = new Date(), ms = false)
{
    let date = new Date(time),
        y = date.getFullYear(),
        m = padding(date.getMonth() + 1, '0', 2),
        d = padding(date.getDate(), '0', 2),
        h = padding(date.getHours(), '0', 2),
        i = padding(date.getMinutes(), '0', 2),
        s = padding(date.getSeconds(), '0', 2);

    if (!ms)
    {
        return y + '-' + m + '-' + d + ' ' + h + ':' + i + ':' + s;
    }
    else
    {
        let u = padding(date.getMilliseconds(), '0', 3);
        return y + '-' + m + '-' + d + ' ' + h + ':' + i + ':' + s + '.' + u;
    }
}


/**
 * 將 `Y-m-d H:i:s (ms = false)` 或 `Y-m-d H:i:s.u (ms = true)` 格式的時間轉換為 13 位數（毫秒級）時間戳
 *
 * @param  {string} date 時間字串
 * @return {number}      時間戳
 */
function dateStamp(date = dateFormat())
{
    date = date.replace(/-/g, '/');     // Safari 及 iOS 下各瀏覽器僅支援 `Y/m/d H:i:s` 格式
    if (date.length == 19)
    {
        if (/\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01]) [0-5]\d:[0-5]\d:[0-5]\d/.test(date))
        {
            return new Date(date).getTime();
        }
    }
    else if (date.length > 20 && date.length <= 23)
    {
        if (/\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01]) [0-5]\d:[0-5]\d:[0-5]\d\.\d{1,3}/.test(date))
        {
            return new Date(date).getTime();
        }
    }
    else if (date.length > 23 && date.length <= 26)
    {
        if (/\d{4}\/(0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01]) [0-5]\d:[0-5]\d:[0-5]\d\.\d{1,6}/.test(date))
        {
            let datetime = date.split('.')[0],
                decimal = date.split('.')[1],
                time = String(new Date(datetime).getTime()).slice(0, -3),
                microtime = '';

            if (decimal.length > 3)
            {
                microtime = time + decimal.slice(0, 3) + '.' + decimal.slice(3);
            }
            else
            {
                microtime = time + padding(decimal, '0', 3, 'right');
            }

            return Number(microtime);
        }
    }
}


/**
 * 轉換度分秒格式的度數為小數點格式
 *
 * @param  {number} degree 度
 * @param  {number} minute 分
 * @param  {number} second 秒
 * @return {number}        小數點格式度數
 */
function degFull(degree, minute, second)
{
    try
    {
        if (second >= 60 || second < 0)
        {
            throw "Second value must be between 0 and 59.999...";
        }
        else if (minute >= 60 || minute < 0)
        {
            throw "Minute value must be between 0 and 59.999...";
        }

        let p = (degree >= 0) ? 1 : -1,
            sim = parseFloat((second / 60).toFixed(12)),
            mid = parseFloat(((sim + minute) / 60).toFixed(12)),
            d = parseFloat((mid + Math.abs(degree)).toFixed(12));

        return d * p;
    }
    catch (err)
    {
        console.error(err);
    }
}


/**
 * 轉換小數點格式的度數為度分秒格式
 *
 * @param  {number}       degree 小數點格式度數
 * @return {degreeObject}        度、分、秒值物件
 */
function degMinSec(degree)
{
    let p  = (degree >= 0) ? 1 : -1,
        df = Math.abs(degree),
        d  = Math.floor(df),
        mf = parseFloat(((df - d) * 60).toFixed(10)),
        m  = Math.floor(mf),
        sf = parseFloat(((mf - m) * 60).toFixed(10));

    return {
        degree: d * p,
        minute: m,
        second: sf
    };
}


/**
 * 將表單 datetime 項的值轉成 `Y-m-d H:i:s (milli = false)` 或 `Y-m-d H:i:s.u (milli = true)` 格式
 *
 * @param  {string}  date  表單的日期時間值
 * @param  {number}  s     指定秒
 * @param  {number}  ms    指定毫秒
 * @param  {boolean} milli 是否輸出毫秒
 * @return {string}        符合 `Y-m-d H:i:s(.u)` 格式的日期時間字串
 */
function formDateFormat(date, s = 0, ms = 0, milli = false)
{
    const ymdhis = /^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01]) ([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
          ymdthi = /^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d)$/,
          ymdthis = /^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d):([0-5]\d)$/,
          ymdthisu = /^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d):([0-5]\d)\.\d{3}$/;

    let snum = parseInt(s),
        msnum = parseInt(ms),
        second, millisecond;
    
    // 指定秒數
    switch (true) {
        case (snum >= 60):
            second = '59';
            break;
        
        case (snum >= 0):
            second = padding(snum, '0', 2);
            break;

        default:
            second = '00';
            break;
    }

    // 指定毫秒數
    switch (true) {
        case (msnum >= 1000):
            millisecond = '999';
            break;

        case (msnum >= 0):
            millisecond = padding(msnum, '0', 3);
            break;

        default:
            millisecond = '000';
            break;
    }

    // 輸出
    switch (milli)
    {
        case true:
            switch (true) {
                case (ymdhis.test(date)):
                    return date + '.' + millisecond;

                case (ymdthi.test(date)):
                    return date.replace('T', ' ') + ':' + second + '.' + millisecond;
                
                case (ymdthis.test(date)):
                    return date.replace('T', ' ') + '.' + millisecond;
                
                case (ymdthisu.test(date)):
                    return date.replace('T', ' ');

                default:
                    return null;
            }
            break;

        default:
            switch (true) {
                case (ymdhis.test(date)):
                    return date;

                case (ymdthi.test(date)):
                    return date.replace('T', ' ') + ':' + second;
                
                case (ymdthis.test(date)):
                    return date.replace('T', ' ');
                
                case (ymdthisu.test(date)):
                    return date.replace('T', ' ').replace(/\.\d{3}/, '');

                default:
                    return null;
            }
            break;
    }
}


/**
 * 取得 URL 中的 GET 參數
 *
 * @param  {string}                   param 參數名稱
 * @return {string|{key:string}|null}       參數值字串、物件或空值
 */
function getParameter(param)
{
    let search = location.search.substr(1),
        paramString = search.split('&'),
        paramParse, key, val,
        paramObj = {};

    // 未指定特定參數時，返回所有參數（物件）
    if (param === undefined)
    {
        // GET 參數字串長度大於 1（問號本身占 1 字元）才進行解析
        if (search.length > 1)
        {
            for (let i = 0; i < paramString.length; i++)
            {
                paramParse = paramString[i].split('=');
                key = paramParse[0];
                val = paramParse[1];
                paramObj[key] = val;
            }
            return paramObj;
        }
        // GET 參數字串長度小於等於 1（空字串或只有問號）時返回空物件
        else
        {
            return {};
        }
    }
    // 指定具體參數時，返回單一參數的值（字串）
    else
    {
        // GET 參數字串長度大於 1（問號本身占 1 字元）才進行解析
        if (search.length > 1)
        {
            for (let i = 0; i < paramString.length; i++)
            {
                paramParse = paramString[i].split('=');
                key = paramParse[0];
                val = paramParse[1];
                if (key == param)
                {
                    return val;
                }
            }
        }
        // GET 參數字串長度小於等於 1（空字串或只有問號）時返回空值
        else
        {
            return null;
        }
    }
}


/**
 * 取得亞毫秒時間
 *
 * @param  {boolean} micro `true` -> 返回整數微秒時間戳，`false` -> 返回毫秒帶小數時間戳
 * @return {number}        亞毫秒時間
 */
function microTimestamp(micro = false)
{
    let timestamp = performance.timing.navigationStart + performance.now();

    if (micro)
    {
        return timestamp * 1000;
    }
    else
    {
        return timestamp;
    }
}


/**
 * 取得帶微秒值的時間字串（`Y-m-d H:i:s.u` 格式）
 *
 * @return {string} 帶微秒值的時間字串
 */
function microtime()
{
    let timestamp = String(microTimestamp(true)),
        length = timestamp.length,
        microsecond = timestamp.substr(-6);

    timestamp = Number(timestamp.substr(0, length - 3));

    let date = dateFormat(timestamp) + '.' + microsecond;

    return date;
}


/**
 * 解析輸入的毫秒值，依轉換等級拆解為週、日、時、分、秒等單位
 *
 * @param  {number} ms    毫秒值
 * @param  {string} level 轉換等級，分為週、日、時、分、秒及毫秒
 * @return {{}}           包含週、日、時、分、秒及毫秒等單位的數值物件
 */
function msPart(ms, level = null)
{
    /* 各等級時間預設值 */
    let week = 0,
        day = 0,
        hour = 0,
        minute = 0,
        second = 0,
        millisecond = 0;

    /** 轉換等級索引值 */
    let levelIndex = null;

    /** 最高轉換等級 */
    const highestLevel = 'w';   // 週

    /** 合法轉換等級陣列物件 */
    const legalLevel = {
        'ms': [ 'ms', 'milli', 'millis', 'millisec', 'millisecs', 'millisecond', 'milliseconds' ],
        's':  [ 's',                     'sec',      'secs',      'second',      'seconds'      ],
        'm':  [ 'm',                     'min',      'mins',      'minute',      'minutes'      ],
        'h':  [ 'h',                     'hr',       'hrs',       'hour',        'hours'        ],
        'd':  [ 'd',                                              'day',         'days'         ],
        'w':  [ 'w',                                              'week',        'weeks'        ]
    };

    /* 未指定轉換等級時，自動代入最高轉換等級；指定其他轉換等級時，一律先將等級字串轉小寫 */
    level = (level === null) ? highestLevel : level.toLowerCase();

    /* 轉換等級合法時，直接賦予索引值 */
    for (let i = 0; i < Object.keys(legalLevel).length; i++)
    {
        let key = Object.keys(legalLevel)[i];
        if (legalLevel[key].indexOf(level) >= 0)
        {
            levelIndex = i;
            break;
        }
    }

    /* 轉換等級非法時，以最高等級索引值代入 */
    if (levelIndex === null)
    {
        levelIndex = Object.keys(legalLevel).length - 1;
    }

    /*
    |----------------------------------------------------------------
    | 經過以上程序，轉換等級索引被限定在 0 與最高等級的索引值之間
    | 便可確保無論有無指定合法 level，都能正確進行接下來的轉換
    |----------------------------------------------------------------
    */

    /* 指定的毫秒值為正確的數字時，將其賦予毫秒數 */
    if (!isNaN(ms))
    {
        millisecond = ms;
    }

    /* 毫秒數達到進位門檻且轉換等級為秒級以上時，計算秒數及餘下的毫秒數 */
    if (millisecond >= 1000 && levelIndex >= 1)
    {
        second = Math.floor(ms / 1000);
        millisecond = ms - second * 1000;
    }

    /* 秒數達到進位門檻且轉換等級為分鐘級以上時，計算分鐘數及餘下的秒數 */
    if (second >= 60 && levelIndex >= 2)
    {
        minute = Math.floor(second / 60);
        second -= minute * 60;
    }

    /* 分鐘數達到進位門檻且轉換等級為小時級以上時，計算小時數及餘下的分鐘數 */
    if (minute >= 60 && levelIndex >= 3)
    {
        hour = Math.floor(minute / 60);
        minute -= hour * 60;
    }

    /* 小時數達到進位門檻且轉換等級為日（天）級以上時，計算天數及餘下的小時數 */
    if (hour >= 24 && levelIndex >= 4)
    {
        day = Math.floor(hour / 24);
        hour -= day * 24;
    }

    /* 天數達到進位門檻且轉換等級為週（星期）級以上時，計算週數及餘下的天數 */
    if (day >= 7 && levelIndex >= 5)
    {
        week = Math.floor(day / 7);
        day -= week * 7;
    }

    return {
        'week': week,
        'day': day,
        'hour': hour,
        'minute': minute,
        'second': second,
        'millisecond': millisecond
    };
}


/**
 * 給定一維物件，轉為 URL GET 參數
 *
 * @param  {{key:string}} paramObj GET 參數物件
 * @return {string}                GET 參數字串
 */
function packParameter(paramObj)
{
    let keys = Object.keys(paramObj),
        paramArray = [],
        paramString = '';

    keys.forEach(function(key)
    {
        paramArray.push(`${key}=${paramObj[key]}`);
    });

    if (paramArray.length > 0)
    {
        paramString = '?' + paramArray.join('&');
    }

    return paramString;
}


/**
 * 依據指定的字元、數量及方向，在輸入字串的前或後填補字元（不支援 both）
 *
 * @param  {string} str       輸入字串
 * @param  {string} char      填補字元
 * @param  {number} num       填補數量
 * @param  {string} direction 填補方向（預設值 = 'left'）
 * @return {string}           填補結果
 */
function padding(str, char, num, direction = 'left')
{
    let dir = direction.toLowerCase(),
        i = 0,
        dif = 0,
        tar = '',
        len = str.toString().length;

    if (len < num)
    {
        dif = num - len;
        for (i = 0; i < dif; i++)
        {
            tar += char;
        }
        switch (dir)
        {
            default:
            case 'left':
            case 'front':
            case 'before':
            case 'default':
                tar += str.toString();
                break;

            case 'right':
            case 'after':
            case 'back':
                tar = str.toString() + tar;
                break;
        }
        return tar;
    }

    return str;
}


/**
 * 輸入 `Y-m-d` 或 `Y-m-d H:i:s` 格式的日期時間字串，將其轉為 Date 物件（可接受西元前日期）
 *
 * @param  {string} dateString 符合 `Y-m-d` 或 `Y-m-d H:i:s` 格式的日期時間字串
 * @return {{}}                Date 物件
 */
function parseDate(dateString)
{
    const CE = /^(CE|AD|\+)?\d{1,}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])( ([01]\d|2[0-3]):([0-5]\d):([0-5]\d))?$/,
          BCE = /^(BCE|BC|-)?\d{1,}-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])( ([01]\d|2[0-3]):([0-5]\d):([0-5]\d))?$/;

    let mode,
        date = '', year, month, day,
        time = '', hour, minute, second;

    if (CE.test(dateString)) {
        mode = 'CE';
    } else if (BCE.test(dateString)) {
        mode = 'BCE';
    } else {
        mode = 'Invalid';
    }

    if (mode !== 'Invalid') {
        let string = dateString.split(' ');

        date = string[0];

        let s = 0;
        // 年份以負號開頭時，擷取日期值應從 index 1 開始
        if (date.indexOf('-') == 0) {
            s++;
        }
        if (s === 0) {
            // 去除年份開頭的 BCE、BC、CE、AD
            date = date.replace(/(BCE|BC|CE|AD)/, '');

            if (mode === 'BCE') {
                year = parseInt('-' + date.split('-')[s++]) + 1;    // 西元前模式下年份加負號，數字部分減 1
            } else {
                year = parseInt(date.split('-')[s++]);
            }
        } else {
            // 年份以負號開頭時，擷取數字部分後要再把負號加在前面
            year = parseInt('-' + date.split('-')[s++]);
        }
        month = parseInt(date.split('-')[s++]) - 1;
        day   = parseInt(date.split('-')[s++]);

        if (string.length > 1) {
            time = string[1];
            hour   = parseInt(time.split(':')[0]);
            minute = parseInt(time.split(':')[1]);
            second = parseInt(time.split(':')[2]);
        } else {
            hour   = 0;
            minute = 0;
            second = 0;
        }
    }

    // console.log(year, month, day, hour, minute, second);

    if (typeof year   !== 'undefined' &&
        typeof month  !== 'undefined' &&
        typeof day    !== 'undefined' &&
        typeof hour   !== 'undefined' &&
        typeof minute !== 'undefined' &&
        typeof second !== 'undefined')
    {
        let dateObj = new Date(year, month, day, hour, minute, second);
        dateObj.setFullYear(year);

        return dateObj;
    }
    // 年月日時分秒任一獲取失敗或未定義，則返回基於當前時間的 Date 物件
    else
    {
        return new Date();
    }
}


/**
 * 產生指定區間的亂數
 *
 * @param  {number} floor 下限（預設值 = 0）
 * @param  {number} ceil  上限（預設值 = 1）
 * @return {number}       結果亂數
 * @throws                下限大於上限或其他異常狀況時拋出錯誤
 */
function randNum(floor = 0, ceil = 1)
{
    try {
        if (ceil < floor)
        {
            throw "Ceil is less than floor!";
        }
        let rand = Math.random() * (ceil - floor);
        return floor + rand;
    }
    catch (err)
    {
        console.error(err);
    }
}


/**
 * 產生由數字或數字 + 英文字母組成的隨機字串，radix 為 36 且 caps 為 true 時等於偽 62 進位隨機亂數
 *
 * @param  {number}  radix 進位制
 * @param  {number}  len   輸出字串長度
 * @param  {boolean} caps  是否包含大寫字元（預設值 = false）
 * @return {string}        結果亂數（字串型態）
 * @throws                 進位制不在 2～36 範圍內時拋出錯誤
 */
function randStr(radix, len, caps = false)
{
    if (radix < 2 || radix > 36)
    {
        console.log("The radix must be between 2 and 36.");
        return undefined;
    }
    else
    {
        let str = "",
            s = Math.random().toString(radix).split(".")[1];

        while (s.length < len)
        {
            s += Math.random().toString(radix).split(".")[1];
        }
        s = s.substring(0, len);

        for (let i = 0; i < s.length; i++)
        {
            if (/[a-z]/.test(s[i]) && caps && parseInt(Math.random() * 2))
            {
                str += s[i].toUpperCase();
            }
            else
            {
                str += s[i];
            }
        }

        return str;
    }
}


/**
 * 將帶時間戳的 62 進位字串（最左 10 位數視為時間戳）轉為 `Y-m-d H:i:s.u` 格式的時間字串
 *
 * @param  {string} base62 待轉換的 62 進位字串
 * @return {string}        `Y-m-d H:i:s.u` 格式的時間字串
 */
function reverseTimedBase62(base62)
{
    let timeBase62 = String(base62),
        length = timeBase62.length;

    if (length > 10) {
        timeBase62 = timeBase62.substr(0, 10);
    }

    let timeBase10 = String(base62to10(timeBase62)),
        microsecond = timeBase10.substr(-6),
        timestamp = Number(timeBase10.substr(0, timeBase10.length - 3)),
        date = dateFormat(timestamp) + '.' + microsecond;

    return date;
}


/**
 * 產生帶時間戳的 62 進位字串（時間戳向左補滿 10 位數）
 *
 * @param  {number} len  轉出字串長度
 * @param  {string} time 16 位微秒級時間戳，預設為 null，即自動取得當前時間
 * @return {string}      轉換完畢的 62 進位字串
 */
function timedBase62(len, time = null)
{
    if (time === null) {
        time = microTimestamp(true);
    }

    let timeBase62 = padding(base10to62(time), '0', 10),
        length = timeBase62.length,
        paddingDigit = Number(len) - length,
        paddingStr = '';

    if (paddingDigit > 0) {
        paddingStr = randStr(36, paddingDigit, true);
    } else {
        if (paddingDigit >= -length) {
            timeBase62 = timeBase62.substr(0, length + paddingDigit);
        }
    }

    return timeBase62 + paddingStr;
}
