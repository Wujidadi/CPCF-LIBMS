<?php

namespace Database\Migrations\Tables;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * 書籍分類資料表（`BookCategories`）遷移類別
 */
class BookCategories extends Migration
{
    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'BookCategories';

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance()
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
    public function up()
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
                "Id"         smallserial                                          NOT NULL,
                "Code"       character varying(10)  COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "Name"       character varying(50)  COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "Level"      unsigned_tinyint                                     NOT NULL,
                "ParentId"   bigint                                                   NULL,
                "CreatedAt"  timestamp(6) with time zone                          NOT NULL  DEFAULT CURRENT_TIMESTAMP,
                "UpdatedAt"  timestamp(6) with time zone                          NOT NULL  DEFAULT CURRENT_TIMESTAMP,

                CONSTRAINT "{$this->_tableName}_Id" UNIQUE ("Id"),

                PRIMARY KEY ("Id")
            )
            TABLESPACE pg_default
            SQL,
            "COMMENT ON TABLE public.\"{$this->_tableName}\" IS '書籍分類資料表'",
            "ALTER TABLE public.\"{$this->_tableName}\" OWNER to root",

            /*
            |--------------------------------------------------
            | 欄位備註
            |--------------------------------------------------
            */

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Id\"        IS '書籍分類ID (流水號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Code\"      IS '書籍分類代碼'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Name\"      IS '書籍分類名稱'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Level\"     IS '書籍分類階層'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"ParentId\"  IS '上一級書籍分類ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"CreatedAt\" IS '資料創建時間'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"UpdatedAt\" IS '資料最後更新時間'",

            /*
            |--------------------------------------------------
            | 欄位索引及索引備註
            |--------------------------------------------------
            */

            "COMMENT ON CONSTRAINT \"{$this->_tableName}_Id\" ON public.\"{$this->_tableName}\" IS '書籍分類資料表主鍵'",

            <<<SQL
            CREATE UNIQUE INDEX "{$this->_tableName}_Code" ON public."{$this->_tableName}" USING btree (
                "Code"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Code\" IS '書籍分類代碼索引（書籍分類資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Name" ON public."{$this->_tableName}" USING btree (
                "Name"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Name\" IS '書籍分類名稱索引（書籍分類資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Level" ON public."{$this->_tableName}" USING btree (
                "Level"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Level\" IS '書籍分類階層索引（書籍分類資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_ParentId" ON public."{$this->_tableName}" USING btree (
                "ParentId"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_ParentId\" IS '上一級書籍分類ID索引（書籍分類資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_CreatedAt" ON public."{$this->_tableName}" USING btree (
                "CreatedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_CreatedAt\" IS '資料創建時間（書籍分類資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_UpdatedAt" ON public."{$this->_tableName}" USING btree (
                "UpdatedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_UpdatedAt\" IS '資料最後更新時間（書籍分類資料表）'",

            /*
            |--------------------------------------------------
            | 觸發器
            |--------------------------------------------------
            */

            <<<SQL
            CREATE TRIGGER auto_update_time BEFORE UPDATE ON public."{$this->_tableName}"
                FOR EACH ROW EXECUTE FUNCTION public.update_timestamp();
            SQL

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
    public function down()
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
