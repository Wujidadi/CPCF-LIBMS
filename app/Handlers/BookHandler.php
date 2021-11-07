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
     * 指定欄位和值查詢書籍資料
     *
     * @param  string          $field           欄位名稱
     * @param  string|integer  $param           關鍵字
     * @param  boolean         $includeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return array
     */
    public function get(string $field, mixed $param, bool $includeDeleted): array
    {
        $functionName = __FUNCTION__;

        if (in_array($field, $this->_allowedQueryField))
        {
            if (in_array($field, [ 'ISN', 'EAN' ]))
            {
                $param = str_replace('-', '', $param);
            }

            $result = BookModel::getInstance()->get($field, $param, $includeDeleted);
            return [
                'BookTotal' => count($result),
                'BookList'  => $result
            ];
        }
        else
        {
            $errorMessage = "Given field ({$field}) is not allowed";

            $logMessage = "{$this->_className}::{$functionName} Error: {$errorMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($errorMessage, 69);    // 「BookData」的字母值加總
        }
    }
}
