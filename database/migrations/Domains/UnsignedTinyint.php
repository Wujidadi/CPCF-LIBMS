<?php

namespace Database\Migrations\Domains;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the domain `unsigned_tinyint`.
 */
class UnsignedTinyint extends Migration
{
    /**
     * Name of the target domain.
     *
     * @var string
     */
    protected $_domainName = 'unsigned_tinyint';

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
     * Create the domain.
     *
     * @return boolean
     */
    public function up(): bool
    {
        $sqlArray = [

            <<<SQL
            CREATE DOMAIN public."{$this->_domainName}"
                AS int2
                CHECK (
                    VALUE >= 0 AND VALUE < 256
                );
            SQL

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Domain \"{$this->_domainName}\" created");
        }

        return $runResult;
    }

    /**
     * Drop the domain.
     *
     * @return boolean
     */
    public function down(): bool
    {
        $sqlArray = [

            "DROP DOMAIN IF EXISTS public.\"{$this->_domainName}\""

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Domain \"{$this->_domainName}\" dropped");
        }

        return $runResult;
    }
}
