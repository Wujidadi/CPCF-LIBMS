<?php

namespace Libraries\HTTP;

/**
 * HTTP response handling class.
 */
class Response
{
    /**
     * HTTP status code.
     *
     * @var integer
     */
    protected $_httpStatusCode;

    /**
     * HTTP status message.
     *
     * @var string
     */
    protected $_httpStatusMessage;

    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_uniqueInstance = null;

    /**
     * Get the instance of this class.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    /**
     * Constructor.
     *
     * @return void
     */
    protected function __construct() {}

    /**
     * Set the HTTP status code.
     *
     * @param  integer  $code  HTTP status code.
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->_httpStatusCode = $code;
        $this->_httpStatusMessage = $this->_getMessage($code);

        return $this;
    }

    /**
     * Get the HTTP status code.
     *
     * @return integer
     */
    public function getCode(): int
    {
        return (is_null($this->_httpStatusCode) || $this->_httpStatusCode === '') ? 200 : $this->_httpStatusCode;
    }

    /**
     * Get the message by HTTP status code.
     *
     * @param  integer  $code  HTTP status code.
     * @return string
     */
    protected function _getMessage(int $code): string
    {
        switch ($code)
        {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 102: $text = 'Processing'; break;
            case 103: $text = 'Early Hints'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 207: $text = 'Multi-Status'; break;
            case 208: $text = 'Already Reported'; break;
            case 226: $text = 'IM Used'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Found'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            // case 306: $text = 'Switch Proxy'; break;
            case 307: $text = 'Temporary Redirect'; break;
            case 308: $text = 'Permanent Redirect'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Timeout'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Payload Too Large'; break;
            case 414: $text = 'URI Too Long'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 416: $text = 'Range Not Satisfiable'; break;
            case 417: $text = 'Expectation Failed'; break;
            case 418: $text = 'I\'m a teapot'; break;
            case 421: $text = 'Misdirected Request'; break;
            case 422: $text = 'Unprocessable Entity'; break;
            case 423: $text = 'Locked'; break;
            case 424: $text = 'Failed Dependency'; break;
            case 425: $text = 'Too Early'; break;
            case 426: $text = 'Upgrade Required'; break;
            case 428: $text = 'Precondition Required'; break;
            case 429: $text = 'Too Many Requests'; break;
            case 431: $text = 'Request Header Fields Too Large'; break;
            case 451: $text = 'Unavailable For Legal Reasons'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Timeout'; break;
            case 505: $text = 'HTTP Version Not Supported'; break;
            case 506: $text = 'Variant Also Negotiates'; break;
            case 507: $text = 'Insufficient Storage'; break;
            case 508: $text = 'Loop Detected'; break;
            case 510: $text = 'Not Extended'; break;
            case 511: $text = 'Network Authentication Required'; break;
            default: exit('Unknown http status code "' . htmlentities($code) . '"');
        }

        return $text;
    }

    /**
     * Replace default HTTP status message.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->_httpStatusMessage = $message;

        return $this;
    }

    /**
     * Output the given content with HTTP status.
     *
     * @param  string  $content  Content to be output.
     * @return void
     */
    public function output(string $content = ''): void
    {
        header('Content-Type: application/json');

        if (!is_null($this->_httpStatusCode) && $this->_httpStatusCode != '')
        {
            header("{$_SERVER['SERVER_PROTOCOL']} {$this->_httpStatusCode} {$this->_httpStatusMessage}");
        }

        if ($content != '')
        {
            echo $content;
        }
    }
}
