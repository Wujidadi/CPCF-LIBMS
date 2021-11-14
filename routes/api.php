<?php

use App\Controllers\TestController;
use App\Controllers\BookController;
use App\Controllers\MemberController;
use App\Controllers\CirculationController;

if (IS_DEV)
{
    # 測試
    $Route->map('GET', '/apt/test', function()
    {
        TestController::getInstance()->main();
    });
}

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

# 查詢書籍借閱紀錄
$Route->map('GET', '/api/records/book/{bookId}', function($bookId)
{
    CirculationController::getInstance()->getRecords($bookId);
});

# 查詢借閱者借閱紀錄
$Route->map('GET', '/api/records/member/{memberId}', function($memberId)
{
    CirculationController::getInstance()->getRecords($memberId, true);
});

# 查詢書籍當前流通狀態
$Route->map('GET', '/api/status/book/{bookId}', function($bookId)
{
    CirculationController::getInstance()->getBookStatus($bookId);
});

# 借書
$Route->map('POST', '/api/borrow/book/{bookId}/{memberId}', function($bookId, $memberId)
{
    CirculationController::getInstance()->borrow($bookId, $memberId);
});

# 還書
$Route->map('PATCH', '/api/return/book/{bookId}', function($bookId)
{
    CirculationController::getInstance()->return($bookId);
});

# 書籍借閱排行榜
$Route->map('GET', '/api/rank/books', function($bookId)
{
    //
});

# 借閱者排行榜
$Route->map('GET', '/api/rank/members', function($bookId)
{
    //
});
