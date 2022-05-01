<?php

namespace App\Controllers\API;

use App\Controller;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Logger;
use App\Constant;
use App\ExceptionCode;
use App\Handlers\CirculationHandler;
use App\Validators\InputCheckers\CirculationInputChecker;
use App\Exceptions\InputException;
use App\Exceptions\CirculationException;

/**
 * 書籍流通（借還書）控制器
 */
class CirculationController extends Controller
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
     * 查詢借閱紀錄
     *
     * @param  integer|string  $id           書籍或借閱者/會員 ID
     * @param  boolean         $getByMember  是否以借閱者/會員 ID 進行查詢
     * @return void
     */
    public function getRecords(mixed $id, bool $getByMember = false): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        $now = MsTimestamp();

        try
        {
            $input = [ 'Id' => $id ];

            CirculationInputChecker::getInstance()->verifyGetRecords($input);
            $filteredData = CirculationInputChecker::getInstance()->getFilteredData();
            $id = $filteredData['Id'];

            if (isset($_GET['from']) && is_numeric($_GET['from']) && $now - ((float) $_GET['from'] <= Constant::MaxRecordsDateRange))
            {
                $from = (float) $_GET['from'];
            }
            else
            {
                $from = $now - Constant::MaxRecordsDateRange;
            }

            if (isset($_GET['to']) && is_numeric($_GET['to']) && $now - ((float) $_GET['to'] <= Constant::MaxRecordsDateRange))
            {
                $to = (float) $_GET['to'];
            }
            else
            {
                if (isset($from))
                {
                    $to = $from + Constant::MaxRecordsDateRange;
                }
                else
                {
                    $to = $now;
                }
            }

            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? (int) $_GET['p'] : Constant::DefaultPageNumber;
            $limit = (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] <= Constant::MaxDataCountPerPage) ? (int) $_GET['c'] : Constant::DefaultPageLimit;
            $offset = ($page - 1) * $limit;

            $output['Data'] = CirculationHandler::getInstance()->getRecords($id, $getByMember, [ $from, $to ], $limit, $offset);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => CirculationInputChecker::getInstance()->getErrorFields()
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
     * 依借閱者/會員編號查詢未歸還（借出中）書籍資料
     *
     * @param  string  $memberNo  借閱者編號
     * @return void
     */
    public function getBorrowingRecords(string $memberNo): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = [ 'MemberNo' => $memberNo ];

            CirculationInputChecker::getInstance()->verifyGetBorrowingRecords($input);
            $filteredData = CirculationInputChecker::getInstance()->getFilteredData();
            $memberNo = $filteredData['MemberNo'];

            $output['Data'] = CirculationHandler::getInstance()->getBorrowingRecordsByMember($memberNo);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => CirculationInputChecker::getInstance()->getErrorFields()
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
     * 查詢書籍當前流通狀態
     *
     * @param  integer|string  $bookId  書籍 ID
     * @return void
     */
    public function getBookStatus(mixed $bookId): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = [ 'BookId' => $bookId ];

            CirculationInputChecker::getInstance()->verifyGetStatus($input);
            $filteredData = CirculationInputChecker::getInstance()->getFilteredData();
            $bookId = $filteredData['Id'];

            $output['Data'] = CirculationHandler::getInstance()->getBookStatus($bookId);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => CirculationInputChecker::getInstance()->getErrorFields()
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
     * 借書
     *
     * @param  integer|string  $bookId    書籍 ID
     * @param  integer|string  $memberId  借閱者/會員 ID
     * @return void
     */
    public function borrow(mixed $bookId, mixed $memberId): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input = [
                'BookId' => $bookId,
                'MemberId' => $memberId
            ];

            CirculationInputChecker::getInstance()->verifyBorrow($input);
            $filteredData = CirculationInputChecker::getInstance()->getFilteredData();

            $result = CirculationHandler::getInstance()->borrowBook($filteredData);
            if ($result)
            {
                $output['Data'] = $result;
            }
            else
            {
                $httpStatusCode = 409;

                $exCode    = $output['Code']    = ExceptionCode::BookBorrowed;
                $exMessage = $output['Message'] = "Book has been borrowed";
                $output['Data'] = [
                    'BookId' => $bookId
                ];

                $logMessage = "{$this->_className}::{$functionName} Error({$exCode}): {$exMessage}";
                Logger::getInstance()->logError($logMessage);
            }
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => CirculationInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (CirculationException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'Id' => $ex->getPayload()['Id']
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} CirculationException({$exCode}): {$exMessage} {$jsonData}";
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
     * 還書
     *
     * @param  integer|string  $bookKey  書籍識別碼（ID 或書號）
     * @return void
     */
    public function return(mixed $bookKey): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            if (!isset($_GET['no']))
            {
                $input['BookId'] = $bookKey;
                $context = 'Id';
            }
            else
            {
                $input['BookNo'] = $bookKey;
                $context = 'No';
            }

            CirculationInputChecker::getInstance()->verifyReturn($input, $context);
            $filteredData = CirculationInputChecker::getInstance()->getFilteredData();

            $result = CirculationHandler::getInstance()->returnBook($filteredData, $context);
            if ($result)
            {
                $output['Data'] = $result;
            }
            else
            {
                $httpStatusCode = 409;

                $exCode    = $output['Code']    = ExceptionCode::BookNotBorrowed;
                $exMessage = $output['Message'] = "Book not borrowed";
                $output['Data'] = [];
                if ($context === 'Id')
                {
                    $output['Data']['BookId'] = $bookKey;
                }
                else
                {
                    $output['Data']['BookNo'] = $bookKey;
                }

                $logMessage = "{$this->_className}::{$functionName} Error({$exCode}): {$exMessage}";
                Logger::getInstance()->logError($logMessage);
            }
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => CirculationInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
        }
        catch (CirculationException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [];
            if ($context === 'Id')
            {
                $output['Data']['Id'] = $ex->getPayload()['Id'];
            }
            else
            {
                $output['Data']['No'] = $ex->getPayload()['No'];
            }

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} CirculationException({$exCode}): {$exMessage} {$jsonData}";
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
