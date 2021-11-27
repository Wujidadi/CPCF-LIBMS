<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use Libraries\Logger;
use App\ExceptionCode;
use App\Model;

/**
 * 書籍資料表模型
 */
class BookModel extends Model
{
    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $_tableName = 'Books';

    /**
     * 資料表欄位屬性表
     *
     * @var array
     */
    protected $_columns = [
        'No'              => [ 'required' => true,  'editable' => false ],
        'Name'            => [ 'required' => true,  'editable' => true  ],
        'OriginalName'    => [ 'required' => false, 'editable' => true  ],
        'Author'          => [ 'required' => false, 'editable' => true  ],
        'Illustrator'     => [ 'required' => false, 'editable' => true  ],
        'Editor'          => [ 'required' => false, 'editable' => true  ],
        'Translator'      => [ 'required' => false, 'editable' => true  ],
        'Series'          => [ 'required' => false, 'editable' => true  ],
        'Publisher'       => [ 'required' => false, 'editable' => true  ],
        'PublishDate'     => [ 'required' => false, 'editable' => true  ],
        'PublishDateType' => [ 'required' => false, 'editable' => true  ],
        'Edition'         => [ 'required' => false, 'editable' => true  ],
        'Print'           => [ 'required' => false, 'editable' => true  ],
        'StorageDate'     => [ 'required' => false, 'editable' => true  ],
        'StorageType'     => [ 'required' => false, 'editable' => true  ],
        'Deleted'         => [ 'required' => true,  'editable' => true  ],
        'DeleteDate'      => [ 'required' => false, 'editable' => true  ],
        'DeleteType'      => [ 'required' => false, 'editable' => true  ],
        'Notes'           => [ 'required' => false, 'editable' => true  ],
        'ISN'             => [ 'required' => false, 'editable' => true  ],
        'EAN'             => [ 'required' => false, 'editable' => true  ],
        'Barcode1'        => [ 'required' => false, 'editable' => true  ],
        'Barcode2'        => [ 'required' => false, 'editable' => true  ],
        'Barcode3'        => [ 'required' => false, 'editable' => true  ],
        'CategoryId'      => [ 'required' => false, 'editable' => true  ],
        'LocationId'      => [ 'required' => false, 'editable' => true  ],
        'CreatedAt'       => [ 'required' => false, 'editable' => true  ],
        'UpdatedAt'       => [ 'required' => false, 'editable' => true  ]
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
     * 新增單筆書籍資料
     *
     * @param  array  $params  待新增資料陣列
     * @return integer
     */
    public function insertOne(array $params): int
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

                    if ($column === 'Deleted')
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
     * 依 ID 查詢資料筆數
     *
     * @param  integer  $id  ID（主鍵）
     * @return integer
     */
    public function countById(int $id): int
    {
        $sql = <<<SQL
        SELECT
            COUNT("Id") AS "Count"
        FROM public."{$this->_tableName}"
        WHERE "Id" = :id
        SQL;

        $bind = [
            'id' => $id
        ];

        return $this->_db->query($sql, $bind)[0]['Count'];
    }

    /**
     * 依書籍 ID 查詢單筆書籍資料
     *
     * @param  integer  $id  書籍 ID
     * @return array
     */
    public function selectOneById(int $id): array
    {
        $functionName = __FUNCTION__;

        $sql = <<<SQL
        SELECT
            *
        FROM public."{$this->_tableName}"
        WHERE "Id" = :id
        SQL;

        $bind = [
            'id' => $id
        ];

        return $this->_db->query($sql, $bind);
    }

    /**
     * 依書號查詢單筆書籍資料
     *
     * @param  string  $no  書籍編號
     * @return array
     */
    public function selectOneByNo(string $no): array
    {
        $functionName = __FUNCTION__;

        $sql = <<<SQL
        SELECT
            *
        FROM public."{$this->_tableName}"
        WHERE "No" = :no
        SQL;

        $bind = [
            'no' => $no
        ];

        return $this->_db->query($sql, $bind);
    }

    /**
     * 依指定的欄位和值，查詢複數資料
     *
     * @param  string          $field           欄位名稱
     * @param  string|integer  $value           關鍵字
     * @param  integer         $limit           查詢資料限制筆數
     * @param  integer         $offset          查詢資料偏移量
     * @param  boolean         $includeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return array
     */
    public function selectMultiple(string $field, mixed $value, int $limit, int $offset, bool $includeDeleted = false): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $withDeleted = ($includeDeleted) ? '' : 'AND "Deleted" IS FALSE';

            switch ($field)
            {
                case 'No':
                case 'ISN':
                case 'EAN':
                {
                    $sql = <<<SQL
                    SELECT
                        *
                    FROM public."{$this->_tableName}"
                    WHERE
                        "{$field}" = :{$field}
                        {$withDeleted}
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
                case 'OriginalName':
                case 'Author':
                case 'Illustrator':
                case 'Editor':
                case 'Translator':
                case 'Series':
                case 'Publisher':
                {
                    $sql = <<<SQL
                    SELECT
                        *
                    FROM public."{$this->_tableName}"
                    WHERE
                        "{$field}" LIKE :{$field}
                        {$withDeleted}
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

                case 'Maker':
                {
                    $sql = <<<SQL
                    SELECT
                        *
                    FROM public."{$this->_tableName}"
                    WHERE
                        CONCAT("Author", "Illustrator", "Editor", "Translator") LIKE :{$field}
                        {$withDeleted}
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
     * 依分頁查詢全部資料
     *
     * @param  integer  $limit           查詢資料限制筆數
     * @param  integer  $offset          查詢資料偏移量
     * @param  boolean  $includeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return array
     */
    public function selectAllWithPage(int $limit, int $offset, bool $includeDeleted = false): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $withDeleted = ($includeDeleted) ? '' : 'WHERE "Deleted" IS FALSE';

            $sql = <<<SQL
            SELECT
                *
            FROM public."{$this->_tableName}"
            {$withDeleted}
            LIMIT :limit
            OFFSET :offset
            SQL;

            $bind = [
                'limit'  => [ $limit,  PDO::PARAM_INT ],
                'offset' => [ $offset, PDO::PARAM_INT ]
            ];

            return $this->_db->query($sql, $bind);
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
     * @param  integer  $bookId  書籍 ID
     * @param  array    $data    待更新的書籍資料
     * @return integer
     */
    public function updateOne(int $bookId, array $data): int
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
                        if ($column === 'Deleted')
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
                $bind['Id'] = $bookId;
                $sql = "UPDATE public.\"{$this->_tableName}\" SET {$field} WHERE \"Id\" = :Id";

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
     * 軟刪除單筆資料
     *
     * @param  integer       $bookId      書籍 ID
     * @param  integer|null  $deleteType  刪除原因類別 ID
     * @return integer
     */
    public function softDeleteOne(int $bookId, ?int $deleteType = null): int
    {
        $functionName = __FUNCTION__;

        try
        {
            $sql = <<<SQL
            UPDATE public."{$this->_tableName}" SET
                "Deleted" = true,
                "DeleteDate" = :deleteDate,
                "DeleteType" = :deleteType
            WHERE "Id" = :bookId
            SQL;

            $bind = [
                'bookId' => $bookId,
                'deleteDate' => date('Y-m-d'),
                'deleteType' => !is_null($deleteType) ? [ $deleteType , PDO::PARAM_INT ] : [ null, PDO::PARAM_NULL ]
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
