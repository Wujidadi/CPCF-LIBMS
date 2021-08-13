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

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$strSourceFileName = '/Users/wujidadi/Library/Mobile Documents/com~apple~CloudDocs/毛毛蟲/20181024-整理毛毛蟲書單_寶芳.xls';
$strTargetFileName = '/Users/wujidadi/Library/Mobile Documents/com~apple~CloudDocs/毛毛蟲/CPCF_Books_trimmed_20210813.xlsx';

$arrColumns = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L' ];

try
{
    $objFromSpreadsheet = IOFactory::load($strSourceFileName);

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
    array_unshift($arrOutput[0], '序號');

    for ($row = 3; $row <= $intMaxRow; $row++)
    {
        $intPcs = 1;

        $arrMade = [];

        $bolthisRowIsEmpty = true;

        for ($col = 1; $col <= $intMaxColumn; $col++)
        {
            $strValue = trim($objFromWorkSheet->getCellByColumnAndRow($col, $row)->getCalculatedValue());

            if (!is_null($strValue) && mb_strlen($strValue) > 0)
            {
                $bolthisRowIsEmpty = false;
            }

            if ($col === 4 && preg_match('/ *[Xx×共](\d+)本*.*/u', $strValue, $matches))
            {
                $intPcs = (int) $matches[1];
                $strValue = preg_replace('/ *[Xx×共]\d+本*.*$/u', '', $strValue);
            }

            $arrMade[$col - 1] = $strValue;
        }

        if (preg_match('/^[12][90]\d{2}[01]\d[0-3]\d$/', $arrMade[8]) && (is_null($arrMade[10]) || mb_strlen($arrMade[10]) == 0))
        {
            $arrMade[10] = $arrMade[9];
            $arrMade[9] = $arrMade[8];
            $arrMade[8] = null;
        }

        $arrMade[7] = preg_replace('/--/', '-', $arrMade[7]);

        if (!$bolthisRowIsEmpty)
        {
            for ($i = 0; $i < $intPcs; $i++)
            {
                $arrOutput[$intRow] = $arrMade;
                array_unshift($arrOutput[$intRow], $intRow++);
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

        $objToSpreadsheet->getDefaultStyle()->getAlignment()
                         ->setHorizontal('left')
                         ->setVertical('center');
        $objToSpreadsheet->getDefaultStyle()->getFont()
                         ->setName('新細明體')
                         ->setSize(12);

        foreach ($arrColumns as $strColumn)
        {
            if ($strColumn === 'I')
            {
                $objToWorkSheet->getStyle($strColumn)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            }
            else
            {
                $objToWorkSheet->getStyle($strColumn)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            }
        }

        $objToWorkSheet->freezePane('A2');

        $objToWorkSheet->fromArray($arrOutput);

        $dblMargin = 0.8125;

        $objToWorkSheet->getColumnDimension('A')->setWidth( 5.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('B')->setWidth(13.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('C')->setWidth(20      + $dblMargin);
        $objToWorkSheet->getColumnDimension('D')->setWidth(55.8125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('E')->setWidth(52      + $dblMargin);
        $objToWorkSheet->getColumnDimension('F')->setWidth(51.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('G')->setWidth(38      + $dblMargin);
        $objToWorkSheet->getColumnDimension('H')->setWidth(24.5    + $dblMargin);
        $objToWorkSheet->getColumnDimension('I')->setWidth(17.8125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('J')->setWidth(41      + $dblMargin);
        $objToWorkSheet->getColumnDimension('K')->setWidth(22.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('L')->setWidth(29.8125 + $dblMargin);

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
