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
     * 類別名稱
     *
     * @var string
     */
    protected $_className;

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

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance()
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
    public function addOne($params)
    {
        $functionName = __FUNCTION__;

        $arrFields = [];
        $arrBind = [];

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

                    ${$column} = $arrBind[$column] = $params[$column];
                    $arrFields[] = $column;
                }
                else
                {
                    if (isset($params[$column]) && trim($params[$column]) !== '')
                    {
                        ${$column} = $arrBind[$column] = $params[$column];
                        $arrFields[] = $column;
                    }
                }
            }

            $strFields = '"' . implode('", "', $arrFields) . '"';
            $strBind   = ':' . implode(', :', $arrFields);
            $strSQL    = "INSERT INTO public.\"{$this->_tableName}\" ({$strFields}) VALUES ({$strBind})";
            
            $intResult = $this->_db->query($strSQL, $arrBind);
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

            throw new Exception($exMsg, 35);    // P (16) + D (4) + O (15) = 35
        }

        return $intResult;
    }
}
