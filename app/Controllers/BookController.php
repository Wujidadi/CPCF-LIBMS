<?php

namespace App\Controllers;

use Throwable;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Logger;
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

            $output['Data'] = $input;
        }
        catch (Throwable $ex)
        {
            $httpStatusCode = 500;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            
            $logMessage = "{$this->_className}::{$functionName} Exception: ({$exCode}) {$exMessage}";
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
            if (isset($_GET['include-deleted']))
            {
                $includeDeleted = true;
            }

            $output['Data'] = BookHandler::getInstance()->get($field, $param, $includeDeleted);
        }
        catch (Throwable $ex)
        {
            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            
            $strLogMessage = "{$this->_className}::{$functionName} (by {$field} with parameter: {$param}) Exception: ({$exCode}) {$exMessage}";

            header("{$_SERVER['SERVER_PROTOCOL']} 500 Internal Server Error");
        }

        echo JsonUnescaped($output);
    }

    /**
     * 刪除指定 ID 的書籍資料（軟刪除）
     *
     * @param  integer  $bookId  書籍 ID
     * @return void
     */
    public function deleteBook(int $bookId): void
    {
        //
    }
}
