<?php

namespace Libraries\Database;

use Libraries\Logger;

/**
 * SQLite API and handling class.
 *
 * This API is not a singleton, for it may be called in one process to connect to different DB file.
 */
class SQLiteAPI
{
    /**
     * PDO object.
     *
     * @var \PDO|null
     */
    private $_pdo;

    /**
     * Path and name of the file of the SQLite database.
     *
     * @var string
     */
    protected $_dbName;

    /**
     * Name of the class.
     *
     * @var string
     */
    protected $_className;

    /**
     * Constructor.
     *
     * @param  string  $db  Path and name of the file of the SQLite database.
     * @return void
     */
    public function __construct(string $db)
    {
        $this->_className = basename(__FILE__, '.php');
        $this->_dbName = $db;
        $this->_connect();
    }

    /**
     * Connect to the database.
     *
     * @return void
     */
    private function _connect()
    {
        $functionName = __FUNCTION__;

        try
        {
            $dsn = "sqlite:{$this->_dbName}";
            $this->_pdo = new \PDO($dsn);
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->_pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
        }
        catch (\Throwable $ex)
        {
            $exType = get_class($ex);
            $exCode = $ex->getCode();
            $exMsg = $ex->getMessage();

            $logMsg = "{$this->_className}::{$functionName} {$exType} ({$exCode}) {$exMsg}";
            Logger::getInstance()->logError($logMsg);
        }
    }

    /**
     * Initiate a DB transaction.
     *
     * @return boolean
     */
    public function beginTransaction(): bool
    {
        return $this->_pdo->beginTransaction();
    }

    /**
     * Commit a DB transaction.
     *
     * @return boolean
     */
    public function commit(): bool
    {
        return $this->_pdo->commit();
    }

    /**
     * Roll back a DB transaction.
     *
     * @return boolean
     */
    public function rollBack(): bool
    {
        return $this->_pdo->rollBack();
    }

    /**
     * Check if inside a transaction.
     *
     * @return boolean
     */
    public function inTransaction(): bool
    {
        return $this->_pdo->inTransaction();
    }

    /**
     * Get the PDO instance.
     *
     * Can be called in a emergency when the needs are unmet after calling current methods of this class.
     *
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        return $this->_pdo;
    }

    /**
     * Execute a SQL query.
     *
     * @param  string  $sql   SQL clause.
     * @param  array   $bind  Query parameters, can be an array of one or two dimension.  
     *                        One-dimensional: paramters will be bound as the default type `PDO::PARAM_STR`.  
     *                        Two-dimensional: `[0]` should be the value, and `[1]` should bet the type to bound as.
     * @return array|integer
     */
    public function query(string $sql, array $bind = [])
    {
        $query = $this->_pdo->prepare($sql);

        foreach ($bind as $key => $value)
        {
            if (!is_array($value))
            {
                $query->bindParam($key, $bind[$key]);
            }
            else
            {
                $query->bindParam($key, $bind[$key][0], $bind[$key][1]);
            }
        }

        $query->execute();

        if ($query->columnCount())
        {
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $query->rowCount();
    }
}
