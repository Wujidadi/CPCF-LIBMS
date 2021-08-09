<?php

namespace Database;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;

abstract class Migration
{
    protected $_db = null;

    abstract public static function getInstance();

    protected function __construct()
    {
        $this->_db = DBAPI::getInstance();
    }

    /**
     * Run migration commands.
     *
     * @param  string    $tableName     Table name
     * @param  string    $functionName  Name of the migration function which calls the _run method
     * @param  string[]  $queryArray    Array of SQL commands
     * @return boolean
     */
    protected function _run($tableName, $functionName, $queryArray)
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
            Logger::getInstance()->logError("{$tableName}::{$functionName} PDOException: ({$exCode}) {$exMsg}");

            return false;
        }

        $commitResult = $this->_db->commit();

        return $commitResult;
    }
}
