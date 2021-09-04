<?php

chdir(__DIR__);
require_once '../bootstrap/bin.php';

/*
|--------------------------------------------------------------------------
| Trim Original Book Excel File
|--------------------------------------------------------------------------
|
| 整理原始書單 Excel 檔案。
|
*/

use Libraries\Logger;
use App\Handlers\SourceFileHandler;

try
{
    $objTrimResult = SourceFileHandler::getInstance()->trimOriginalBookExcelFile(date('Ymd'));

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
