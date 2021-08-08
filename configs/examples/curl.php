<?php

/**
 * Default number of seconds to wait of a cURL connection trying.
 *
 * @var integer
 */
define('CURL_CONNECT_TIMEOUT', 0);

/**
 * Default maximum number of seconds to allow cURL functions to execute.
 *
 * @var integer
 */
define('CURL_TIMEOUT', 60);

/**
 * Default value that whether or not to verifying the peer's certificate.
 *
 * @var boolean
 */
define('CURL_SSL_VERIFYPEER', false);

/**
 * Default value that whether or not to follow the location by "Location: " header.
 *
 * @var boolean
 */
define('CURL_FOLLOW_LOCATION', true);

/**
 * Default value that the response is returned as a string (`true`) or outputted directly (`false`).
 *
 * @var boolean
 */
define('CURL_RETURN_TRANSFER', true);
