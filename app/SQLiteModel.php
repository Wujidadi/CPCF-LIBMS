<?php

namespace App;

use Libraries\Database\SQLiteAPI;

/**
 * Parent class of SQLite models.
 */
abstract class SQLiteModel
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
     * @var SQLiteAPI
     */
    protected $_db;

    /**
     * Path and name of the file of the SQLite database.
     *
     * @var string
     */
    protected $_dbFile;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_instance;

    /**
     * Get the instance of this class.
     *
     * @return self
     */
    abstract public static function getInstance();

    /**
     * Constructor.
     *
     * @param  string  $db  Database name
     * @return void
     */
    public function __construct(string $db)
    {
        $this->_dbFile = STORAGE_DIR . "/sqlite/{$db}.db";
        $this->_db = new SQLiteAPI($this->_dbFile);
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
