<?php

namespace Libraries\HTTP;

/**
 * HTTP request handling class.
 */
class Request
{
    /**
     * Raw input data
     *
     * @var array|string
     */
    protected $_rawInput;

    /**
     * Input data in string type.
     *
     * @var string
     */
    protected $_strData;

    /**
     * Input data in array type.
     *
     * @var array
     */
    protected $_arrData = null;

    /**
     * Get the instance of this class.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return new self();
    }

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
     * Initialization method (input data parsing).
     *
     * @return void
     */
    protected function _init(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            $this->_rawInput = $_GET;

            $this->_strData = json_encode($this->_rawInput, 320);
            $this->_arrData = $this->_rawInput;
        }
        else
        {
            $this->_rawInput = file_get_contents('php://input');

            $this->_strData = $this->_rawInput;
            $this->_arrData = json_decode($this->_rawInput, true);
        }

        $this->_arrData = is_null($this->_arrData) ? [] : $this->_arrData;
    }

    /**
     * Replace original input data.
     *
     * @param  array  $data  Data to replace the original input data.
     * @return void
     */
    public function setData(array $data): void
    {
        $this->_strData = json_encode($data, 320);
        $this->_arrData = $data;
    }

    /**
     * Get raw input data.
     *
     * @return string|array
     */
    public function getRawInput(): mixed
    {
        return $this->_rawInput;
    }

    /**
     * Get input data.
     *
     * @param  boolean  $arrType  The data should be return in array type or not.
     * @return string|array
     */
    public function getData(bool $arrType = true): mixed
    {
        if ($arrType)
        {
            return $this->_arrData;
        }
        else
        {
            return $this->_strData;
        }
    }
}
