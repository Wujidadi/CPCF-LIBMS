<?php

namespace App;

use Libraries\DBAPI;

/**
 * Parent class of model.
 */
abstract class Model
{
    protected $_db = null;

    abstract public static function getInstance();

    /**
     * Constructor.
     *
     * @param  string  $dbConfigKey  Key of the database configurations in `DB_CONFIG` array which shall be use.
     */
    protected function __construct($dbConfigKey = 'DEFAULT')
    {
        $this->_db = DBAPI::getInstance($dbConfigKey);
    }

    public function beginTransaction()
    {
        return !$this->_db->inTransaction() ? $this->_db->beginTransaction() : false;
    }

    public function commit()
    {
        return $this->_db->commit();
    }

    public function rollBack()
    {
        return $this->_db->rollBack();
    }

    public function inTransaction()
    {
        return $this->_db->inTransaction();
    }
}
