<?php

chdir(__DIR__);
require_once '../bootstrap/bin.php';

/*
|--------------------------------------------------------------------------
| Import Book Data to DB
|--------------------------------------------------------------------------
|
| 將經 RetrimBookExcelFile 及人工處理過的最終書籍原資料匯入資料庫
|
*/

use Libraries\Logger;
use App\Handlers\SourceFileHandler;

try
{
    $objTrimResult = SourceFileHandler::getInstance()->insertBookDataToDB('CPCF_Books_retrimmed_20210813.xlsx', null, 1200, 200);

    if ($objTrimResult->status)
    {
        echo "{$objTrimResult->message}\n";
    }
    else
    {
        echo "\033[31;1m{$objTrimResult->message}\033[0m\n";
    }
}
catch (Throwable $th)
{
    $strErrorMessage = "{$th->getMessage()} ({$th->getCode()})";
    echo "\033[31;1m{$strErrorMessage}\033[0m\n";
    exit(1);
}

exit(0);
