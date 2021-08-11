<?php

namespace App\Models;

use App\Model;

/**
 * Demo model.
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
