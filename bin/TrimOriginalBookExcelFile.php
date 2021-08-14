<?php

/*
|--------------------------------------------------------------------------
| Trim Original Book Excel File
|--------------------------------------------------------------------------
|
| 整理原始書單 Excel 檔案。
|
*/

require_once '_startup.php';

use Libraries\Logger;
use App\Controllers\SourceFileController;

try
{
    $objTrimResult = SourceFileController::getInstance()->trimOriginalBookExcelFile(date('Ymd'));

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
