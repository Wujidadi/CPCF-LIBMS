<?php

use App\Controllers\TestController;
use App\Controllers\BookController;

# 新增書籍資料
$Route->map('POST', '/api/book', function()
{
    BookController::getInstance()->addBook();
});

# 查詢書籍資料
$Route->map('GET', '/api/books/{field}/{value}', function($field, $value)
{
    BookController::getInstance()->getBooks($field, $value);
});

# 修改書籍資料
$Route->map('PATCH', '/api/book/{bookId}', function($bookId)
{
    BookController::getInstance()->editBook($bookId);
});

# 刪除書籍資料（軟刪除）
$Route->map('DELETE', '/api/book/{bookId}', function($bookId)
{
    BookController::getInstance()->deleteBook($bookId);
});

# 測試
$Route->map('GET', '/apt/test', function()
{
    TestController::getInstance()->main();
});
