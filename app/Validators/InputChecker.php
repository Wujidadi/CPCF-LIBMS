<?php

namespace App\Validators;

/**
 * 輸入驗證抽象類別
 */
abstract class InputChecker
{
    /**
     * 類別名稱
     *
     * @var string
     */
    protected $_className;

    /**
     * 原始輸入資料
     *
     * @var array
     */
    protected $_rawInput;

    /**
     * 經過驗證的輸入資料
     *
     * @var array
     */
    protected $_filteredData;

    /**
     * 驗證錯誤資訊
     *
     * @var array
     */
    protected $_errors = [
        'info' => [],
        'fields' => []
    ];

    /**
     * 取得類別實例
     *
     * @return self
     */
    abstract public static function getInstance();

    /**
     * 檢查輸入資料中指定名稱的欄位是否為空
     *
     * @param  string  $field  欄位名稱
     * @return boolean
     */
    protected function _isNull($field)
    {
        return (!isset($this->_rawInput[$field]) || trim($this->_rawInput[$field]) == '') ? true : false;
    }

    /**
     * 以正規表示法檢驗輸入的字串
     *
     * @param  string  $text     要檢驗的字串
     * @param  string  $pattern  正規表示式
     * @return boolean
     */
    protected function _isRegexIllegl($text, $pattern)
    {
        return (!preg_match($pattern, $text)) ? true : false;
    }

    /**
     * 檢驗輸入的日期字串是否非法
     *
     * @param  string  $text     要檢驗的日期字串
     * @param  string  $pattern  日期格式
     * @return boolean
     */
    protected function _isInvalidDate($date, $format)
    {
        $objDT = DateTime::createFromFormat($format, $date);
        if ($objDT && $objDT->format($format) == $date)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * 檢查輸入的數字或數字字串是否非法（不可解析或負數）
     *
     * @param  integer|double|string  $number  要檢驗的數字或數字字串
     * @return boolean
     */
    protected function _isInvalidNumber($number)
    {
        if (is_numeric($number))
        {
            return ($number < 0) ? true : false;
        }
        return true;
    }

    /**
     * 將錯誤欄位資料加入錯誤收集陣列
     *
     * @param  array  $error  錯誤欄位資料
     * @return void
     */
    protected function _pushError($error)
    {
        $this->_errors['info'][] = $error;
        $this->_errors['fields'][] = $error['field'];
    }

    /**
     * 取得驗證錯誤欄位名稱陣列
     *
     * @return array
     */
    public function getErrorColumns()
    {
        return $this->_errors['fields'];
    }

    /**
     * 重設驗證錯誤資訊陣列
     *
     * @return void
     */
    public function resetErrorInfo()
    {
        $this->_errors = [
            'info' => [],
            'fields' => []
        ];
    }

    /**
     * 取得經過驗證的輸入資料
     *
     * @return object
     */
    public function getFilteredData()
    {
        return $this->_filteredData;
    }
}
