<?php

namespace App\Validators\InputCheckers;

use Libraries\Logger;
use App\ExceptionCode;
use App\Validators\InputChecker;
use App\Validators\InputException;

/**
 * 書籍資料輸入驗證器
 */
class BookInputChecker extends InputChecker
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
     * 驗證新增書籍輸入資料
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
        $this->_checkOriginalName();
        $this->_checkAuthor();
        $this->_checkIllustrator();
        $this->_checkEditor();
        $this->_checkTranslator();
        $this->_checkSeries();
        $this->_checkPublisher();
        $this->_checkPublishDate();
        $this->_checkPublishDateType();
        $this->_checkEdition();
        $this->_checkPrint();
        $this->_checkStorageDate();
        $this->_checkStorageType();
        $this->_checkNotes();
        $this->_checkISN();
        $this->_checkEAN();
        $this->_checkBarcode(1);
        $this->_checkBarcode(2);
        $this->_checkBarcode(3);
        $this->_checkCategoryId();
        $this->_checkLocationId();

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
     * 驗證查詢書籍輸入資料
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
     * 驗證查詢書籍輸入資料的欄位和值
     *
     * @return void
     */
    protected function _verifyGetParam(): void
    {
        $allowedField = [
            'No',
            'Name', 'OriginalName',
            'Author', 'Illustrator', 'Editor', 'Translator', 'Maker',
            'Series',
            'Publisher',
            'ISN', 'EAN'
        ];

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
     * 驗證修改書籍輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyEdit(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkName();
        $this->_checkOriginalName();
        $this->_checkAuthor();
        $this->_checkIllustrator();
        $this->_checkEditor();
        $this->_checkTranslator();
        $this->_checkSeries();
        $this->_checkPublisher();
        $this->_checkNotes();
        $this->_checkISN();
        $this->_checkEAN();
        $this->_checkCategoryId();
        $this->_checkLocationId();

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
     * 驗證刪除書籍輸入資料
     *
     * @param  array  $input  輸入資料
     * @return void
     */
    public function verifyDelete(array $input): void
    {
        $functionName = __FUNCTION__;

        $this->_rawInput = $input;

        $this->_checkId();
        $this->_checkDeleteType();

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
     * 檢查輸入的書籍 ID
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
     * 檢查輸入的書號
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
     * 檢查輸入的書名
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
     * 檢查輸入的原文書名
     *
     * @return void
     */
    protected function _checkOriginalName(): void
    {
        $field = 'OriginalName';
        $length = 1000;

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
     * 檢查輸入的作者
     *
     * @return void
     */
    protected function _checkAuthor(): void
    {
        $field = 'Author';
        $length = 200;

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
     * 檢查輸入的繪者
     *
     * @return void
     */
    protected function _checkIllustrator(): void
    {
        $field = 'Illustrator';
        $length = 200;

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
     * 檢查輸入的編者
     *
     * @return void
     */
    protected function _checkEditor(): void
    {
        $field = 'Editor';
        $length = 200;

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
     * 檢查輸入的譯者
     *
     * @return void
     */
    protected function _checkTranslator(): void
    {
        $field = 'Translator';
        $length = 200;

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
     * 檢查輸入的創作者（作者、繪者、編者、譯者的組合欄位）
     *
     * @return void
     */
    protected function _checkMaker():void
    {
        $field = 'Maker';
        $length = 200;

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
     * 檢查輸入的系列/叢書名
     *
     * @return void
     */
    protected function _checkSeries(): void
    {
        $field = 'Series';
        $length = 200;

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
     * 檢查輸入的出版者
     *
     * @return void
     */
    protected function _checkPublisher(): void
    {
        $field = 'Publisher';
        $length = 200;

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
     * 檢查輸入的出版日期
     *
     * @return void
     */
    protected function _checkPublishDate(): void
    {
        $field = 'PublishDate';
        $regex = '/^\d{4}-\d{2}-\d{2}$/';
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
     * 檢查輸入的出版日期類別
     *
     * @return void
     */
    protected function _checkPublishDateType(): void
    {
        $field = 'PublishDateType';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidNumber($this->_rawInput[$field]))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的版本別
     *
     * @return void
     */
    protected function _checkEdition(): void
    {
        $field = 'Edition';
        $length = 30;

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
     * 檢查輸入的印刷別
     *
     * @return void
     */
    protected function _checkPrint(): void
    {
        $field = 'Print';
        $length = 30;

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
     * 檢查輸入的入庫日期
     *
     * @return void
     */
    protected function _checkStorageDate(): void
    {
        $field = 'StorageDate';
        $regex = '/^\d{4}-\d{2}-\d{2}$/';
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
     * 檢查輸入的入庫類別 ID
     *
     * @return void
     */
    protected function _checkStorageType(): void
    {
        $field = 'StorageType';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidNumber($this->_rawInput[$field]))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的報廢/刪除類別 ID
     *
     * @return void
     */
    protected function _checkDeleteType(): void
    {
        $field = 'DeleteType';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidNumber($this->_rawInput[$field]))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
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

    /**
     * 檢查輸入的 ISBN 或 ISSN
     *
     * @return void
     */
    protected function _checkISN(): void
    {
        $field = 'ISN';
        $regex8 = '/^\d{7}[\dX]$/';
        $regex10 = '/^\d{9}[\dX]$/';
        $regex13 = '/^\d{12}[\dX]$/';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            $alteredInput = preg_replace('/-/', '', $this->_rawInput[$field]);

            if ($this->_isIllegalRegex($alteredInput, $regex8) &&
                $this->_isIllegalRegex($alteredInput, $regex10) &&
                $this->_isIllegalRegex($alteredInput, $regex13))
            {
                if ($alteredInput === $this->_rawInput[$field])
                {
                    $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
                }
                else
                {
                    $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field], 'alteredInput' => $alteredInput]);
                }
            }
            else
            {
                $this->_filteredData[$field] = $alteredInput;
            }
        }
    }

    /**
     * 檢查輸入的國際商品條碼
     *
     * @return void
     */
    protected function _checkEAN(): void
    {
        $field = 'EAN';
        $regex8 = '/^\d{8}$/';
        $regex13 = '/^\d{13}$/';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            $alteredInput = preg_replace('/-/', '', $this->_rawInput[$field]);

            if ($this->_isIllegalRegex($alteredInput, $regex8) &&
                $this->_isIllegalRegex($alteredInput, $regex13))
            {
                if ($alteredInput === $this->_rawInput[$field])
                {
                    $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field]]);
                }
                else
                {
                    $this->_pushError(['field' => $field, 'reason' => self::ILLEGAL_REGEX, 'input' => $this->_rawInput[$field], 'alteredInput' => $alteredInput]);
                }
            }
            else
            {
                $this->_filteredData[$field] = $alteredInput;
            }
        }
    }

    /**
     * 檢查輸入的其他條碼
     *
     * @param  integer|string  $sn  其他條碼欄位序號
     * @return void
     */
    protected function _checkBarcode(mixed $sn): void
    {
        $field = "Barcode{$sn}";
        $length = 30;

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidLength($this->_rawInput[$field], $length))
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
     * 檢查輸入的書籍分類 ID
     *
     * @return void
     */
    protected function _checkCategoryId(): void
    {
        $field = 'CategoryId';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidNumber($this->_rawInput[$field]))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }

    /**
     * 檢查輸入的書籍架位 ID
     *
     * @return void
     */
    protected function _checkLocationId(): void
    {
        $field = 'LocationId';

        if (isset($this->_rawInput[$field]) && $this->_rawInput[$field] !== '')
        {
            if ($this->_isInvalidNumber($this->_rawInput[$field]))
            {
                $this->_pushError(['field' => $field, 'reason' => self::INVALID_NUMBER, 'input' => $this->_rawInput[$field]]);
            }
            else
            {
                $this->_filteredData[$field] = $this->_rawInput[$field];
            }
        }
    }
}
