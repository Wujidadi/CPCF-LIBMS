<?php

namespace Database\Migrations\TriggerFunctions;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the trigger function `update_timestamp()`.
 */
class UpdateTimestamp extends Migration
{
    /**
     * Name of the target trigger function.
     *
     * @var string
     */
    protected $_triggerFunctionName = 'update_timestamp';

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
     * Create the trigger function.
     *
     * @return boolean
     */
    public function up(): bool
    {
        $sqlArray = [

            <<<SQL
            CREATE OR REPLACE FUNCTION public.{$this->_triggerFunctionName}()
                RETURNS trigger
                LANGUAGE 'plpgsql'
            AS $$
            BEGIN
                new."UpdatedAt" = CURRENT_TIMESTAMP;
                RETURN new;
            END
            $$;
            SQL

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Trigger function \"{$this->_triggerFunctionName}\" created");
        }

        return $runResult;
    }

    /**
     * Drop the trigger function.
     *
     * @return boolean
     */
    public function down(): bool
    {
        $sqlArray = [

            "DROP FUNCTION IF EXISTS public.{$this->_triggerFunctionName}"

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Trigger function \"{$this->_triggerFunctionName}\" dropped");
        }

        return $runResult;
    }
}
