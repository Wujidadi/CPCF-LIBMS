<?php

/*
|--------------------------------------------------------------------------
| APP Bootstrap
|--------------------------------------------------------------------------
|
| Entry point of main app (web).
|
*/

# Autoload
require_once VENDOR_DIR . '/autoload.php';

# Configurations
require_once CONFIG_DIR . '/env.php';
require_once CONFIG_DIR . '/log.php';
require_once CONFIG_DIR . '/database.php';
require_once CONFIG_DIR . '/curl.php';

# Framework tools
require_once BOOTSTRAP_DIR . '/framework.php';
