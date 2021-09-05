<?php

namespace App\Controllers;

use App\Constant;

/**
 * 首頁控制器
 */
class HomeController
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
     * 顯示首頁
     *
     * @return void
     */
    public function main()
    {
        $systemName = Constant::SystemName;
        $customerFullName = Constant::CustomerFullName;

        $pageTitle = '首頁';
        $headerTitle = "{$customerFullName} {$systemName}";

        view('Home.Main', compact(
            'pageTitle',
            'headerTitle'
        ));
    }
}
