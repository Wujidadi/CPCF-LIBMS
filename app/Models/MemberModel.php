<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use Libraries\Logger;
use App\ExceptionCode;
use App\Model;

/**
 * 借閱者/會員資料表模型
 */
class MemberModel extends Model
{
    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $_tableName = 'Members';

    /**
     * 資料表欄位屬性表
     *
     * @var array
     */
    protected $_columns = [
        'No'         => [ 'required' => true,  'editable' => false ],
        'Name'       => [ 'required' => true,  'editable' => true  ],
        'Email'      => [ 'required' => false, 'editable' => true  ],
        'Gender'     => [ 'required' => false, 'editable' => true  ],
        'Birthday'   => [ 'required' => false, 'editable' => true  ],
        'Address'    => [ 'required' => false, 'editable' => true  ],
        'Tel'        => [ 'required' => false, 'editable' => true  ],
        'Mobile'     => [ 'required' => false, 'editable' => true  ],
        'JoinDate'   => [ 'required' => false, 'editable' => true  ],
        'Membership' => [ 'required' => true,  'editable' => true  ],
        'Disabled'   => [ 'required' => true,  'editable' => true  ],
        'Notes'      => [ 'required' => false, 'editable' => true  ],
        'CreatedAt'  => [ 'required' => false, 'editable' => true  ],
        'UpdatedAt'  => [ 'required' => false, 'editable' => true  ]
    ];

    protected $_className;

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        parent::__construct('DEFAULT');
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 新增單筆借閱者/會員資料
     *
     * @param  array  $params  待新增資料陣列
     * @return integer
     */
    public function createOne(array $params): int
    {
        $functionName = __FUNCTION__;

        $field = [];
        $bind  = [];

        $createdAt = MsTime();

        try
        {
            if (!isset($data['CreatedAt']))
            {
                $data['CreatedAt'] = $createdAt;
            }
            if (!isset($data['UpdatedAt']))
            {
                $data['UpdatedAt'] = $createdAt;
            }

            foreach (array_keys($this->_columns) as $column)
            {
                if ($this->_columns[$column]['required'])
                {
                    if (!isset($params[$column]) || (trim($params[$column]) === '' && $params[$column] !== false))
                    {
                        throw new Exception("Column \"{$column}\" is required but unfilled", ExceptionCode::Unfilled);
                    }

                    if ($column === 'Disabled')
                    {
                        $bind[$column] = [ $params[$column], PDO::PARAM_BOOL ];
                    }
                    else
                    {
                        $bind[$column] = $params[$column];
                    }

                    $field[] = $column;
                }
                else
                {
                    if (isset($params[$column]) && trim($params[$column]) !== '')
                    {
                        $bind[$column] = $params[$column];
                        $field[] = $column;
                    }
                }
            }

            $value = ':' . implode(', :', $field);
            $field = '"' . implode('", "', $field) . '"';
            $sql   = "INSERT INTO public.\"{$this->_tableName}\" ({$field}) VALUES ({$value})";

            $result = $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }

        return $result;
    }

    /**
     * 依指定的欄位和值，查詢複數資料
     *
     * @param  string          $field           欄位名稱
     * @param  string|integer  $value           關鍵字
     * @param  integer         $limit           查詢資料限制筆數
     * @param  integer         $offset          查詢資料偏移量
     * @param  boolean         $includeDisabled  是否包含除帳（軟刪除）借閱者/會員：預設為 `false`
     * @return array
     */
    public function selectMultiple(string $field, mixed $value, int $limit, int $offset, bool $includeDisabled = false): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $withDisabled = ($includeDisabled) ? '' : 'AND "Disabled" IS FALSE';

            switch ($field)
            {
                case 'No':
                {
                    $sql = <<<SQL
                    SELECT
                        *
                    FROM public."{$this->_tableName}"
                    WHERE
                        "{$field}" = :{$field}
                        {$withDisabled}
                    LIMIT :limit
                    OFFSET :offset
                    SQL;

                    $bind = [
                        $field   => $value,
                        'limit'  => [ $limit,  PDO::PARAM_INT ],
                        'offset' => [ $offset, PDO::PARAM_INT ]
                    ];

                    break;
                }

                case 'Name':
                {
                    $sql = <<<SQL
                    SELECT
                        *
                    FROM public."{$this->_tableName}"
                    WHERE
                        "{$field}" LIKE :{$field}
                        {$withDisabled}
                    LIMIT :limit
                    OFFSET :offset
                    SQL;

                    $bind = [
                        $field => "%{$value}%",
                        'limit'  => [ $limit,  PDO::PARAM_INT ],
                        'offset' => [ $offset, PDO::PARAM_INT ]
                    ];

                    break;
                }
            }

            if (isset($sql) && isset($bind))
            {
                return $this->_db->query($sql, $bind);
            }
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }

    /**
     * 更新單筆資料
     *
     * @param  integer  $memberId  借閱者/會員 ID
     * @param  array    $data    待更新的借閱者/會員資料
     * @return integer
     */
    public function updateOne(int $memberId, array $data): int
    {
        $functionName = __FUNCTION__;

        $field = [];
        $bind  = [];

        $updatedAt = MsTime();

        try
        {
            foreach (array_keys($this->_columns) as $column)
            {
                if (isset($data[$column]))
                {
                    if ($this->_columns[$column]['editable'])
                    {
                        if ($column === 'Disabled')
                        {
                            $bind[$column] = [ $data[$column], PDO::PARAM_BOOL ];
                        }
                        else
                        {
                            $bind[$column] = $data[$column];
                        }

                        $field[] = "\"{$column}\" = :{$column}";
                    }
                    else
                    {
                        $logMessage = "{$this->_className}::{$functionName} Warning: Given field {$column} is not editable";
                        Logger::getInstance()->logWarning($logMessage);
                    }
                }
            }

            if (count($field) > 0)
            {
                if (!isset($bind['UpdatedAt']))
                {
                    $bind['UpdatedAt'] = $updatedAt;
                    $field[] = '"UpdatedAt" = :UpdatedAt';
                }

                $field = implode(', ', $field);
                $bind['Id'] = $memberId;
                $sql = "UPDATE public.\"{$this->_tableName}\" SET {$field} WHERE \"Id\" = :Id";

                Logger::getInstance()->logInfo($sql);
                Logger::getInstance()->logInfo(JsonUnescaped($bind));

                $result = $this->_db->query($sql, $bind);
            }
            else
            {
                $exMessage = 'No editable data given';

                $logMessage = "{$this->_className}::{$functionName} Error: {$exMessage}";
                Logger::getInstance()->logError($logMessage);

                throw new Exception($exMessage, ExceptionCode::BookData);
            }
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }

        return $result;
    }

    /**
     * 禁用單筆資料
     *
     * @param  integer  $memberId  借閱者/會員 ID
     * @param  boolean  $action    禁用（`true`）或啟用（`false`）
     * @return integer
     */
    public function disableOne(int $memberId, bool $action = true): int
    {
        $functionName = __FUNCTION__;

        try
        {
            $sql = <<<SQL
            UPDATE public."{$this->_tableName}" SET
                "Disabled" = :disabled
            WHERE "Id" = :memberId
            SQL;

            $bind = [
                'disabled' => [ $action, PDO::PARAM_BOOL ],
                'memberId' => $memberId
            ];

            $result = $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }

        return $result;
    }
}
