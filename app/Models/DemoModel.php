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

    /**
     * Constructor.
     *
     * Can be commented if the used DB configurations are the same as parent `Model` class (`DEFAULT` by default).
     */
    protected function __construct()
    {
        parent::__construct('DEFAULT');
    }

    public function demo()
    {
        return 'Connected to DB';
    }
}
