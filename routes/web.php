<?php

use App\Controllers\HomeController;

$Route->map('GET', '/', function()
{
    header('Location: /home');
    exit();
});

$Route->map('GET', '/home', function()
{
    HomeController::getInstance()->main();
});
