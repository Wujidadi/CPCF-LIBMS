<?php

namespace Database\Migrations\Tables;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * 借閱者資料表（`Members`）遷移類別
 */
class Members extends Migration
{
    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'Members';

    /**
     * Instance of this class.
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
                "Id"          bigserial                                              NOT NULL,
                "No"          character varying(10)    COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "Name"        character varying(1000)  COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "Email"       character varying(255)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Gender"      unsigned_tinyint                                           NULL,
                "Birthday"    date                                                       NULL,
                "Address"     character varying(512)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Tel"         character varying(20)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "Mobile"      character varying(20)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "JoinDate"    date                                                       NULL,
                "Membership"  unsigned_tinyint                                       NOT NULL  DEFAULT 1,
                "Disabled"    boolean                                                NOT NULL  DEFAULT FALSE,
                "Notes"       text                                                       NULL,
                "CreatedAt"   timestamp(6) with time zone                            NOT NULL  DEFAULT CURRENT_TIMESTAMP,
                "UpdatedAt"   timestamp(6) with time zone                            NOT NULL  DEFAULT CURRENT_TIMESTAMP,

                CONSTRAINT "{$this->_tableName}_Id" UNIQUE ("Id"),

                PRIMARY KEY ("Id")
            )
            TABLESPACE pg_default
            SQL,
            "COMMENT ON TABLE public.\"{$this->_tableName}\" IS '借閱者資料表'",
            "ALTER TABLE public.\"{$this->_tableName}\" OWNER to root",

            /*
            |--------------------------------------------------
            | 欄位備註
            |--------------------------------------------------
            */

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Id\"              IS '借閱者ID (流水號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"No\"              IS '借閱者/會員編號'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Name\"            IS '姓名'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Email\"           IS '電子郵件信箱'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Gender\"          IS '性別 (0=女, 1=男)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Birthday\"        IS '出生年月日'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Address\"         IS '通訊地址'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Tel\"             IS '電話號碼'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Mobile\"          IS '手機號碼'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"JoinDate\"        IS '入會日期'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Membership\"      IS '會籍狀態 (0=無會籍/會籍取消, 1=會籍正常, 2=會籍保留)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Disabled\"        IS '可用狀態'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Notes\"           IS '附註'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"CreatedAt\"       IS '資料創建時間'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"UpdatedAt\"       IS '資料最後更新時間'",

            /*
            |--------------------------------------------------
            | 欄位索引及索引備註
            |--------------------------------------------------
            */

            "COMMENT ON CONSTRAINT \"{$this->_tableName}_Id\" ON public.\"{$this->_tableName}\" IS '借閱者資料表主鍵'",

            <<<SQL
            CREATE UNIQUE INDEX "{$this->_tableName}_No" ON public."{$this->_tableName}" USING btree (
                "No"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_No\" IS '借閱者/會員編號索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Name" ON public."{$this->_tableName}" USING btree (
                "Name"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Name\" IS '姓名索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Email" ON public."{$this->_tableName}" USING btree (
                "Email"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Email\" IS '電子郵件信箱索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Mobile" ON public."{$this->_tableName}" USING btree (
                "Mobile"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Mobile\" IS '手機號碼索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Membership" ON public."{$this->_tableName}" USING btree (
                "Membership"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Membership\" IS '會籍狀態索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_Disabled" ON public."{$this->_tableName}" USING btree (
                "Disabled"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Disabled\" IS '可用狀態索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_CreatedAt" ON public."{$this->_tableName}" USING btree (
                "CreatedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_CreatedAt\" IS '資料創建時間索引（借閱者資料表）'",

            <<<SQL
            CREATE INDEX "{$this->_tableName}_UpdatedAt" ON public."{$this->_tableName}" USING btree (
                "UpdatedAt"  ASC  NULLS LAST
            )
            SQL,
            "COMMENT ON INDEX public.\"{$this->_tableName}_UpdatedAt\" IS '資料最後更新時間索引（借閱者資料表）'",

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
