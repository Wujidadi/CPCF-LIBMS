<?php

namespace App\Handlers;

use Exception;
use Libraries\Logger;
use App\ExceptionCode;
use App\Models\MemberModel;

/**
 * 借閱者/會員資料處理器
 */
class MemberHandler
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
     * 新增借閱者/會員資料
     *
     * @param  array   $data  待新增借閱者/會員資料
     * @return integer
     */
    public function addMember(array $data): int
    {
        $functionName = __FUNCTION__;

        if (!isset($data['Membership']))
        {
            $data['Membership'] = 1;
        }

        if (!isset($data['Disabled']))
        {
            $data['Disabled'] = false;
        }

        return MemberModel::getInstance()->insertOne($data);
    }

    /**
     * 指定欄位和值查詢複數借閱者/會員資料
     *
     * @param  string          $field            欄位名稱
     * @param  string|integer  $param            關鍵字
     * @param  integer         $limit            查詢資料限制筆數
     * @param  integer         $offset           查詢資料偏移量
     * @param  boolean         $includeDisabled  是否包含被禁用借閱者/會員
     * @return array
     */
    public function getMembers(string $field, mixed $param, int $limit, int $offset, bool $includeDisabled): array
    {
        $functionName = __FUNCTION__;

        $result = MemberModel::getInstance()->selectMultiple($field, $param, $limit, $offset, $includeDisabled);

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
     * 編輯借閱者/會員資料
     *
     * @param  integer  $memberId  借閱者/會員 ID
     * @param  array    $data    待更新的借閱者/會員資料
     * @return integer
     */
    public function editMember(int $memberId, array $data): int
    {
        $functionName = __FUNCTION__;

        $updatedData = [];

        foreach ($data as $field => $value)
        {
            $updatedData[$field] = $value;
        }

        if (count($updatedData) > 0)
        {
            return MemberModel::getInstance()->updateOne($memberId, $updatedData);
        }
        else
        {
            $errorMessage = "No data to be updated";

            $logMessage = "{$this->_className}::{$functionName} Error: {$errorMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($errorMessage, ExceptionCode::MemberData);
        }
    }

    /**
     * 禁用借閱者/會員
     *
     * @param  integer  $memberId  借閱者/會員 ID
     * @param  boolean  $action    禁用（`true`）或啟用（`false`）
     * @return integer
     */
    public function disableMember(int $memberId, bool $action = true): int
    {
        $functionName = __FUNCTION__;

        return MemberModel::getInstance()->disableOne($memberId, $action);
    }
}
