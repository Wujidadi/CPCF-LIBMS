<?php

/*
|--------------------------------------------------------------------------
| Index
|--------------------------------------------------------------------------
|
| Entry of the web of this project.
|
*/

# Error displaying and reporting
// ini_set('display_errors','1');
// error_reporting(E_ALL);

# PHP decimal precision
ini_set('precision', 16);

chdir(__DIR__);

# Basic definitions
require_once '../bootstrap/definitions.php';

# Framework tools
require_once BOOTSTRAP_DIR . '/app.php';

# URI holder
require_once BOOTSTRAP_DIR . '/uriholder.php';

# Routes dispatcher
require_once ROUTE_DIR . '/dispatcher.php';

# All undefined routes lead to 404
header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
exit;
