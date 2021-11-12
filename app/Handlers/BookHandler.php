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

        if (in_array($field, [ 'ISN', 'EAN' ]))
        {
            $param = str_replace('-', '', $param);
        }

        $result = BookModel::getInstance()->get($field, $param, $limit, $offset, $includeDeleted);

        # 移除時區標記
        $bookList = array_map(function($row) {
            $row['CreatedAt'] = preg_replace(TimeZoneSuffix, '', $row['CreatedAt']);
            $row['UpdatedAt'] = preg_replace(TimeZoneSuffix, '', $row['UpdatedAt']);
            return $row;
        }, $result);

        return [
            'Total' => count($result),
            'List'  => $bookList
        ];
    }

    /**
     * 編輯書籍資料
     *
     * @param  integer  $bookId  書籍 ID
     * @param  array    $data    待更新的書籍資料
     * @return integer
     */
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

    /**
     * 刪除書籍資料（軟刪除）
     *
     * @param  integer       $bookId      書籍 ID
     * @param  integer|null  $deleteType  刪除原因類別 ID
     * @return integer
     */
    public function delete(int $bookId, ?int $deleteType = null): int
    {
        $functionName = __FUNCTION__;

        return BookModel::getInstance()->delete($bookId, $deleteType);
    }
}
