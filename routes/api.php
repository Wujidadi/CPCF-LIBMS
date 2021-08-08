<?php

use App\Controllers\DemoController;

$Route->map('GET', '/api', [DemoController::getInstance(), 'api']);
