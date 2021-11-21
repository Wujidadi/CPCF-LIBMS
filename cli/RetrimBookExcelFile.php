<?php

chdir(__DIR__);
require_once '../bootstrap/bin.php';

/*
|--------------------------------------------------------------------------
| Retrim Book Excel File
|--------------------------------------------------------------------------
|
| 對經由 TrimOriginalBookExcelFile 整理過的書單檔案進行再修整
|
*/

use Libraries\Logger;
use App\Handlers\SourceFileHandler;

try
{
    $objTrimResult = SourceFileHandler::getInstance()->retrimBookExcelFile('20210813', date('Ymd'));

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
