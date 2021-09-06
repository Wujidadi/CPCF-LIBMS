<?php

namespace App\Handlers;

use Exception;
use Libraries\Logger;
use App\Models\BookModel;

/**
 * 書籍資料處理類別
 */
class BookHandler
{
    /**
     * 類別名稱
     *
     * @var string
     */
    protected $_className;

    /**
     * 可用於查詢書籍資料的欄位名稱
     *
     * @var string[]
     */
    protected $_allowedQueryField = [
        'No',
        'Name', 'OriginalName',
        'Author', 'Illustrator', 'Editor', 'Translator', 'Maker',
        'Series',
        'Publisher',
        'ISN', 'EAN'
    ];

    protected static $_uniqueInstance = null;

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
    }

    /** @return self */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    /**
     * 指定欄位和值查詢書籍資料
     *
     * @param  string          $strField           欄位名稱
     * @param  string|integer  $mixParam           關鍵字
     * @param  boolean         $bolIncludeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return void
     */
    public function get($strField, $mixParam, $bolIncludeDeleted)
    {
        $functionName = __FUNCTION__;

        if (in_array($strField, $this->_allowedQueryField))
        {
            if (in_array($strField, [ 'ISN', 'EAN' ]))
            {
                $mixParam = str_replace('-', '', $mixParam);
            }

            $arrResult = BookModel::getInstance()->get($strField, $mixParam, $bolIncludeDeleted);
            return [
                'BookTotal' => count($arrResult),
                'BookList'  => $arrResult
            ];
        }
        else
        {
            $strErrorMessage = "Given field ({$strField}) is not allowed";

            $strLogMessage = "{$this->_className}::{$functionName} Error: {$strErrorMessage}";
            Logger::getInstance()->logError($strLogMessage);

            throw new Exception($strErrorMessage, 69);    // Sum of alphabet number of "BookData"
        }
    }
}
