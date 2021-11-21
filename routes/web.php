<?php

use App\Controllers\Web\MainController;

$Route->map('GET', '/', function()
{
    header('Location: /home');
    exit();
});

$Route->map('GET', '/home', function()
{
    MainController::getInstance()->home();
});

$Route->map('GET', '/circulation', function()
{
    MainController::getInstance()->circulation();
});

$Route->map('GET', '/books', function()
{
    MainController::getInstance()->books();
});

$Route->map('GET', '/members', function()
{
    MainController::getInstance()->members();
});
