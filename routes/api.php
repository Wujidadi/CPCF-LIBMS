<?php

use App\Controllers\BookController;

# 查詢書籍資料
$Route->map('GET', '/api/books/{field}/{value}', function($field, $param)
{
    BookController::getInstance()->getBooks($field, $param);
});
