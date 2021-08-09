<?php

namespace App\Controllers;

use App\Handlers\Demo;
use Libraries\Logger;

/**
 * Demo controller.
 */
class DemoController
{
    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /**
     * Constructor.
     *
     * @return void
     */
    protected function __construct() {}

    /**
     * Get the instance of this class.
     * 
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    /**
     * Main demo method.
     *
     * @return void
     */
    public function main()
    {
        echo Demo::getInstance()->welcome('Tarascanta Welcome Page');
    }

    /**
     * Main demo API method.
     *
     * @return void
     */
    public function api()
    {
        header('Content-Type: text/plain');

        echo 'API';
    }
}
