<?php

namespace App\Handlers;

use Exception;
use Libraries\Logger;
use App\ExceptionCode;
use App\Models\BookModel;

/**
 * 書籍資料處理類別
 */
class BookHandler
{
    /**
     * 可用於查詢書籍資料的欄位名稱（應以輸入驗證取代）
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

    /**
     * 書籍資料中可修改的欄位名稱（應以輸入驗證取代）
     *
     * @var string[]
     */
    protected $_allowedEditField = [
        'Name', 'OriginalName',
        'Author', 'Illustrator', 'Editor', 'Translator',
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
     * 新增書籍資料
     *
     * @param  array   $data  待新增書籍資料
     * @return integer
     */
    public function add(array $data): int
    {
        $functionName = __FUNCTION__;

        if (!isset($data['Deleted']))
        {
            $data['Deleted'] = false;
        }

        return BookModel::getInstance()->addOne($data);
    }

    /**
     * 指定欄位和值查詢書籍資料
     *
     * @param  string          $field           欄位名稱
     * @param  string|integer  $param           關鍵字
     * @param  integer         $limit           查詢資料限制筆數
     * @param  integer         $offset          查詢資料偏移量
     * @param  boolean         $includeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return array
     */
    public function get(string $field, mixed $param, int $limit, int $offset, bool $includeDeleted): array
    {
        $functionName = __FUNCTION__;

        // if (in_array($field, $this->_allowedQueryField))
        // {
            if (in_array($field, [ 'ISN', 'EAN' ]))
            {
                $param = str_replace('-', '', $param);
            }

            $result = BookModel::getInstance()->get($field, $param, $limit, $offset, $includeDeleted);

            return [
                'Total' => count($result),
                'List'  => $result
            ];
        // }
        // else
        // {
        //     $errorMessage = "Given field ({$field}) is not allowed";

        //     $logMessage = "{$this->_className}::{$functionName} Error: {$errorMessage}";
        //     Logger::getInstance()->logError($logMessage);

        //     throw new Exception($errorMessage, ExceptionCode::BookData);
        // }
    }

    public function edit(int $bookId, array $data): int
    {
        $functionName = __FUNCTION__;

        $updatedData = [];

        foreach ($data as $field => $value)
        {
            if (in_array($field, [ 'ISN', 'EAN' ]))
            {
                $value = str_replace('-', '', $value);
            }
            $updatedData[$field] = $value;
        }

        if (count($updatedData) > 0)
        {
            return BookModel::getInstance()->edit($bookId, $updatedData);
        }
        else
        {
            $errorMessage = "No data to be updated";

            $logMessage = "{$this->_className}::{$functionName} Error: {$errorMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($errorMessage, ExceptionCode::BookData);
        }
    }
}
