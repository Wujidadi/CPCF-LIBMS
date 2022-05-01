<?php

namespace App\Exceptions;

/**
 * 輸入資料驗證例外
 */
class InputException extends \Exception
{
    /**
     * 錯誤訊息
     *
     * @var string
     */
    protected $message;

    /**
     * 錯誤代碼
     *
     * @var integer|string|double
     */
    protected $code;

    /**
     * 建構子
     *
     * @param string|null            $message  錯誤訊息
     * @param integer|string|double  $code     錯誤代碼
     */
    public function __construct(?string $message = null, mixed $code = 0)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
