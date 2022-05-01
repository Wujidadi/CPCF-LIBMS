<?php

namespace App;

/**
 * Parent class of controllers.
 */
abstract class Controller
{
    /**
     * Name of the object.
     *
     * @var string
     */
    protected $_className;

    /**
     * Unique instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance;

    /**
     * Get the unique instance of this class.
     *
     * @return self
     */
    abstract public static function getInstance();

    /**
     * Constructor.
     */
    protected function __construct() {}
}
