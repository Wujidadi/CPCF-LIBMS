<?php

namespace App;

/**
 * 自訂錯誤碼類別
 *
 * 錯誤碼來自常數名稱的字母值（如：`SumWord('PDO')`）
 */
class ExceptionCode
{
    const PDO             = 35;
    const BookData        = 69;
    const Input           = 80;
    const MemberData      = 82;
    const Unfilled        = 83;
    const BookBorrowed    = 143;
    const BookNotExist    = 169;
    const MemberNotExist  = 182;
    const BookNotBorrowed = 192;
}
