<?php

namespace App\Controllers\API;

use Throwable;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Logger;
use App\Constant;
use App\Handlers\MemberHandler;
use App\Validators\InputCheckers\MemberInputChecker;
use App\Exceptions\InputException;

/**
 * 借閱者/會員資料控制器
 */
class MemberController
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
     * @return void
     */
    public function addMember(): void
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

            MemberInputChecker::getInstance()->verifyAdd($input);
            $filteredData = MemberInputChecker::getInstance()->getFilteredData();

            $result = MemberHandler::getInstance()->addMember($filteredData);

            $output['Data'] = $result;
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => MemberInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
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
     * 指定欄位及關鍵字查詢借閱者/會員資料
     *
     * @param  string          $field  欄位
     * @param  string|integer  $value  關鍵字
     * @return void
     */
    public function getMembers(string $field, mixed $value): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        $includeDisabled = false;

        try
        {
            $input = [ 'Field' => $field, 'Value' => $value ];

            MemberInputChecker::getInstance()->verifyGet($input);
            $filteredData = MemberInputChecker::getInstance()->getFilteredData();
            $field = $filteredData['Field'];
            $value = $filteredData['Value'];

            if (isset($_GET['d']) && $_GET['d'] == '1')
            {
                $includeDisabled = true;
            }

            $page = (isset($_GET['p']) && is_numeric($_GET['p'])) ? (int) $_GET['p'] : Constant::DefaultPageNumber;
            $limit = (isset($_GET['c']) && is_numeric($_GET['c']) && $_GET['c'] <= Constant::MaxDataCountPerPage) ? (int) $_GET['c'] : Constant::DefaultPageLimit;
            $offset = ($page - 1) * $limit;

            $output['Data'] = MemberHandler::getInstance()->getMembers($field, $value, $limit, $offset, $includeDisabled);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => MemberInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
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
     * 修改借閱者/會員資料
     *
     * @param  integer|string  $memberId  借閱者/會員 ID
     * @return void
     */
    public function editMember(mixed $memberId): void
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

            MemberInputChecker::getInstance()->verifyEdit($input);
            $filteredData = MemberInputChecker::getInstance()->getFilteredData();

            $output['Data'] = MemberHandler::getInstance()->editMember($memberId, $filteredData);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => MemberInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
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
     * 禁用或啟用指定 ID 的借閱者/會員資料
     *
     * @param  integer|string  $memberId  借閱者/會員 ID
     * @param  boolean         $action    禁用（`true`）或啟用（`false`）
     * @return void
     */
    public function disableMember(mixed $memberId, bool $action = true): void
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $input['Id'] = $memberId;

            MemberInputChecker::getInstance()->verifyDisable($input);
            $filteredData = MemberInputChecker::getInstance()->getFilteredData();
            $memberId = $filteredData['Id'];

            $output['Data'] = MemberHandler::getInstance()->disableMember($memberId, $action);
        }
        catch (InputException $ex)
        {
            $httpStatusCode = 400;

            $exCode    = $output['Code']    = $ex->getCode();
            $exMessage = $output['Message'] = $ex->getMessage();
            $output['Data'] = [
                'ErrorField' => MemberInputChecker::getInstance()->getErrorFields()
            ];

            $jsonData = JsonUnescaped($output['Data']);
            $logMessage = "{$this->_className}::{$functionName} InputException({$exCode}): {$exMessage} {$jsonData}";
            Logger::getInstance()->logError($logMessage);
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
