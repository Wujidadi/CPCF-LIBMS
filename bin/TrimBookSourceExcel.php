<?php

/*
|--------------------------------------------------------------------------
| Get Excel Content Test
|--------------------------------------------------------------------------
|
| 取得 Excel XLS 檔案內容測試。
|
*/

require_once '_startup.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$strExcelFileName = '/Users/wujidadi/Library/Mobile Documents/com~apple~CloudDocs/毛毛蟲/20181024-整理毛毛蟲書單_寶芳.xls';
$strTargetFileName = '/Users/wujidadi/Downloads/CPCF_Books.xlsx';

$arrColumns = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K' ];

try
{
    $objFromSpreadsheet = IOFactory::load($strExcelFileName);

    $intFromSheetIndex = 0;
    $objFromWorkSheet = $objFromSpreadsheet->getSheet($intFromSheetIndex);

    // $strMaxColumn = $objFromWorkSheet->getHighestColumn();
    // $intMaxColumn = Coordinate::columnIndexFromString($strMaxColumn);
    // $intMaxRow = $objFromWorkSheet->getHighestRow();

    $intMaxColumn = 11;
    $intMaxRow = 4795;

    $intRow = 0;

    $arrOutput[$intRow++] = $objFromWorkSheet->rangeToArray('A2:K2')[0];
    $arrOutput[0][4] = '作者';

    for ($row = 3; $row <= $intMaxRow; $row++)
    {
        // if ($row === 64) exit;
        $intPcs = 1;

        $arrMade = [];

        $bolthisRowIsEmpty = true;

        for ($col = 1; $col <= $intMaxColumn; $col++)
        {
            $value = trim($objFromWorkSheet->getCellByColumnAndRow($col, $row)->getCalculatedValue());
            $oldVal = $value;
            if (!is_null($value) && mb_strlen($value) > 0)
            {
                $bolthisRowIsEmpty = false;
            }

            if ($col === 4 && preg_match('/ *[Xx×共](\d+)本*.*/u', $value, $matches))
            {
                $intPcs = (int) $matches[1];
                $value = preg_replace('/ *[Xx×共]\d+本*.*$/u', '', $value);
            }

            $arrMade[$col - 1] = $value;
        }

        if (preg_match('/^[12][90]\d{2}[01]\d[0-3]\d$/', $arrMade[8]) && (is_null($arrMade[10]) || mb_strlen($arrMade[10]) == 0))
        {
            $arrMade[10] = $arrMade[9];
            $arrMade[9] = $arrMade[8];
            $arrMade[8] = null;
        }

        if (!$bolthisRowIsEmpty)
        {
            for ($i = 0; $i < $intPcs; $i++)
            {
                $arrOutput[$intRow++] = $arrMade;
            }
        }
    }

    if (count($arrOutput) > 0)
    {
        $objToSpreadsheet = new Spreadsheet;

        $objToSpreadsheet->removeSheetByIndex(0);
        $objToSpreadsheet->createSheet(0)->setTitle('Books');
        $objToSpreadsheet->setActiveSheetIndex(0);

        $objToWorkSheet = $objToSpreadsheet->getActiveSheet();

        foreach ($arrColumns as $strColumn)
        {
            $objToWorkSheet->getStyle($strColumn)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        $objToWorkSheet->fromArray($arrOutput);

        $dblMargin = 0.625;

        $objToWorkSheet->getColumnDimension('A')->setWidth(12.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('B')->setWidth(18.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('C')->setWidth(51.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('D')->setWidth(47.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('E')->setWidth(44      + $dblMargin);
        $objToWorkSheet->getColumnDimension('F')->setWidth(34.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('G')->setWidth(22.5    + $dblMargin);
        $objToWorkSheet->getColumnDimension('H')->setWidth(17.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('I')->setWidth(37.8125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('J')->setWidth(20.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('K')->setWidth(27.8125 + $dblMargin);

        $objToWorkSheet->setSelectedCells('A1');

        $objWriter = IOFactory::createWriter($objToSpreadsheet, 'Xlsx');
        $objWriter->save($strTargetFileName);
    }
}
catch (Exception $ex)
{
    $exCode = $ex->getCode();
    $exMsg  = $ex->getMessage();
    echo "\033[31;1mException: ({$exCode}) {$exMsg}\033[0m\n";
}
