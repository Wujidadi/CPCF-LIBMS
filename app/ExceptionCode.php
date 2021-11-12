<?php

namespace App;

/**
 * 自訂錯誤碼類別
 */
class ExceptionCode
{
    /**
     * `PDO` 的字母值（`SumWord('PDO')`）
     *
     * @var integer
     */
    const PDO = 35;

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
     * `Unfilled` 的字母值（`SumWord('unfilled')`）
     *
     * @var integer
     */
    const Unfilled = 83;
}
