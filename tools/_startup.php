<?php

/*
|--------------------------------------------------------------------------
| Tools Startup
|--------------------------------------------------------------------------
|
| Entry point of command line tools
|
*/

chdir(__DIR__);

require_once '../bootstrap/definitions.php';
require_once VENDOR_DIR . '/autoload.php';
require_once BOOTSTRAP_DIR . '/app.php';
