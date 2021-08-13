<?php

namespace Database\Migrations;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the trigger function `update_timestamp()`.
 */
class TriggerFunctionUpdateTimestamp extends Migration
{
    /**
     * Name of the target trigger function.
     *
     * @var string
     */
    protected $_triggerFunctionName = 'update_timestamp';

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
     * Create the trigger function.
     *
     * @return boolean
     */
    public function up()
    {
        $sqlArray = [

            <<<EOT
            CREATE OR REPLACE FUNCTION public.{$this->_triggerFunctionName}()
                RETURNS trigger
                LANGUAGE 'plpgsql'
            AS $$
            BEGIN
                new."UpdatedAt" = CURRENT_TIMESTAMP;
                RETURN new;
            END
            $$;
            EOT

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
    public function down()
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
