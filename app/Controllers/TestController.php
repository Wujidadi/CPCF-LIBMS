<?php

namespace App\Controllers;

use App\Controller;
use App\Handlers\StorageTypeHandler;
use Libraries\Logger;
use Libraries\HTTP\Request;
use Libraries\HTTP\Response;
use Libraries\Database\DBAPI;
use App\Models\BookModel;
use App\Models\MemberModel;
use App\Models\StorageTypeModel;

class TestController extends Controller
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
     * 主要測試方法
     */
    public function main()
    {
        $functionName = __FUNCTION__;

        $httpStatusCode = 200;
        $output = [
            'Code'    => 200,
            'Message' => 'OK'
        ];

        try
        {
            $result = [
                // 'Text'  => $text = 'MemberNotExist',
                // 'Sum'   => SumWord($text),
                // 'Count' => MemberModel::getInstance()->countById(3),
                // 'No'    => $_GET['no'] ?? 'Number',
                // 'Data'  => DBAPI::getInstance()->query('SELECT * FROM "public"."StorageTypes"')
                // 'Data'  => DBAPI::getInstance()->lastInsertId('StorageTypes_Id_seq')
                // 'Data'  => DBAPI::getInstance()->insert('StorageTypes', [
                //     'Name' => '測試',
                //     'Alias' => 'Test'
                // ])
                // 'Data'  => DBAPI::getInstance()->insertMulti('StorageTypes', [
                //     [
                //         'Name' => '測試1',
                //         'Alias' => 'Test1'
                //     ],
                //     [
                //         'Name' => '測試2',
                //         'Alias' => 'Test2'
                //     ]
                // ]),
                // 'Data'  => DBAPI::getInstance()->count('StorageTypes')
                // 'Data'  => DBAPI::getInstance()->select('StorageTypes', [
                //     'Id' => '唯一碼',
                //     'Name',
                //     'Alias' => '別名'
                // ], [
                //     'Name' => ['LIKE', '%測試%'],
                //     // 'Alias' => 'Test'
                // ])
                // 'Data'  => DBAPI::getInstance()->update('StorageTypes', [
                //     'Alias' => 'TTTEEST'
                // ], [
                //     // 'Name' => '測試2'
                //     'Name' => ['LIKE', '%測試%']
                // ])
                // 'Data'  => DBAPI::getInstance()->delete('StorageTypes', [
                //     'Name' => [ 'LIKE', '%測試%' ]
                // ]),
                'Data'  => StorageTypeHandler::getInstance()->getAll()
            ];
            $output['Data'] = $result;
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

        if (!is_string($output))
        {
            $output = JsonUnescaped($output);
        }

        Response::getInstance()->setCode($httpStatusCode)->output($output);
    }

    public function sumWord()
    {
        $input = Request::getInstance()->getData();

        $text = $input['text'] ?? '';

        $result = [
            'Text' => $text,
            'Sum' => SumWord($text)
        ];

        Response::getInstance()->setCode(200)->output(JsonUnescaped($result));
    }

    /**
     * DBAPI 巢狀參數使用方法示例
     *
     * @return void
     */
    public function DbQueryWithNestedParam(): void
    {
        $sql = <<<SQL
        SELECT
            *
        FROM public."Books"
        WHERE
            "Id" < :id AND
            "Author" IN :author
        LIMIT :limit
        SQL;

        $bind = [
            'id' => 100,
            'author' => [
                [
                    '畢克馬',
                    '提利‧勒南',
                    '艾拉克‧巴圖'
                ]
            ],
            'limit' => [
                '4',
                \PDO::PARAM_INT
            ]
        ];

        $result = DBAPI::getInstance()->query($sql, $bind);

        Response::getInstance()->setCode(200)->output(JsonUnescaped($result));
    }
}
