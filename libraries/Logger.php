<?php

namespace Libraries;

use Exception;

/**
 * Log handle class.
 */
class Logger
{
    /**
     * Path of log file.
     *
     * @var string
     */
    protected $_logFile = '';

    /**
     * Prefix of the log message.
     *
     * @var string
     */
    protected $_logPrefix = '';

    /**
     * Sequence of log messages.
     *
     * @var integer
     */
    protected $_logSequence = 0;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /**
     * Constructor.
     *
     * @return void
     */
    protected function __construct()
    {
        $this->_init();
    }

    /**
     * Get the instance of this class.
     * 
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    /**
     * Initialization method.
     * 
     * @return void
     */
    private function _init()
    {
        $this->_logFile = LOG_DIR . DIRECTORY_SEPARATOR . 'log_' . date('Ymd') . '.log';
    }

    /**
     * Set the prefix of the log message.
     *
     * @param  string  $prefix  Prefix of the log message.
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->_logPrefix = $prefix;
    }

    /**
     * Write a "DEBUG" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logDebug($message)
    {
        return $this->_writeLog('DEBUG', $message);
    }

    /**
     * Write a "INFO" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logInfo($message)
    {
        return $this->_writeLog('INFO', $message);
    }

    /**
     * Write a "NOTICE" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logNotice($message)
    {
        return $this->_writeLog('NOTICE', $message);
    }

    /**
     * Write a "WARNING" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logWarning($message)
    {
        return $this->_writeLog('WARNING', $message);
    }

    /**
     * Write a "ERROR" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logError($message)
    {
        return $this->_writeLog('ERROR', $message);
    }

    /**
     * Write a "CRITICAL" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logCritical($message)
    {
        return $this->_writeLog('CRITICAL', $message);
    }

    /**
     * Write a "ALERT" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logAlert($message)
    {
        return $this->_writeLog('ALERT', $message);
    }

    /**
     * Write a "EMERGENCY" message to the log.
     *
     * @param  string  $message  Log message
     * @return array
     */
    public function logEmergency($message)
    {
        return $this->_writeLog('EMERGENCY', $message);
    }

    /**
     * Write a message to the log.
     *
     * @param  string  $type     Type of the log message
     * @param  string  $message  Log message
     * @return array
     */
    protected function _writeLog($type, $message)
    {
        try
        {
            $time     = MsTime();
            $uname    = php_uname('n');
            $prefix   = $this->_logPrefix;
            $pid      = getmypid();
            $sequence = ++$this->_logSequence;
            $logMessage = "{$time} {$uname} {$prefix}[{$pid}]: [{$type}]({$sequence}) {$message}\n";

            $result = file_put_contents($this->_logFile, $logMessage, FILE_APPEND);

            return [
                'status' => true,
                'code' => 0,
                'message' => $result
            ];
        }
        catch (Exception $error)
        {
            return [
                'status' => false,
                'code' => $error->getCode(),
                'message' => $error->getMessage()
            ];
        }
    }
}
