<?php

namespace App\Controllers\API;

use App\Controller;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Logger;
use App\Constant;
use App\Handlers\BookHandler;
use App\Validators\InputCheckers\BookInputChecker;
use App\Exceptions\InputException;

/**
 * 書籍資料控制器
 */
class BookController extends Controller
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

            BookInputChecker::getInstance()->verifyAdd($input);
            $filteredData = BookInputChecker::getInstance()->getFilteredData();

            $result = BookHandler::getInstance()->addBook($filteredData);

            $output['Data'] = $result;
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => BookInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (\Throwable $ex)
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
     * 查詢全部書籍資料
     *
     * @return void
     */
    public function getAllBooks(): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        $appendField = [];

        $includeDeleted = false;

        try
        {
            $input = [];
            if (isset($_GET['m']) && $_GET['m'] !== '')
            {
                $input['Maker'] = $_GET['m'];
            }
            if (isset($_GET['r']) && $_GET['r'] !== '')
            {
                $input['Publisher'] = $_GET['r'];
            }
            if (count($input) > 0)
            {
                BookInputChecker::getInstance()->verifyGetAll($input);
                $filteredData = BookInputChecker::getInstance()->getFilteredData();
                if (isset($filteredData['Maker']))
                {
                    $appendField['Maker'] = $_GET['m'];
                }
                if (isset($filteredData['Publisher']))
                {
                    $appendField['Publisher'] = $_GET['r'];
                }
            }

            if (isset($_GET['d']) && $_GET['d'] == '1')
            {
                $includeDeleted = true;
            }

            $page = (isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] > 0) ? (int) $_GET['p'] : Constant::DefaultPageNumber;
            $limit = (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0 && $_GET['c'] <= Constant::MaxDataCountPerPage) ? (int) $_GET['c'] : Constant::DefaultPageLimit;
            $offset = ($page - 1) * $limit;

            $output['Data'] = BookHandler::getInstance()->getAllBooks($limit, $offset, $appendField, $includeDeleted);
        }
        catch (\Throwable $ex)
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
     * @param  string|integer  $value  關鍵字
     * @return void
     */
    public function getBooks(string $field, mixed $value): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        $appendField = [];

        $includeDeleted = false;

        try
        {
            $input = [ 'Field' => $field, 'Value' => $value ];

            if (isset($_GET['m']) && $_GET['m'] !== '')
            {
                $input['Maker'] = $_GET['m'];
            }
            if (isset($_GET['r']) && $_GET['r'] !== '')
            {
                $input['Publisher'] = $_GET['r'];
            }

            BookInputChecker::getInstance()->verifyGetByField($input);
            $filteredData = BookInputChecker::getInstance()->getFilteredData();
            $field = $filteredData['Field'];
            $value = $filteredData['Value'];

            if (isset($filteredData['Maker']))
            {
                $appendField['Maker'] = $_GET['m'];
            }
            if (isset($filteredData['Publisher']))
            {
                $appendField['Publisher'] = $_GET['r'];
            }

            if (isset($_GET['d']) && $_GET['d'] == '1')
            {
                $includeDeleted = true;
            }

            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? (int) $_GET['p'] : Constant::DefaultPageNumber;
            $limit = (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] > 0 && $_GET['c'] <= Constant::MaxDataCountPerPage) ? (int) $_GET['c'] : Constant::DefaultPageLimit;
            $offset = ($page - 1) * $limit;

            $output['Data'] = BookHandler::getInstance()->getBooks($field, $value, $limit, $offset, $appendField, $includeDeleted);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => BookInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (\Throwable $ex)
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
     * 修改書籍資料
     *
     * @param  integer|string  $bookId  書籍 ID
     * @return void
     */
    public function editBook(mixed $bookId): void
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

            BookInputChecker::getInstance()->verifyEdit($input);
            $filteredData = BookInputChecker::getInstance()->getFilteredData();

            $output['Data'] = BookHandler::getInstance()->editBook($bookId, $filteredData);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => BookInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (\Throwable $ex)
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
     * @param  integer|string  $bookId  書籍 ID
     * @return void
     */
    public function deleteBook(mixed $bookId): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input['Id'] = $bookId;

            BookInputChecker::getInstance()->verifyDelete($input);
            $filteredData = BookInputChecker::getInstance()->getFilteredData();
            $bookId = $filteredData['Id'];
            $deleteType = $filteredData['DeleteType'] ?? null;

            $output['Data'] = BookHandler::getInstance()->deleteBook($bookId, $deleteType);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => BookInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (\Throwable $ex)
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
