<?php

namespace App\Models;

use Exception;
use PDOException;
use Libraries\Logger;
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
        'No'              => [ 'required' => true  ],
        'Name'            => [ 'required' => true  ],
        "OriginalName"    => [ 'required' => false ],
        "Author"          => [ 'required' => false ],
        "Illustrator"     => [ 'required' => false ],
        "Editor"          => [ 'required' => false ],
        "Translator"      => [ 'required' => false ],
        "Series"          => [ 'required' => false ],
        "Publisher"       => [ 'required' => false ],
        "PublishDate"     => [ 'required' => false ],
        "PublishDateType" => [ 'required' => false ],
        "Edition"         => [ 'required' => false ],
        "Print"           => [ 'required' => false ],
        "StorageDate"     => [ 'required' => false ],
        "StorageType"     => [ 'required' => false ],
        "Deleted"         => [ 'required' => true  ],
        "DeleteDate"      => [ 'required' => false ],
        "DeleteType"      => [ 'required' => false ],
        "Notes"           => [ 'required' => false ],
        "ISN"             => [ 'required' => false ],
        "EAN"             => [ 'required' => false ],
        "Barcode1"        => [ 'required' => false ],
        "Barcode2"        => [ 'required' => false ],
        "Barcode3"        => [ 'required' => false ],
        "CategoryId"      => [ 'required' => false ],
        "LocationId"      => [ 'required' => false ]
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
    public function addOne(array $params): int
    {
        $functionName = __FUNCTION__;

        $field = [];
        $bind  = [];

        try
        {
            foreach (array_keys($this->_columns) as $column)
            {
                if ($this->_columns[$column]['required'])
                {
                    if (!isset($params[$column]) || (trim($params[$column]) === '' && $params[$column] !== false))
                    {
                        throw new Exception("Column \"{$column}\" is required but unfilled");
                    }

                    $bind[$column] = $params[$column];
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
            $exMsg  = $ex->getMessage();
            Logger::getInstance()->logError("{$this->_className}::{$functionName} PDOException: ({$exCode}) {$exMsg}");

            throw new Exception($exMsg, 35);    // 「PDO」的字母值加總
        }

        return $result;
    }

    /**
     * 依指定的欄位和值查詢書籍資料
     *
     * @param  string          $field           欄位名稱
     * @param  string|integer  $value           關鍵字
     * @param  boolean         $includeDeleted  是否包含除帳（軟刪除）書籍：預設為 `false`
     * @return array
     */
    public function get(string $field, mixed $value, bool $includeDeleted = false): array
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
                    $sql  = "SELECT * FROM public.\"{$this->_tableName}\" WHERE \"{$field}\" = :{$field} {$withDeleted}";
                    $bind = [ $field => $value ];
                    return $this->_db->query($sql, $bind);

                case 'Name':
                case 'OriginalName':
                case 'Author':
                case 'Illustrator':
                case 'Editor':
                case 'Translator':
                case 'Series':
                case 'Publisher':
                    $sql  = "SELECT * FROM public.\"{$this->_tableName}\" WHERE \"{$field}\" LIKE :{$field} {$withDeleted}";
                    $bind = [ $field => "%{$value}%" ];
                    return $this->_db->query($sql, $bind);

                case 'Maker':
                    $sql  = "SELECT * FROM public.\"{$this->_tableName}\" WHERE CONCAT(\"Author\", \"Illustrator\", \"Editor\", \"Translator\") LIKE :{$field} {$withDeleted}";
                    $bind = [ $field => "%{$value}%" ];
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
            $exMsg  = $ex->getMessage();
            Logger::getInstance()->logError("{$this->_className}::{$functionName} PDOException: ({$exCode}) {$exMsg}");

            throw new Exception($exMsg, 35);    // 「PDO」的字母值加總
        }
    }
}
