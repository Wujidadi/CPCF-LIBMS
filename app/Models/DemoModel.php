<?php

namespace App\Models;

use App\Models;

/**
 * Parent class of model.
 */
class DemoModel extends Model
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

    public function demo()
    {
        return 'Connected to DB';
    }
}
