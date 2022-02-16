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
    public static function getInstance(string $configKey = 'DEFAULT'): self
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
    public function __construct(string $dbtype, string $host, mixed $port, string $dbname, string $username, string $password)
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
    private function _connect(): void
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
     * Close PDO connection.
     *
     * @return void
     */
    public function close(): void
    {
        $this->_pdo = null;
    }

    /**
     * Set DB failure flag.
     *
     * @return void
     */
    private function _setFailureFlag(): void
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
    private function _init(string $query, ?array $parameters = null, array $driverOptions = []): ?bool
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
                    $this->_pdoStatement->bindParam(
                        $parametersType ? intval($column) : ':' . $column,
                        $this->_parameters[$column]['value'],
                        $this->_parameters[$column]['type']
                    );
                }
            }

            if (!isset($driverOptions[PDO::ATTR_CURSOR]))
            {
                $execResult = $this->_pdoStatement->execute();
            }
            $this->_queryCount++;
        }
        catch (PDOException $ex)
        {
            $this->_exceptionLog($ex, $this->_buildParams($query), __FUNCTION__, ['query' => $query, 'parameters' => $parameters]);
        }

        $this->_parameters = [];

        return $execResult;
    }

    /**
     * Build SQL parameters.
     *
     * @param  string      $query   SQL clause
     * @param  array|null  $params  Binding variables
     * @return string
     */
    private function _buildParams(string $query, ?array $params = null): string
    {
        if (!empty($params))
        {
            foreach ($params as $paramKey => $parameter)
            {
                unset($params[$paramKey]);

                if (is_array($parameter))
                {
                    if (is_array($parameter[0]))
                    {
                        $in = '';

                        foreach ($parameter[0] as $key => $value)
                        {
                            $namePlaceholder = "{$paramKey}_{$key}";

                            $in .= ":{$namePlaceholder}, ";                 // Concatenates params as named placeholders

                            $params[$namePlaceholder]['value'] = $value;    // Adds each single parameter to $params

                            $params[$namePlaceholder]['type'] = (isset($parameter[1]) && is_int($parameter[1])) ? $parameter[1] : PDO::PARAM_STR;
                        }

                        $in = '(' . rtrim($in, ', ') . ')';

                        $query = preg_replace("/:{$paramKey}/", $in, $query);
                    }
                    else
                    {
                        $params[$paramKey]['value'] = $parameter[0];
                        $params[$paramKey]['type']  = (isset($parameter[1]) && is_int($parameter[1])) ? $parameter[1] : PDO::PARAM_STR;
                    }
                }
                else
                {
                    $params[$paramKey]['value'] = $parameter;
                    $params[$paramKey]['type']  = PDO::PARAM_STR;
                }
            }

            $this->_parameters = $params;
        }

        return $query;
    }

    /**
     * Build SQL clauses of `SELECT` columns by given column names.
     *
     * @param  string[]  $columns  Column names that should be selected
     * @return string
     */
    private function _buildSelectColumn(array $columns): string
    {
        if (is_array($columns) && count($columns) > 0)
        {
            $fields = '';

            foreach ($columns as $key => $value)
            {
                if (is_numeric($key) && preg_match('/\d+/', $key))
                {
                    $fields .= "`{$value}`, ";
                }
                else
                {
                    $fields .= "`{$key}` AS `{$value}`, ";
                }
            }
            $fields .= preg_replace('/, $/', '', $fields);

            if ($this->_dbtype !== 'mysql')
            {
                $fields = $this->_changeSystemIdentifiers($fields);
            }
        }
        else
        {
            $fields = '*';
        }

        return $fields;
    }

    /**
     * Build SQL clause and binding parameters by given data for update.
     *
     * @param  array  $params  Column names and values to be updated
     * @return array
     */
    private function _buildUpdateValues(array $params): array
    {
        $bind = [];
        $values = '';

        foreach ($params as $key => $val)
        {
            $bind["{$key}_to_update"] = $val;
            $values .= "`{$key}` = :{$key}_to_update, ";
        }

        $values = preg_replace('/, $/', '', $values);

        if ($this->_dbtype !== 'mysql')
        {
            $values = $this->_changeSystemIdentifiers($values);
        }

        return [
            'Pattern' => $values,
            'Bind' => $bind
        ];
    }

    /**
     * Build SQL clauses of `WHERE` conditions and binding parameters by given data.
     *
     * `OR` clauses in `WHERE` data are not supported yet
     *
     * @param  array  $params  `WHERE` columns and values
     * @return array
     */
    private function _buildWhere(array $params): array
    {
        $singleValueOperators = [ '=', '!=', '>', '<', '<>', '<=>', 'is', 'is not', 'like', 'not like' ];
        $betweenOperators = [ 'between', 'not between' ];
        $inOperators = [ 'in', 'not in' ];

        $bind = [];
        $wherePattern = '';

        if ((is_array($params) && count($params) > 0))
        {
            foreach ($params as $key => $value)
            {
                # Handle "equal" statement
                if (!is_array($value))
                {
                    $wherePattern .= "`{$key}` = :{$key} AND ";
                    $bind[$key] = $value;
                }
                else
                {
                    $operator = trim(strtolower($value[0]));

                    # Handle "equal", "compare", "IS/IS NOT" and "LIKE/NOT LIKE" statement
                    if (in_array($operator, $singleValueOperators) && !is_array($value[1]))
                    {
                        $wherePattern .= "`{$key}` {$value[0]} :{$key} AND ";
                        $bind[$key] = $value[1];
                    }
                    else if (is_array($value[1]))
                    {
                        # Handle "BETWEEN" statement
                        if (in_array($operator, $betweenOperators))
                        {
                            $wherePattern .= "`{$key}` {$value[0]} :{$key}_FROM AND :{$key}_TO AND ";
                            $bind["{$key}_FROM"] = $value[1][0];
                            $bind["{$key}_TO"] = $value[1][1];
                        }
                        # Handle "IN" statement
                        else if (in_array($operator, $inOperators))
                        {
                            $wherePattern .= "`{$key}` {$value[0]} (";
                            foreach ($value[1] as $inIdx => $inVal)
                            {
                                $wherePattern .= ":{$key}{$inIdx}, ";
                                $bind["{$key}{$inIdx}"] = $inVal;
                            }
                            $wherePattern = preg_replace('/, $/', '', $wherePattern) . ') AND ';
                        }
                    }
                }
            }

            $wherePattern = ' WHERE ' . preg_replace('/ AND $/', '', $wherePattern);
        }

        if ($this->_dbtype !== 'mysql')
        {
            $wherePattern = $this->_changeSystemIdentifiers($wherePattern);
        }

        return [
            'Pattern' => $wherePattern,
            'Bind' => $bind
        ];
    }

    /**
     * Execute a SQL query, return a result array in the select operation, or return the number of rows affected in other operations.
     *
     * @param  string      $query      SQL clause
     * @param  array|null  $params     Query parameters
     * @param  integer     $fetchMode  Fetch mode
     * @return array|integer|boolean|null
     */
    public function query(string $query, ?array $params = null, int $fetchMode = PDO::FETCH_ASSOC): mixed
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
     * Select the result of `COUNT()` by given table name and `WHERE` array; `OR` clauses in `WHERE` data are not supported yet.
     *
     * @param  string  $tableName  Name of the target table
     * @param  array   $where      Data of `WHERE` conditions
     * @return integer
     */
    public function count(string $tableName, array $where = []): int
    {
        list('Pattern' => $wherePattern, 'Bind' => $bind) = $this->_buildWhere($where);

        $as = '`Count`';

        $table = "`{$tableName}`";

        if ($this->_dbtype !== 'mysql')
        {
            $as = $this->_changeSystemIdentifiers($as);
            $table = $this->_changeSystemIdentifiers($table);
        }

        $sql = "SELECT COUNT(*) AS {$as} FROM {$table}{$wherePattern}";

        return (int) $this->query($sql, $bind)[0]['Count'];
    }

    /**
     * Select by given table name, column names and `WHERE` array; `OR` clauses in `WHERE` data are not supported yet.
     *
     * @param  string    $tableName  Name of the target table
     * @param  string[]  $columns    Column names that should be selected
     * @param  array     $where      Data of `WHERE` conditions
     * @return array
     */
    public function select(string $tableName, array $columns = [], array $where = []): array
    {
        $selectPattern = $this->_buildSelectColumn($columns);

        list('Pattern' => $wherePattern, 'Bind' => $bind) = $this->_buildWhere($where);

        $table = "`{$tableName}`";

        if ($this->_dbtype !== 'mysql')
        {
            $table = $this->_changeSystemIdentifiers($table);
        }

        $sql = "SELECT {$selectPattern} FROM {$table}{$wherePattern}";

        return $this->query($sql, $bind);
    }

    /**
     * Insert data by given table name and parameters.
     *
     * @param  string    $tableName  Name of the target table
     * @param  string[]  $params     Binding variables
     * @return integer
     */
    public function insert(string $tableName, array $params = []): int
    {
        $keys = array_keys($params);

        $columns = '(`' . implode('`, `', $keys) . '`)';

        $values = '(:' . implode(', :', $keys) . ')';

        $table = "`{$tableName}`";

        if ($this->_dbtype !== 'mysql')
        {
            $columns = $this->_changeSystemIdentifiers($columns);
            $table = $this->_changeSystemIdentifiers($table);
        }

        $sql = "INSERT INTO {$table} {$columns} VALUES {$values}";

        return $this->query($sql, $params);
    }

    /**
     * Insert multiple rows of data by given table name and parameters.
     *
     * @param  string  $tableName  Name of the target table
     * @param  array   $params     Binding variables in two-dimensional array with indexless first dimension
     * @return boolean             Success or not
     */
    public function insertMulti(string $tableName, array $params = []): bool
    {
        $rowCount = 0;

        if (!empty($params))
        {
            $values = '';
            $bind = [];

            $columns = '(`' . implode('`, `', array_keys($params[0])) . '`)';

            foreach ($params as $addRow)
            {
                $values .= '(' . implode(', ', array_fill(0, count($addRow), '?')) . '), ';
                $bind = array_merge($bind, array_values($addRow));
            }
            $values = preg_replace('/, $/', '', $values);

            $table = "`{$tableName}`";

            if ($this->_dbtype !== 'mysql')
            {
                $columns = $this->_changeSystemIdentifiers($columns);
                $table = $this->_changeSystemIdentifiers($table);
            }

            $sql = "INSERT INTO {$table} {$columns} VALUES {$values}";

            $rowCount = $this->query($sql, $bind);
        }

        return (bool) ($rowCount > 0);
    }

    /**
     * Update data by by given table name and parameters.
     *
     * @param  string    $tableName  Name of the target table
     * @param  string[]  $params     Binding variables
     * @param  array     $where      Data of `WHERE` conditions
     * @return integer               Count of affected rows
     */
    public function update(string $tableName, array $params = [], array $where = []): int
    {
        $rowCount = 0;

        if (!empty($params))
        {
            list('Pattern' => $values, 'Bind' => $updateParam) = $this->_buildUpdateValues($params);
            list('Pattern' => $wherePattern, 'Bind' => $whereParam) = $this->_buildWhere($where);
            $bind = array_merge($updateParam, $whereParam);

            $table = "`{$tableName}`";

            if ($this->_dbtype !== 'mysql')
            {
                $table = $this->_changeSystemIdentifiers($table);
            }

            $sql = "UPDATE {$table} SET {$values}{$wherePattern}";

            $rowCount = $this->query($sql, $bind);
        }

        return $rowCount;
    }

    /**
     * Delete data by by given table name and parameters.
     *
     * @param  string  $tableName  Name of the target table
     * @param  array   $where      Data of `WHERE` conditions
     * @return integer
     */
    public function delete(string $tableName, array $where = []): int
    {
        $rowCount = 0;

        # To avoid accidentally delete all data, the workflow only continues while `$where` is not empty
        if (!empty($where))
        {
            list('Pattern' => $wherePattern, 'Bind' => $bind) = $this->_buildWhere($where);

            $table = "`{$tableName}`";

            if ($this->_dbtype !== 'mysql')
            {
                $table = $this->_changeSystemIdentifiers($table);
            }

            $sql = "DELETE FROM {$table}{$wherePattern}";

            $rowCount = $this->query($sql, $bind);
        }

        return $rowCount;
    }

    /**
     * Get the ID or sequence value of the last inserted row.
     *
     * @param  string|null   $name  Name of the sequence object (mainly for DB types besides MySQL, ex: PostgreSQL)
     * @return string|false
     */
    public function lastInsertId(?string $name = null): string|false
    {
        return $this->_pdo->lastInsertId($name);
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
    private function _exceptionLog(PDOException $ex, string $sql = '', string $method = '', array $parameters = []): void
    {
        $message = $ex->getMessage();

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

    /**
     * Convert system identifiers between DB types.
     *
     * @param  string  $text    Clause or text that includes system identifiers
     * @param  string  $dbType  Type of the database
     * @return string
     */
    private function _changeSystemIdentifiers(string $text, string $dbType = null): string
    {
        if (is_null($dbType))
        {
            $dbType = $this->_dbtype;
        }

        switch ($dbType)
        {
            case 'pgsql':
                return preg_replace('/`/', '"', $text);

            default:
                return $text;
        }
    }
}
