<?php

namespace App\Models;

use Libraries\DBAPI;

/**
 * Parent class of model.
 */
abstract class Model
{
    protected $_db = null;

    abstract public static function getInstance();

    protected function __construct()
    {
        $this->_db = DBAPI::getInstance();
    }

    public function beginTransaction()
    {
        return $this->_db->beginTransaction();
    }

    public function commit()
    {
        return $this->_db->commit();
    }

    public function rollBack()
    {
        return $this->_db->rollBack();
    }
}
