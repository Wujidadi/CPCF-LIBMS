<?php

namespace App\Handlers;

use App\Handler;
use App\Models\StorageTypeModel;

/**
 * 入庫類型處理器
 */
class StorageTypeHandler extends Handler
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
