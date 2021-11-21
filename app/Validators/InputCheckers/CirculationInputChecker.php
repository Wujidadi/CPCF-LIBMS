<?php

namespace App\Validators\InputCheckers;

use Libraries\Logger;
use App\ExceptionCode;
use App\Validators\InputChecker;
use App\Exceptions\InputException;

/**
 * 書籍流通（借還書）資料輸入驗證器
 */
class CirculationInputChecker extends InputChecker
{
    protected $_className;

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 驗證查詢借閱紀錄輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyGetRecords(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId();

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = JsonUnescaped($this->_rawInput);
            $erroInfo = JsonUnescaped($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 驗證查詢使用者未歸還書籍輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyGetBorrowingRecords(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkNo('Member');

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = JsonUnescaped($this->_rawInput);
            $erroInfo = JsonUnescaped($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 驗證查詢書籍當前流通狀態輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyGetStatus(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId('Book');

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = JsonUnescaped($this->_rawInput);
            $erroInfo = JsonUnescaped($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 驗證借書輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyBorrow(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId('Book');
        $this->_checkId('Member');

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = JsonUnescaped($this->_rawInput);
            $erroInfo = JsonUnescaped($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 驗證還書輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyReturn(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId('Book');

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = JsonUnescaped($this->_rawInput);
            $erroInfo = JsonUnescaped($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 檢查輸入的書籍或借閱者/會員 ID
     *
     * @param  string  $type  ID 類型（書籍或借閱者/會員）
     * @return void
     */
    protected function _checkId(string $type = ''): void
    {
        $field = "{$type}Id";

        if ($this->_isNull($field))
        {
            $this->_pushError(['field' => $field, 'reason' => self::REQUIRED_BUT_NULL]);
        }
        else if ($this->_isInvalidNumber($this->_rawInput[$field]))
        {
            $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
        }
        else
        {
            $this->_filteredData[$field] = $this->_rawInput[$field];
        }
    }

    /**
     * 檢查輸入的書籍或借閱者/會員編號
     *
     * @param  string  $type  編號類型（書籍或借閱者/會員）
     * @return void
     */
    protected function _checkNo(string $type = ''): void
    {
        $field = "{$type}No";
        $length = 10;

        if ($this->_isNull($field))
        {
            $this->_pushError(['field' => $field, 'reason' => self::REQUIRED_BUT_NULL]);
        }
        else if ($this->_isInvalidLength($this->_rawInput[$field], $length))
        {
            $this->_pushError(['field' => $field, 'reason' => self::INVALID_LENGTH, 'input' => $this->_rawInput[$field]]);
        }
        else
        {
            $this->_filteredData[$field] = $this->_rawInput[$field];
        }
    }
}
