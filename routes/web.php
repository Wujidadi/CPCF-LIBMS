<?php

use App\Controllers\Web\MainController;

# 根目錄轉跳首頁
$Route->map('GET', '/', function()
{
    header('Location: /home');
    exit();
});

# 首頁
$Route->map('GET', '/home', function()
{
    MainController::getInstance()->home();
});

# 借書
$Route->map('GET', '/circulation/borrow', function()
{
    MainController::getInstance()->borrow();
});

# 還書
$Route->map('GET', '/circulation/return', function()
{
    MainController::getInstance()->return();
});

# 圖書管理作業
$Route->map('GET', '/books', function()
{
    MainController::getInstance()->books();
});

# 新增圖書
$Route->map('GET', '/book/add', function()
{
    MainController::getInstance()->addBook();
});

# 借閱者管理作業
$Route->map('GET', '/members', function()
{
    MainController::getInstance()->members();
});
