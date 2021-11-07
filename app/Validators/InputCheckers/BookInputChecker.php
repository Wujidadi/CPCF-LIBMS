<?php

namespace App\Validators\InputCheckers;

use App\Validators\InputChecker;

/**
 * 書籍資料輸入驗證器
 */
class BookInputChecker extends InputChecker
{
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
}
