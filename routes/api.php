<?php

use App\Controllers\TestController;
use App\Controllers\BookController;
use App\Controllers\MemberController;

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

# 新增借閱者資料
$Route->map('POST', '/api/member', function()
{
    MemberController::getInstance()->addMember();
});

# 查詢借閱者資料
$Route->map('GET', '/api/members/{field}/{value}', function($field, $value)
{
    MemberController::getInstance()->getMembers($field, $value);
});

# 修改借閱者資料
$Route->map('PATCH', '/api/member/{memberId}', function($memberId)
{
    MemberController::getInstance()->editMember($memberId);
});

# 禁用借閱者
$Route->map('PATCH', '/api/member/{memberId}/disable', function($memberId)
{
    MemberController::getInstance()->disableMember($memberId);
});

# 啟用借閱者
$Route->map('PATCH', '/api/member/{memberId}/enable', function($memberId)
{
    MemberController::getInstance()->disableMember($memberId, false);
});

# 測試
$Route->map('GET', '/apt/test', function()
{
    TestController::getInstance()->main();
});
