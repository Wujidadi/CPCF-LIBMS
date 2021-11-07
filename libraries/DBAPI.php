<?php

namespace Libraries;

use PDO;
use PDOStatement;
use PDOException;
use Libraries\Logger;

/**
 * Database API and handling class.
 */
class DBAPI
{
    /**
     * @var PDO|null
     */
    private $_pdo;

    /**
     * @var PDOStatement|false
     */
    private $_pdoStatement;

    private $_dbtype;
    private $_host;
    private $_port;
    private $_dbname;
    private $_username;
    private $_password;

    private $_parameters;
    private $_connectionStatus = false;
    public $_queryCount = 0;

    const AUTO_RECONNECT = true;
    const MAX_RETRY = 3;
    private $_retryAttempt = 0;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /**
     * Get the instance of this class.
     *
     * @param  string  $configKey  Key of the database configurations array.
     * @return self
     */
    public static function getInstance($configKey = 'DEFAULT')
    {
        if (self::$_uniqueInstance === null)
        {
            self::$_uniqueInstance = new self(
                DB_CONFIG[$configKey]['TYPE'],
                DB_CONFIG[$configKey]['HOST'],
                DB_CONFIG[$configKey]['PORT'],
                DB_CONFIG[$configKey]['DATABASE'],
                DB_CONFIG[$configKey]['USERNAME'],
                DB_CONFIG[$configKey]['PASSWORD']
            );
        }
        return self::$_uniqueInstance;
    }

    /**
     * Constructor.
     *
     * @param  string          $dbtype    Database type
     * @param  string          $host      Database host
     * @param  integer|string  $port      Database port
     * @param  string          $dbname    Database name
     * @param  string          $username  Database username
     * @param  string          $password  Database password
     * @return void
     */
    public function __construct($dbtype, $host, $port, $dbname, $username, $password)
    {
        $this->_dbtype   = $dbtype;
        $this->_host     = $host;
        $this->_port     = $port;
        $this->_dbname   = $dbname;
        $this->_username = $username;
        $this->_password = $password;

        $this->_parameters = [];

        $this->_connect();
    }

    /**
     * Connect to database.
     *
     * @return void
     */
    private function _connect()
    {
        try
        {
            $dsn = "{$this->_dbtype}:host={$this->_host};port={$this->_port};dbname={$this->_dbname}";

            $options = [
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            $this->_pdo = new PDO(
                $dsn,
                $this->_username,
                $this->_password,
                $options
            );

            $this->_connectionStatus = true;
        }
        catch (PDOException $e)
        {
            $this->_exceptionLog($e, '', __FUNCTION__);
        }
    }

    /**
     * Initiate a DB transaction.
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        return $this->_pdo->beginTransaction();
    }

    /**
     * Commit a DB transaction.
     *
     * @return boolean
     */
    public function commit()
    {
        return $this->_pdo->commit();
    }

    /**
     * Roll back a DB transaction.
     *
     * @return boolean
     */
    public function rollBack()
    {
        return $this->_pdo->rollBack();
    }

    /**
     * Check if inside a transaction.
     *
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->_pdo->inTransaction();
    }

    /**
     * Close PDO connection.
     *
     * @return void
     */
    public function close()
    {
        $this->_pdo = null;
    }

    /**
     * Set DB failure flag.
     *
     * @return void
     */
    private function _setFailureFlag()
    {
        $this->_pdo = null;
        $this->_connectionStatus = false;
    }

    /**
     * Initiate the query and execute it.
     *
     * @param  string      $query          SQL clause
     * @param  array|null  $parameters     Query parameters
     * @param  array       $driverOptions  SQL Driver options
     * @return boolean|null
     */
    private function _init($query, $parameters = null, $driverOptions = [])
    {
        $execResult = null;

        if (!$this->_connectionStatus)
        {
            $this->_connect();
        }

        try
        {
            $this->_parameters = $parameters;
            $this->_pdoStatement = $this->_pdo->prepare($this->_buildParams($query, $this->_parameters), $driverOptions);

            if (!empty($this->_parameters))
            {
                if (array_key_exists(0, $parameters))
                {
                    $parametersType = true;
                    array_unshift($this->_parameters, '');
                    unset($this->_parameters[0]);
                }
                else
                {
                    $parametersType = false;
                }

                foreach ($this->_parameters as $column => $value)
                {
                    $this->_pdoStatement->bindParam($parametersType ? intval($column) : ":" . $column, $this->_parameters[$column]);
                    // It would be queried after loop end (before '_pdoStatement->execute()'). It is wrong to use $value.
                }
            }

            if (!isset($driverOptions[PDO::ATTR_CURSOR]))
            {
                $execResult = $this->_pdoStatement->execute();
            }
            $this->_queryCount++;
        }
        catch (PDOException $e)
        {
            $this->_exceptionLog($e, $this->_buildParams($query), __FUNCTION__, ['query' => $query, 'parameters' => $parameters]);
        }

        $this->_parameters = [];

        return $execResult;
    }

    /**
     * Build SQL parameters.
     *
     * @param  string      $query   SQL clause
     * @param  array|null  $params  Binding Variables
     * @return string
     */
    private function _buildParams($query, $params = null)
    {
        if (!empty($params))
        {
            $arrayParameterFound = false;

            foreach ($params as $paramKey => $parameter)
            {
                if (is_array($parameter))
                {
                    $arrayParameterFound = true;

                    $in = '';

                    foreach ($parameter as $key => $value)
                    {
                        $namePlaceholder = "{$paramKey}_{$key}";

                        $in .= ":{$namePlaceholder}, ";         // Concatenates params as named placeholders

                        $params[$namePlaceholder] = $value;     // Adds each single parameter to $params
                    }

                    $in = rtrim($in, ', ');

                    $query = preg_replace("/:{$paramKey}/", $in, $query);

                    // Removes array form $params
                    unset($params[$paramKey]);
                }
            }

            // Updates $this->_parameters if $params and $query have changed
            if ($arrayParameterFound) $this->parameters = $params;
        }

        return $query;
    }

    /**
     * Execute a SQL query, return a result array in the select operation, or return the number of rows affected in other operations.
     *
     * @param  string      $query      SQL clause
     * @param  array|null  $params     Query parameters
     * @param  integer     $fetchMode  Fetch mode
     * @return array|integer|boolean|null
     */
    public function query($query, $params = null, $fetchMode = PDO::FETCH_ASSOC)
    {
        $query        = trim($query);
        $rawStatement = preg_split("/( |\r|\n)/", $query);
        $statement    = strtolower($rawStatement[0]);

        $exec = $this->_init($query, $params);

        if (in_array($statement, ['select', 'show', 'call', 'describe']))
        {
            return $this->_pdoStatement->fetchAll($fetchMode);
        }
        else if (in_array($statement, ['insert', 'update', 'delete']))
        {
            return $this->_pdoStatement->rowCount();
        }
        else
        {
            return $exec;
        }
    }

    /**
     * Log exception and retry.
     *
     * @param  PDOException  $ex          Error raised by PDO
     * @param  string        $sql         SQL clause
     * @param  string        $method      The Function in which the error has been raised
     * @param  array         $parameters  Query parameters
     * @return void
     */
    private function _exceptionLog($ex, $sql = '', $method = '', $parameters = [])
    {
        $message = $ex->getMessage();
        $exception = "Unhandled exception.<br />{$message}<br />You can find the error back in the log.";

        if (!empty($sql))
        {
            $message .= "\nRaw SQL: {$sql}";
        }
        Logger::getInstance()->logError($message);

        if (self::AUTO_RECONNECT &&
            $this->_retryAttempt < self::MAX_RETRY &&
            stripos($message, 'server has gone away') !== false &&
            !empty($method) &&
            !$this->inTransaction())
        {
            $this->_setFailureFlag();
            $this->retryAttempt++;
            Logger::getInstance()->logError("Retry {$this->retryAttempt} times");
            call_user_func_array([$this, $method], $parameters);
        }
        else
        {
            throw $ex;
        }
    }
}
