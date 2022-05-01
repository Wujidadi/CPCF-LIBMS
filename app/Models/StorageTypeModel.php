<?php

namespace App\Models;

use App\Model;

/**
 * 入庫類別資料模型
 */
class StorageTypeModel extends Model
{
    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $_tableName = 'StorageTypes';

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
     * 取得所有入庫類型資料
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->_db->select($this->_tableName);
    }
}
