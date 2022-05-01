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
     * 錯誤原因：必填欄位為空值
     *
     * @var string
     */
    const REQUIRED_BUT_NULL = 'Required but null';

    /**
     * 錯誤原因：不合法的正規表示式
     */
    const ILLEGAL_REGEX = 'Illegal regex';

    /**
     * 錯誤原因：不正確的字串長度
     */
    const INVALID_LENGTH = 'Invalid length';

    /**
     * 錯誤原因：不正確的數字
     */
    const INVALID_NUMBER = 'Invalid number';

    /**
     * 錯誤原因：不正確的日期/時間
     */
    const INVALID_DATE = 'Invalid date';

    /**
     * 錯誤原因：不允許的列舉值
     */
    const NOT_ALLOWED = 'Not allowed';

    /**
     * 錯誤原因：無效資料
     */
    const INVALID_DATA = 'Invalid data';

    /**
     * 原始輸入資料
     *
     * @var array
     */
    protected $_rawInput = [];

    /**
     * 經過驗證的輸入資料
     *
     * @var array
     */
    protected $_filteredData = [];

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
    protected function _isNull(string $field): bool
    {
        return (!isset($this->_rawInput[$field]) || trim($this->_rawInput[$field]) == '') ? true : false;
    }

    /**
     * 檢查輸入資料是否符合允許值
     *
     * @param  string|integer|double  $value         要檢驗的資料
     * @param  array                  $allowedValue  允許值陣列
     * @return boolean
     */
    protected function _isNotAllowed(mixed $value, array $allowedValue): bool
    {
        return (!in_array($value, $allowedValue)) ? true : false;
    }

    /**
     * 以正規表示法檢驗輸入的字串
     *
     * @param  string  $text     要檢驗的字串
     * @param  string  $pattern  正規表示式
     * @return boolean
     */
    protected function _isIllegalRegex(string $text, string $pattern): bool
    {
        return (!preg_match($pattern, $text)) ? true : false;
    }

    /**
     * 檢查字串長度是否正確
     *
     * @param  string             $text       要檢驗的字串
     * @param  integer|integer[]  $length     字串長度：可為單一整數（最大長度）或陣列（最小長度與最大長度）
     * @param  boolean            $multibyte  是否依多位元字串計算
     * @return boolean
     */
    protected function _isInvalidLength(string $text, mixed $length, bool $multibyte = false): bool
    {
        $minLen = 0;
        if (is_array($length))
        {
            $minLen = $length[0];
            $maxLen = $length[1];
        }
        else
        {
            $maxLen = $length;
        }

        if ($multibyte)
        {
            return (mb_strlen($text) < $minLen || mb_strlen($text) > $maxLen) ? true : false;
        }
        else
        {
            return (strlen($text) < $minLen || strlen($text) > $maxLen) ? true : false;
        }
    }

    /**
     * 檢查輸入的數字或數字字串是否正確（不可解析或負數）
     *
     * @param  integer|double|string  $number         要檢驗的數字或數字字串
     * @param  boolean                $canBeNegative  是否允許為負數
     * @return boolean
     */
    protected function _isInvalidNumber(mixed $number, bool $canBeNegative = false): bool
    {
        if (is_numeric($number))
        {
            return (!$canBeNegative && $number < 0) ? true : false;
        }
        return true;
    }

    /**
     * 檢驗輸入的日期字串是否正確
     *
     * @param  string  $date     要檢驗的日期字串
     * @param  string  $format  日期格式
     * @return boolean
     */
    protected function _isInvalidDate(string $date, string $format): bool
    {
        $objDT = \DateTime::createFromFormat($format, $date);
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
     * 將錯誤欄位資料加入錯誤收集陣列
     *
     * @param  array  $error  錯誤欄位資料
     * @return void
     */
    protected function _pushError(array $error): void
    {
        $this->_errors['info'][] = $error;
        $this->_errors['fields'][] = $error['field'];
    }

    /**
     * 取得驗證錯誤欄位名稱陣列
     *
     * @return array
     */
    public function getErrorFields(): array
    {
        return $this->_errors['fields'];
    }

    /**
     * 重設驗證錯誤資訊陣列
     *
     * @return void
     */
    public function resetErrorInfo(): void
    {
        $this->_errors = [
            'info' => [],
            'fields' => []
        ];
    }

    /**
     * 取得經過驗證的輸入資料
     *
     * @return array
     */
    public function getFilteredData(): array
    {
        return $this->_filteredData;
    }
}
