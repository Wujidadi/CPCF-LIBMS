<?php

// auto_update_time

namespace Database\Migrations;

use PDOException;
use Libraries\DBAPI;
use Libraries\Logger;
use Database\Migration;

/**
 * Migration class of the trigger `auto_update_time` on table `DemoTable`.
 */
class TriggerAutoUpdateTimeOnDemoTable extends Migration
{
    /**
     * Name of the target trigger.
     *
     * @var string
     */
    protected $_triggerName = 'auto_update_time';

    /**
     * Name of the target table.
     *
     * @var string
     */
    protected $_tableName = 'DemoTable';

    /**
     * Name of the trigger function our trigger calls.
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
     * Create the trigger.
     *
     * @return boolean
     */
    public function up()
    {
        $sqlArray = [

            <<<EOT
            CREATE TRIGGER "{$this->_triggerName}"
                BEFORE UPDATE
                ON public."{$this->_tableName}"
                FOR EACH ROW
                EXECUTE FUNCTION public.{$this->_triggerFunctionName}();
            EOT

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Trigger \"{$this->_triggerName}\" created");
        }

        return $runResult;
    }

    /**
     * Drop the trigger.
     *
     * @return boolean
     */
    public function down()
    {
        $sqlArray = [

            "DROP TRIGGER IF EXISTS \"{$this->_triggerName}\" on public.\"{$this->_tableName}\""

        ];

        if ($runResult = $this->_run($this->_className, __FUNCTION__, $sqlArray))
        {
            Logger::getInstance()->logInfo("Trigger \"{$this->_triggerName}\" dropped");
        }

        return $runResult;
    }
}
