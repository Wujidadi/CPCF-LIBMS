<?php

/*
|--------------------------------------------------------------------------
| Retrim Book Excel File
|--------------------------------------------------------------------------
|
| 對經由 TrimOriginalBookExcelFile 整理過的書單檔案進行再修整
|
*/

require_once '_startup.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$strSourceFileName = STORAGE_DIR . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'books' . DIRECTORY_SEPARATOR . 'CPCF_Books_trimmed_20210813.xlsx';
$strTargetFileName = STORAGE_DIR . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'books' . DIRECTORY_SEPARATOR . 'CPCF_Books_retrimmed_20210813.xlsx';

$arrColumns = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L' ];

try
{
    $objFromSpreadsheet = IOFactory::load($strSourceFileName);

    $intFromSheetIndex = 0;
    $objFromWorkSheet = $objFromSpreadsheet->getSheet($intFromSheetIndex);

    $intMaxColumn = 12;
    $intMaxRow = 5219;

    $intRow = 0;

    $arrOutput[$intRow++] = $objFromWorkSheet->rangeToArray('A1:L1')[0];
    unset($arrOutput[0][2]);
    $arrOutput[0] = array_values($arrOutput[0]);

    for ($row = 2; $row <= $intMaxRow; $row++)
    {
        $intCol = 0;

        $arrMade = [];

        for ($col = 1; $col <= $intMaxColumn; $col++)
        {
            if ($col !== 3)
            {
                $strValue = trim($objFromWorkSheet->getCellByColumnAndRow($col, $row)->getCalculatedValue());

                $strValue = preg_replace('/ {2,}/', ' ', $strValue);

                $arrMade[$intCol++] = $strValue;
            }
        }

        $arrMade[3] = preg_replace('/-{2,}/', '──', $arrMade[3]);

        $arrOutput[$intRow++] = $arrMade;
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
            if ($strColumn === 'H')
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
        $objToWorkSheet->getColumnDimension('C')->setWidth(59.1875 + $dblMargin);
        $objToWorkSheet->getColumnDimension('D')->setWidth(52      + $dblMargin);
        $objToWorkSheet->getColumnDimension('E')->setWidth(51.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('F')->setWidth(38      + $dblMargin);
        $objToWorkSheet->getColumnDimension('G')->setWidth(24.5    + $dblMargin);
        $objToWorkSheet->getColumnDimension('H')->setWidth(17.8125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('I')->setWidth(41      + $dblMargin);
        $objToWorkSheet->getColumnDimension('J')->setWidth(22.3125 + $dblMargin);
        $objToWorkSheet->getColumnDimension('K')->setWidth(29.8125 + $dblMargin);

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
