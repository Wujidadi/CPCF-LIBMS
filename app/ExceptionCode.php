<?php

namespace App;

/**
 * 自訂錯誤碼類別
 */
class ExceptionCode
{
    /**
     * `BookData` 的字母值（`SumWord('BookData')`）
     *
     * @var integer
     */
    const BookData = 69;

    /**
     * `Input` 的字母值（`SumWord('Input')`）
     *
     * @var integer
     */
    const Input = 80;

    /**
     * `MemberData` 的字母值（`SumWord('MemberData')`）
     *
     * @var integer
     */
    const MemberData = 82;

    /**
     * `PDO` 的字母值（`SumWord('PDO')`）
     *
     * @var integer
     */
    const PDO = 35;

    /**
     * `Unfilled` 的字母值（`SumWord('unfilled')`）
     *
     * @var integer
     */
    const Unfilled = 83;
}
