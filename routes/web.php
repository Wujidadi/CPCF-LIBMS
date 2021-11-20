<?php

use App\Controllers\Web\HomeController;

$Route->map('GET', '/', function()
{
    header('Location: /home');
    exit();
});

$Route->map('GET', '/home', function()
{
    HomeController::getInstance()->main();
});
