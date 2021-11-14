<?php

namespace App;

use Libraries\DBAPI;

/**
 * Parent class of model.
 */
abstract class Model
{
    /**
     * Name of this class.
     *
     * @var string
     */
    protected $_className;

    /**
     * Instance of Database connection.
     *
     * @var Libraries\DBAPI
     */
    protected $_db;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance;

    /**
     * Get the instance of this class.
     *
     * @return self
     */
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

    /**
     * Begin transaction.
     *
     * @return boolean
     */
    public function beginTransaction(): bool
    {
        return !$this->_db->inTransaction() ? $this->_db->beginTransaction() : false;
    }

    /**
     * Commit a DB transaction.
     *
     * @return boolean
     */
    public function commit(): bool
    {
        return $this->_db->commit();
    }

    /**
     * Roll back a DB transaction.
     *
     * @return boolean
     */
    public function rollBack(): bool
    {
        return $this->_db->rollBack();
    }

    /**
     * Check if inside a transaction.
     *
     * @return boolean
     */
    public function inTransaction(): bool
    {
        return $this->_db->inTransaction();
    }
}
