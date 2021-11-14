<?php

namespace Database;

use Exception;
use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;

/**
 * Parent class of migration.
 */
abstract class Migration
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
     * Run migration commands.
     *
     * @param  string    $className     Name of the migration class in which the _run method is called
     * @param  string    $functionName  Name of the migration function in which the _run method is called
     * @param  string[]  $queryArray    Array of SQL commands
     * @return boolean
     */
    protected function _run(string $className, string $functionName, array $queryArray): bool
    {
        try
        {
            $this->_db->beginTransaction();

            foreach ($queryArray as $sql)
            {
                $this->_db->query($sql);
            }
        }
        catch (PDOException $ex)
        {
            $this->_db->rollBack();

            $exCode = $ex->getCode();
            $exMsg  = $ex->getMessage();
            Logger::getInstance()->logError("{$className}::{$functionName} PDOException: ({$exCode}) {$exMsg}");

            throw new Exception($exMsg, 35);    // Sum of alphabet number of "PDO"
        }

        $commitResult = $this->_db->commit();

        return $commitResult;
    }
}
