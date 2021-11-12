<?php

namespace App\Controllers;

use PDO;
use Libraries\HTTP\Response;
use Libraries\DBAPI;

class TestController
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
        $result = [
            'Text' => $text = 'Input',
            'Sum'  => SumWord($text)
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
                PDO::PARAM_INT
            ]
        ];

        $result = DBAPI::getInstance()->query($sql, $bind);

        Response::getInstance()->setCode(200)->output(JsonUnescaped($result));
    }
}
