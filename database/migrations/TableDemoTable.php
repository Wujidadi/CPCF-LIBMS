<?php

namespace Database\Migrations;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the table `DemoTable`.
 */
class TableDemoTable extends Migration
{
    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'DemoTable';

    protected $_className;

    protected static $_uniqueInstance = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        parent::__construct();
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

            <<<EOT
            CREATE TABLE public."{$this->_tableName}"
            (
                "ID"        bigserial                                            NOT NULL,
                "Type"      character varying(191)  COLLATE pg_catalog."C.UTF-8" NOT NULL,
                "Content"   character varying(2048) COLLATE pg_catalog."C.UTF-8" NOT NULL,
                "Data"      jsonb                                                NOT NULL,
                "Flag"      boolean,
                "Available" unsigned_tinyint                                     NOT NULL DEFAULT 1,
                "Editable"  unsigned_tinyint                                     NOT NULL DEFAULT 1,
                "CreatedAt" timestamp(6) with time zone                          NOT NULL DEFAULT CURRENT_TIMESTAMP,
                "UpdatedAt" timestamp(6) with time zone                          NOT NULL DEFAULT CURRENT_TIMESTAMP,

                CONSTRAINT "{$this->_tableName}_ID" UNIQUE ("ID"),

                PRIMARY KEY ("ID")
            )
            TABLESPACE pg_default
            EOT,

            "ALTER TABLE public.\"{$this->_tableName}\" OWNER to root",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"ID\"        IS '流水號（唯一值，主鍵）'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Type\"      IS '類型'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Content\"   IS '內容'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Data\"      IS '結構化資料'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Flag\"      IS '旗標'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Available\" IS '可用性'",
            
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Editable\"  IS '可變性'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"CreatedAt\" IS '建立時間'",

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"UpdatedAt\" IS '更新時間'"

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
