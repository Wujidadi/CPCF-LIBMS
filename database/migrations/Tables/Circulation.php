<?php

namespace Database\Migrations\Tables;

use Libraries\Logger;
use Database\Migration;

/**
 * 書籍流通紀錄資料表（`Circulation`）遷移類別
 */
class Circulation extends Migration
{
    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'Circulation';

    /**
     * Unique instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    /**
     * Constructor.
     *
     * Override the constructor of parent `Migration` class to use different DB configurations.
     */
    protected function __construct()
    {
        parent::__construct('DEFAULT');
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * Create the table.
     *
     * @return boolean
     */
    public function up(): bool
    {
        $sqlArray = [

            /*
            |--------------------------------------------------
            | 建表
            |--------------------------------------------------
            */

            <<<SQL
            CREATE TABLE public."{$this->_tableName}"
            (
                "Id"          bigserial                    NOT NULL,
                "BookId"      bigint                       NOT NULL,
                "MemberId"    bigint                       NOT NULL,
                "BorrowedAt"  timestamp(6) with time zone  NOT NULL  DEFAULT CURRENT_TIMESTAMP,
                "ReturnedAt"  timestamp(6) with time zone      NULL,

                CONSTRAINT "{$this->_tableName}_Id" UNIQUE ("Id"),

                PRIMARY KEY ("Id")
            )
            TABLESPACE pg_default
            SQL,
            "COMMENT ON TABLE public.\"{$this->_tableName}\" IS '書籍流通紀錄資料表'",
            "ALTER TABLE public.\"{$this->_tableName}\" OWNER to root",

            /*
            |--------------------------------------------------
            | 欄位備註
            |--------------------------------------------------
            */

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Id\"         IS '書籍刪除類別ID (流水號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"BookId\"     IS '書籍ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"MemberId\"   IS '借閱者ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"BorrowedAt\" IS '借出時間'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"ReturnedAt\" IS '歸還時間'",

            /*
            |--------------------------------------------------
            | 欄位索引及索引備註
            |--------------------------------------------------
            */

            "COMMENT ON CONSTRAINT \"{$this->_tableName}_Id\" ON public.\"{$this->_tableName}\" IS '書籍流通紀錄資料表主鍵'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_BookId" ON public."{$this->_tableName}" USING btree (
                "BookId"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_BookId\" IS '書籍ID索引（書籍流通紀錄資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_MemberId" ON public."{$this->_tableName}" USING btree (
                "MemberId"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_MemberId\" IS '借閱者ID索引（書籍流通紀錄資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_History" ON public."{$this->_tableName}" USING btree (
                "BookId"    ASC  NULLS LAST,
                "MemberId"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_History\" IS '借出歷史（書籍ID與借閱者ID組合）索引（書籍流通紀錄資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_BorrowedAt" ON public."{$this->_tableName}" USING btree (
                "BorrowedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_BorrowedAt\" IS '借出時間索引（書籍流通紀錄資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_ReturnedAt" ON public."{$this->_tableName}" USING btree (
                "ReturnedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_ReturnedAt\" IS '歸還時間索引（書籍流通紀錄資料表）'"

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Table \"{$this->_tableName}\" created");
        }

        return $runResult;
    }

    /**
     * Drop the table.
     *
     * @return boolean
     */
    public function down(): bool
    {
        $sqlArray = [

            "DROP TABLE IF EXISTS public.\"{$this->_tableName}\""

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Table \"{$this->_tableName}\" dropped");
        }

        return $runResult;
    }
}
