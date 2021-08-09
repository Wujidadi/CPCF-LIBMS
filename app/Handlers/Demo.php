<?php

namespace App\Handlers;

/**
 * Demo handler.
 */
class Demo
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
     * Get welcome message.
     *
     * @param  string  $message  String to form the welcom message
     * @return string
     */
    public function welcome($message)
    {
        echo 'Welcome to ' . $message;
    }
}
