<?php

namespace Database\Migrations\Tables;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the table `Books`.
 */
class Books extends Migration
{
    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'Books';

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

            <<<EOT
            CREATE TABLE public."{$this->_tableName}"
            (
                "Id"               bigserial                                              NOT NULL,
                "No"               character varying(10)    COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "Name"             character varying(1024)  COLLATE pg_catalog."C.UTF-8"  NOT NULL,
                "OriginalName"     character varying(1024)  COLLATE pg_catalog."C.UTF-8"      NULL,
                "Author"           character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Illustrator"      character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Editor"           character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Translator"       character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Series"           character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "Publisher"        character varying(191)   COLLATE pg_catalog."C.UTF-8"      NULL,
                "PublishDate"      date                                                       NULL,
                "PublishDateType"  unsigned_tinyint                                           NULL  DEFAULT 0,
                "Edition"          character varying(30)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "Print"            character varying(30)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "StorageDate"      date                                                       NULL,
                "StorageType"      smallint                                                   NULL,
                "Deleted"          boolean                                                NOT NULL  DEFAULT FALSE,
                "DeleteDate"       date                                                       NULL,
                "DeleteType"       smallint                                                   NULL,
                "Notes"            text                                                       NULL,
                "ISN"              character varying(17)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "EAN"              character varying(16)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "Barcode1"         character varying(30)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "Barcode2"         character varying(30)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "Barcode3"         character varying(30)    COLLATE pg_catalog."C.UTF-8"      NULL,
                "CategoryId"       bigint                                                     NULL,
                "LocationId"       bigint                                                     NULL,
                "CreatedAt"        timestamp(6) with time zone                            NOT NULL  DEFAULT CURRENT_TIMESTAMP,
                "UpdatedAt"        timestamp(6) with time zone                            NOT NULL  DEFAULT CURRENT_TIMESTAMP,

                CONSTRAINT "{$this->_tableName}_Id" UNIQUE ("Id"),

                PRIMARY KEY ("Id")
            )
            TABLESPACE pg_default
            EOT,
            "ALTER TABLE public.\"{$this->_tableName}\" OWNER to root",

            /*
            |--------------------------------------------------
            | 欄位備註
            |--------------------------------------------------
            */

            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Id\"              IS '書籍ID (流水號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"No\"              IS '書號'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Name\"            IS '書名'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"OriginalName\"    IS '原文書名'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Author\"          IS '作者'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Illustrator\"     IS '繪者'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Editor\"          IS '編者'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Translator\"      IS '譯者'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Series\"          IS '系列/叢書名'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Publisher\"       IS '出版者'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"PublishDate\"     IS '出版日期'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"PublishDateType\" IS '出版日期類別 (0=無, 1=年月日, 2=年月, 3=年)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Edition\"         IS '版本別'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Print\"           IS '印刷別'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"StorageDate\"     IS '入庫日期'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"StorageType\"     IS '入庫類別ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Deleted\"         IS '是否已報廢/刪除'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"DeleteDate\"      IS '報廢/刪除日期'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"DeleteType\"      IS '報廢/刪除類別ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Notes\"           IS '附註'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"ISN\"             IS 'ISBN或ISSN (含連字號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"EAN\"             IS '國際商品條碼 (含連字號)'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Barcode1\"        IS '其他條碼1'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Barcode2\"        IS '其他條碼2'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"Barcode3\"        IS '其他條碼3'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"CategoryId\"      IS '書籍分類ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"LocationId\"      IS '書籍架位ID'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"CreatedAt\"       IS '資料創建時間'",
            "COMMENT ON COLUMN public.\"{$this->_tableName}\".\"UpdatedAt\"       IS '資料最後更新時間'",

            /*
            |--------------------------------------------------
            | 欄位索引及索引備註
            |--------------------------------------------------
            */

            <<<EOT
            CREATE INDEX "{$this->_tableName}_No" ON public."{$this->_tableName}" USING btree (
                "No"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_No\" IS '書號索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Name" ON public."{$this->_tableName}" USING btree (
                "Name"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Name\" IS '書名索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_OriginalName" ON public."{$this->_tableName}" USING btree (
                "OriginalName"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_OriginalName\" IS '原文書名索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Author" ON public."{$this->_tableName}" USING btree (
                "Author"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Author\" IS '作者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Illustrator" ON public."{$this->_tableName}" USING btree (
                "Illustrator"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Illustrator\" IS '繪者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Editor" ON public."{$this->_tableName}" USING btree (
                "Editor"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Editor\" IS '編者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Translator" ON public."{$this->_tableName}" USING btree (
                "Translator"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Translator\" IS '譯者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Maker" ON public."{$this->_tableName}" USING btree (
                "Author"       COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST,
                "Illustrator"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST,
                "Editor"       COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST,
                "Translator"   COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Maker\" IS '創作者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Series" ON public."{$this->_tableName}" USING btree (
                "Series"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Series\" IS '系列/叢書名索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Publisher" ON public."{$this->_tableName}" USING btree (
                "Publisher"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Publisher\" IS '出版者索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_PublishDate" ON public."{$this->_tableName}" USING btree (
                "PublishDate"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_PublishDate\" IS '出版日期索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Edition" ON public."{$this->_tableName}" USING btree (
                "Edition"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Edition\" IS '版本別索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Print" ON public."{$this->_tableName}" USING btree (
                "Print"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Print\" IS '印刷別索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_StorageDate" ON public."{$this->_tableName}" USING btree (
                "StorageDate"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_StorageDate\" IS '入庫日期索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_Deleted" ON public."{$this->_tableName}" USING btree (
                "Deleted"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Deleted\" IS '報廢/刪除索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_DeleteDate" ON public."{$this->_tableName}" USING btree (
                "DeleteDate"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_Deleted\" IS '報廢/刪除日期索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_ISN" ON public."{$this->_tableName}" USING btree (
                "ISN"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_ISN\" IS 'ISBN或ISSN索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_EAN" ON public."{$this->_tableName}" USING btree (
                "EAN"  COLLATE pg_catalog."C.UTF-8"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_EAN\" IS '國際商品條碼索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_CategoryId" ON public."{$this->_tableName}" USING btree (
                "CategoryId"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_CategoryId\" IS '書籍分類索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_LocationId" ON public."{$this->_tableName}" USING btree (
                "LocationId"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_LocationId\" IS '書籍架位索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_CreatedAt" ON public."{$this->_tableName}" USING btree (
                "CreatedAt"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_CreatedAt\" IS '資料創建時間索引（書籍資料表）'",

            <<<EOT
            CREATE INDEX "{$this->_tableName}_UpdatedAt" ON public."{$this->_tableName}" USING btree (
                "UpdatedAt"  ASC  NULLS LAST
            )
            EOT,
            "COMMENT ON INDEX public.\"{$this->_tableName}_UpdatedAt\" IS '資料最後更新時間索引（書籍資料表）'",

            /*
            |--------------------------------------------------
            | 觸發器
            |--------------------------------------------------
            */

            <<<EOT
            CREATE TRIGGER auto_update_time BEFORE UPDATE ON public."{$this->_tableName}"
                FOR EACH ROW EXECUTE FUNCTION public.update_timestamp();
            EOT,

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
