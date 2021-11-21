/*
 *==============================
 * 覆寫原生方法
 *==============================
 */

/**
 * console.log() 的簡寫
 */
const echo = console.log;


/*
 *==============================
 * 字串相關方法
 *==============================
 */

/**
 * 以 underline（ 底線）作為 undefined 的簡寫
 */
const _ = undefined;

/**
 * 將指定字串的首字母轉為大寫
 *
 * 可選擇整句或每個單字的首字母大寫，以及其他字母是否轉小寫
 *
 * @param  {boolean} everyword 是否將每個單字的首字母均轉為大寫，預設 false
 * @param  {boolean} otherlc   是否將非首字母的其他字母轉為小寫，預設 true
 * @return {string}            轉換完畢的字串或句子
 */
String.prototype.ucfirst = function(everyword = false, otherlc = true)
{
    let string = this.split(' ');

    for (let i = 0; i < string.length; i++)
    {
        let tempString = string[i];

        if (everyword)
        {
            string[i] = tempString.slice(0, 1).toUpperCase();

            if (otherlc)
            {
                string[i] += tempString.slice(1).toLowerCase();
            }
            else
            {
                string[i] += tempString.slice(1);
            }
        }
        else
        {
            if (i === 0)
            {
                string[i] = tempString.slice(0, 1).toUpperCase();

                if (otherlc)
                {
                    string[i] += tempString.slice(1).toLowerCase();
                }
                else
                {
                    string[i] += tempString.slice(1);
                }
            }
            else
            {
                if (otherlc)
                {
                    string[i] = tempString.toLowerCase();
                }
                else
                {
                    string[i] = tempString;
                }
            }
        }
    }

    return string.join(' ');
};

/**
 * 將已轉成字串的秒數轉換為 HH:MM:SS 格式
 *
 * @return {string} HH:MM:SS 格式的秒數
 */
String.prototype.toHHMMSS = function()
{
    let ProtoSecond = parseInt(this, 10),
        Hour = Math.floor(ProtoSecond / 3600),
        Minute = Math.floor((ProtoSecond - (Hour * 3600)) / 60),
        Second = ProtoSecond - (Hour * 3600) - (Minute * 60);

    if (Hour < 10)
    {
        Hour = '0' + Hour;
    }
    if (Minute < 10)
    {
        Minute = '0' + Minute;
    }
    if (Second < 10)
    {
        Second = '0' + Second;
    }

    return Hour + ':' + Minute + ':' + Second;
};


/*
 *==============================
 * 物件相關方法
 *==============================
 */

/**
 * 取得物件屬性數量（透過 `Object.defineProperty` 在 `Object.prototype` 中添加的方法，ES5 以後適用）
 */
Object.defineProperty(Object.prototype, 'objectLength',
{
    get: function()
    {
        let obj = this,
            len = 0;

        for (let key in obj)
        {
            len++;
        }

        return len;
    },
    enumerable: false
});

/**
 * 清空物件（透過 `Object.defineProperty` 在 `Object.prototype` 中添加的方法，ES5 以後適用）
 */
Object.defineProperty(Object.prototype, 'removeAllProperties',
{
    value: function()
    {
        let obj = this;

        for (let key in obj)
        {
            delete obj[key];
        }
    },
    enumerable: false
});


/*
 *==============================
 * 元素相關方法
 *==============================
 */

/**
 * 清空所有子元素（在 `Element.prototype` 中添加的方法）
 */
Element.prototype.removeAllChildren = function()
{
    while (this.firstChild)
    {
        this.removeChild(this.firstChild);
    }
};
