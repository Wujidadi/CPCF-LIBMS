<?php

/*
|--------------------------------------------------------------------------
| URI Holder
|--------------------------------------------------------------------------
|
| Hold the URI of HTTP requests.
|
*/

/**
 * HTTP request URI (without query parameters）.
 *
 * @var string
 */
$requestUri = preg_replace(['/\?.*/', '/#.*/'], '', $_SERVER['REQUEST_URI']);

/**
 * HTTP request method.
 *
 * @var string
 */
$requestMethod = $_SERVER['REQUEST_METHOD'];

# Disable .php extension name for request
if (REWRITE_PHP_EXT && preg_match('/.+.php$/', $requestUri))
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    exit;
}
# Remove trailing slash
else if (preg_match('/\/$/', $requestUri))
{
    $requestUri = rtrim($requestUri, '/');
}
