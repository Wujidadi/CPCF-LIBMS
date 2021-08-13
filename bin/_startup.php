<?php

/*
|--------------------------------------------------------------------------
| Bin Startup
|--------------------------------------------------------------------------
|
| Entry point of command line scripts.
|
*/

chdir(__DIR__);

require_once '../bootstrap/definitions.php';
require_once VENDOR_DIR . '/autoload.php';
require_once CONFIG_DIR . '/env.php';
require_once CONFIG_DIR . '/log.php';
require_once CONFIG_DIR . '/database.php';
require_once CONFIG_DIR . '/curl.php';
require_once BOOTSTRAP_DIR . '/app.php';
