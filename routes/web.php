<?php

use App\Controllers\DemoController;

$Route->map('GET', '/', function()
{
    view('Demo.Home', [
        'title' => 'Tarascanta',
        'mainMessage' => 'Welcome to Tarascanta!'
    ]);
});

$Route->map('GET', '/demo', [DemoController::getInstance(), 'main']);
