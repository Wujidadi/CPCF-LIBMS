<?php

namespace App\Controllers;

use Throwable;
use App\Handlers\BookHandler;

/**
 * 書籍資料控制器
 */
class BookController
{
    /**
     * 類別名稱
     *
     * @var string
     */
    protected $_className;

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
     * 指定欄位及關鍵字查詢書籍資料
     *
     * @param  string          $field  欄位
     * @param  string|integer  $param  關鍵字
     * @return void
     */
    public function getBooks($field, $param)
    {
        $functionName = __FUNCTION__;

        $output = [
            'Code'    => '200',
            'Message' => 'OK'
        ];

        header('Content-Type: application/json');
        header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");

        try
        {
            $bookData = BookHandler::getInstance()->get($field, $param);

            $output['Data'] = $bookData;
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
}
