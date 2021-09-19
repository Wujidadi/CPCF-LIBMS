<?php

namespace App\Controllers;

use App\Constant;

/**
 * 首頁控制器
 */
class HomeController
{
    protected $_className;

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
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
