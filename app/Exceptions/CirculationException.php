<?php

namespace App\Exceptions;

use Exception;

/**
 * 書籍流通資料（借閱紀錄）例外處理類別
 */
class CirculationException extends Exception
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
     * 負載資訊
     *
     * @var array
     */
    protected $payload;

    /**
     * 建構子
     *
     * @param string|null            $message  錯誤訊息
     * @param integer|string|double  $code     錯誤代碼
     */
    public function __construct(?string $message = null, mixed $code = 0, array $payload = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->payload = $payload;
    }

    /**
     * 取得負載資訊
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
