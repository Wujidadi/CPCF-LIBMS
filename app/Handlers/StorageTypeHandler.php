<?php

namespace App\Handlers;

use Exception;
use Libraries\Logger;
use App\ExceptionCode;
use App\Models\BookModel;
use App\Models\StorageTypeModel;

/**
 * 入庫類型處理器
 */
class StorageTypeHandler
{
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
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 取得所有入庫類型
     *
     * @return array
     */
    public function getAll(): array
    {
        return array_map(function($datum) {
            return [
                'Id' => (int) $datum['Id'],
                'Name' => $datum['Name'],
                'Alias' => $datum['Alias']
            ];
        }, StorageTypeModel::getInstance()->fetchAll());
    }
}
