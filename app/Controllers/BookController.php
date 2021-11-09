<?php

namespace App\Controllers;

use Throwable;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Logger;
use App\Constant;
use App\Handlers\BookHandler;

/**
 * 書籍資料控制器
 */
class BookController
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
     * 新增書籍資料
     *
     * @return void
     */
    public function addBook(): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = Request::getInstance()->getData();

            $filteredData = $input;    // 這裡需要資料驗證

            $result = BookHandler::getInstance()->add($filteredData);

            $output['Data'] = $result;
        }
        catch (Throwable $ex)
        {
            $httpStatusCode = 500;

            $exType    = get_class($ex);
            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} {$exType}({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);
        }

        Response::getInstance()->setCode($httpStatusCode)->output(JsonUnescaped($output));
    }

    /**
     * 指定欄位及關鍵字查詢書籍資料
     *
     * @param  string          $field  欄位
     * @param  string|integer  $param  關鍵字
     * @return void
     */
    public function getBooks(string $field, mixed $param): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        $includeDeleted = false;

        try
        {
            // 這裡需要資料驗證

            if (isset($_GET['d']) && $_GET['d'] == '1')
            {
                $includeDeleted = true;
            }

            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? (int) $_GET['p'] : Constant::DefaultPageNumber;
            $limit = (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] <= Constant::MaxDataCountPerPage) ? (int) $_GET['c'] : Constant::DefaultPageLimit;
            $offset = ($page - 1) * $limit;

            $output['Data'] = BookHandler::getInstance()->get($field, $param, $limit, $offset, $includeDeleted);
        }
        catch (Throwable $ex)
        {
            $httpStatusCode = 500;

            $exType    = get_class($ex);
            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} {$exType}({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);
        }

        Response::getInstance()->setCode($httpStatusCode)->output(JsonUnescaped($output));
    }

    public function editBook(int $bookId): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = Request::getInstance()->getData();

            $filteredData = $input;    // 這裡需要資料驗證

            $output['Data'] = BookHandler::getInstance()->edit($bookId, $filteredData);
        }
        catch (Throwable $ex)
        {
            $httpStatusCode = 500;

            $exType    = get_class($ex);
            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} {$exType}({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);
        }

        Response::getInstance()->setCode($httpStatusCode)->output(JsonUnescaped($output));
    }

    /**
     * 刪除指定 ID 的書籍資料（軟刪除）
     *
     * @param  integer  $bookId  書籍 ID
     * @return void
     */
    public function deleteBook(int $bookId): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = Request::getInstance()->getData();

            $filteredData = $input;    // 這裡需要資料驗證
            $deleteType = $filteredData['DeleteType'];

            $output['Data'] = BookHandler::getInstance()->delete($bookId, $deleteType);
        }
        catch (Throwable $ex)
        {
            $httpStatusCode = 500;

            $exType    = get_class($ex);
            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} {$exType}({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);
        }

        Response::getInstance()->setCode($httpStatusCode)->output(JsonUnescaped($output));
    }
}
