<?php

namespace App\Validators\InputCheckers;

use Libraries\Logger;
use App\ExceptionCode;
use App\Validators\InputChecker;
use App\Validators\InputException;

/**
 * 借閱者/會員資料輸入驗證器
 */
class MemberInputChecker extends InputChecker
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
     * 驗證新增借閱者/會員輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyAdd(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkNo();
        $this->_checkName();
        $this->_checkEmail();
        $this->_checkGender();
        $this->_checkBirthday();
        $this->_checkAddress();
        $this->_checkTel();
        $this->_checkMobile();
        $this->_checkJoinDate();
        $this->_checkNotes();

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = json_encode($this->_rawInput);
            $erroInfo = json_encode($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 驗證查詢借閱者/會員輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyGet(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_verifyGetParam();

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = json_encode($this->_rawInput);
            $erroInfo = json_encode($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
        else
        {
            $field = $this->_rawInput['Field'];
            $this->_filteredData['Value'] = $this->_filteredData[$field];
        }
    }

    /**
     * 驗證查詢借閱者/會員輸入資料的欄位和值
     *
     * @return void
     */
    protected function _verifyGetParam(): void
    {
        $allowedField = [ 'No', 'Name' ];

        if (in_array($this->_rawInput['Field'], $allowedField))
        {
            $field = $this->_filteredData['Field'] = $this->_rawInput['Field'];
            $this->_rawInput[$field] = $this->_rawInput['Value'];

            $checkMethod = "_check{$field}";

            $this->{$checkMethod}();
        }
        else
        {
            $this->_pushError(['field' => 'Field', 'reason' => self::NOT_ALLOWED]);
        }
    }

    /**
     * 驗證修改借閱者/會員輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyEdit(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkName();
        $this->_checkEmail();
        $this->_checkGender();
        $this->_checkBirthday();
        $this->_checkAddress();
        $this->_checkTel();
        $this->_checkMobile();
        $this->_checkJoinDate();
        $this->_checkNotes();

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = json_encode($this->_rawInput);
            $erroInfo = json_encode($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
        else
        {
            $field = $this->_rawInput['Field'];
            $this->_filteredData['Value'] = $this->_filteredData[$field];
        }
    }

    /**
     * 驗證刪除借閱者/會員輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyDisable(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId();

        $illegal = (count($this->_errors['fields']) > 0) ? true : false;
        if ($illegal)
        {
            $rawInput = json_encode($this->_rawInput);
            $erroInfo = json_encode($this->_errors['info']);
            Logger::getInstance()->logInfo("{$this->_className}::{$functionName} Input: {$rawInput}");
            Logger::getInstance()->logWarning("{$this->_className}::{$functionName} Error: {$erroInfo}");
            throw new InputException('Input Error', ExceptionCode::Input);
        }
    }

    /**
     * 檢查輸入的借閱者/會員 ID
     *
     * @return void
     */
    protected function _checkId(): void
    {
        $field = 'Id';

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
     * 檢查輸入的借閱者/會員編號
     *
     * @return void
     */
    protected function _checkNo(): void
    {
        $field = 'No';
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

    /**
     * 檢查輸入的借閱者/會員姓名
     *
     * @return void
     */
    protected function _checkName(): void
    {
        $field = 'Name';
        $length = 1000;

        if ($this->_isNull($field))
        {
            $this->_pushError(['field' => $field, 'reason' => self::REQUIRED_BUT_NULL]);
        }
        else if ($this->_isInvalidLength($this->_rawInput[$field], $length, true))
        {
            $this->_pushError(['field' => $field, 'reason' => self::INVALID_LENGTH, 'input' => $this->_rawInput[$field]]);
        }
        else
        {
            $this->_filteredData[$field] = $this->_rawInput[$field];
        }
    }

    /**
     * 檢查輸入的借閱者/會員電子郵件信箱
     *
     * @return void
     */
    protected function _checkEmail(): void
    {
        $field = 'Email';
        $regex = EmailFormat;

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isIllegalRegex($this->_rawInput[$field], $regex))
            {
                $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員性別
     *
     * @return void
     */
    protected function _checkGender(): void
    {
        $field = 'Email';
        $allowedValue = [ 0, 1 ];

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isNotAllowed($this->_rawInput[$field], $allowedValue))
            {
                $this->_pushError(['field' => $field, 'reason' => self::NOT_ALLOWED, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員出生年月日
     *
     * @return void
     */
    protected function _checkBirthday(): void
    {
        $field = 'Birthday';
        $regex = '/^\d{4}[\-\/]\d{2}[\-\/]\d{2}$/';
        $dateFormat = 'Y-m-d';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isIllegalRegex($this->_rawInput[$field], $regex))
            {
                $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
            }
            else if ($this->_isInvalidDate($this->_rawInput[$field], $dateFormat))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_DATE, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員通訊地址
     *
     * @return void
     */
    protected function _checkAddress(): void
    {
        $field = 'Address';
        $length = 512;

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidLength($this->_rawInput[$field], $length, true))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_LENGTH, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員電話號碼
     *
     * @return void
     */
    protected function _checkTel(): void
    {
        $field = 'Email';
        $regex = '/^[\d\-\+\(\),#]{3,20}$/';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isIllegalRegex($this->_rawInput[$field], $regex))
            {
                $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員手機號碼
     *
     * @return void
     */
    protected function _checkMobile(): void
    {
        $field = 'Mobile';
        $regex = '/^[\d\-\+\(\),#]{3,20}$/';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isIllegalRegex($this->_rawInput[$field], $regex))
            {
                $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的借閱者/會員入會日期
     *
     * @return void
     */
    protected function _checkJoinDate(): void
    {
        $field = 'Birthday';
        $regex = '/^\d{4}[\-\/]\d{2}[\-\/]\d{2}$/';
        $dateFormat = 'Y-m-d';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isIllegalRegex($this->_rawInput[$field], $regex))
            {
                $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
            }
            else if ($this->_isInvalidDate($this->_rawInput[$field], $dateFormat))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_DATE, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的附註
     *
     * 無限制規則及長度，事實上不必檢查，僅直接將資料填入 `$this->_filteredData`
     *
     * @return void
     */
    protected function _checkNotes(): void
    {
        $field = 'Notes';
        
        $this->_filteredData[$field] = $this->_rawInput[$field];
    }
}
