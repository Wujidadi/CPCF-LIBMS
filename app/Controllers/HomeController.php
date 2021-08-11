<?php

namespace App\Controllers;

use App\Constant;

class HomeController
{
    protected static $_uniqueInstance = null;

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

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
