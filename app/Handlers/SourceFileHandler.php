<?php

namespace App\Handlers;

use stdClass;
use Throwable;
use Libraries\Logger;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\BookModel;

/**
 * 原始資料檔處理類別
 */
class SourceFileHandler
{
    /**
     * 原始資料檔存放路徑
     *
     * @var string
     */
    protected $_srcStoragePath = STORAGE_DIR . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'books' . DIRECTORY_SEPARATOR;

    /**
     * 原始書單 Excel 檔有效欄位名稱
     *
     * @var string[]
     */
    protected $_srcBookExcelColumns = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L' ];

    /**
     * 欄寬與程式設定值的誤差
     *
     * @var integer
     */
    const COLUMN_MARGIN = 0.8125;

    protected $_className;

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance()
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 對原始書單 Excel 檔進行初步整理
     *
     * @param  string|null  $fileSuffix  輸出檔名後綴：建議為當天日期（`Ymd` 格式）
     * @return object
     */
    public function trimOriginalBookExcelFile($fileSuffix = null)
    {
        $strFunction = __FUNCTION__;

        $strFileSuffix = !is_null($fileSuffix) ? "_{$fileSuffix}" : '';

        $strSourceFileName = "{$this->_srcStoragePath}20181024-整理毛毛蟲書單_寶芳.xls";
        $strTargetFileName = "{$this->_srcStoragePath}CPCF_Books_trimmed{$strFileSuffix}.xlsx";

        $objReturns = new stdClass;
        $objReturns->status  = true;
        $objReturns->message = '';

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

                $arrColumn = $this->_srcBookExcelColumns;
                foreach ($arrColumn as $strColumn)
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

                $objToWorkSheet->getColumnDimension('A')->setWidth( 5.1875 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('B')->setWidth(13.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('C')->setWidth(20      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('D')->setWidth(59.1875 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('E')->setWidth(52      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('F')->setWidth(51.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('G')->setWidth(38      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('H')->setWidth(24.5    + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('I')->setWidth(17.8125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('J')->setWidth(41      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('K')->setWidth(22.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('L')->setWidth(29.8125 + self::COLUMN_MARGIN);

                $objToWorkSheet->setSelectedCells('A1');

                $objWriter = IOFactory::createWriter($objToSpreadsheet, 'Xlsx');
                $objWriter->save($strTargetFileName);
            }

            $objReturns->message = "原始檔案：{$strSourceFileName}\n輸出檔案：{$strTargetFileName}";

            $strLogMessage = "{$this->_className}::{$strFunction} {$objReturns->message}";
            Logger::getInstance()->logInfo($strLogMessage);
        }
        catch (Exception $ex)
        {
            $exCode = $ex->getCode();
            $exMsg  = $ex->getMessage();

            $objReturns->status  = false;
            $objReturns->message = "Exception: ({$exCode}) {$exMsg}";

            $strLogMessage = "{$this->_className}::{$strFunction} {$objReturns->message}";
            Logger::getInstance()->logError($strLogMessage);
        }

        return $objReturns;
    }

    /**
     * 對經由 `trimOriginalBookExcelFile` 方法整理過的書單 Excel 檔進行再整理
     *
     * @param  string|null  $srcFileSuffix  原始檔名後綴
     * @param  string|null  $dstFileSuffix  輸出檔名後綴：建議為當天日期（`Ymd` 格式）
     * @return object
     */
    public function retrimBookExcelFile($srcFileSuffix = null, $dstFileSuffix = null)
    {
        $strFunction = __FUNCTION__;

        $strSourceFileSuffix = !is_null($srcFileSuffix) ? "_{$srcFileSuffix}" : '';
        $strTargetFileSuffix = !is_null($dstFileSuffix) ? "_{$dstFileSuffix}" : '';

        $strSourceFileName = "{$this->_srcStoragePath}CPCF_Books_trimmed{$strSourceFileSuffix}.xlsx";
        $strTargetFileName = "{$this->_srcStoragePath}CPCF_Books_retrimmed{$strTargetFileSuffix}.xlsx";

        $objReturns = new stdClass;
        $objReturns->status  = true;
        $objReturns->message = '';

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

                $arrColumn = $this->_srcBookExcelColumns;
                unset($arrColumn[11]);
                foreach ($arrColumn as $strColumn)
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

                $objToWorkSheet->getColumnDimension('A')->setWidth( 5.1875 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('B')->setWidth(13.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('C')->setWidth(59.1875 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('D')->setWidth(52      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('E')->setWidth(51.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('F')->setWidth(38      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('G')->setWidth(24.5    + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('H')->setWidth(17.8125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('I')->setWidth(41      + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('J')->setWidth(22.3125 + self::COLUMN_MARGIN);
                $objToWorkSheet->getColumnDimension('K')->setWidth(29.8125 + self::COLUMN_MARGIN);

                $objToWorkSheet->setSelectedCells('A1');

                $objWriter = IOFactory::createWriter($objToSpreadsheet, 'Xlsx');
                $objWriter->save($strTargetFileName);
            }

            $objReturns->message = "原始檔案：{$strSourceFileName}\n輸出檔案：{$strTargetFileName}";

            $strLogMessage = "{$this->_className}::{$strFunction} {$objReturns->message}";
            Logger::getInstance()->logInfo($strLogMessage);
        }
        catch (Throwable $ex)
        {
            $exCode = $ex->getCode();
            $exMsg  = $ex->getMessage();

            $objReturns->status  = false;
            $objReturns->message = "Exception: ({$exCode}) {$exMsg}";

            $strLogMessage = "{$this->_className}::{$strFunction} {$objReturns->message}";
            Logger::getInstance()->logError($strLogMessage);
        }

        return $objReturns;
    }

    /**
     * 將經 `retrimBookExcelFile` 及人工處理過的最終書籍原資料匯入資料庫
     *
     * @param  string        $strFile    書單 Excel 檔名   
     * @param  integer|null  $intCount   匯入筆數：預設值為 `null` 即全部匯入
     * @param  integer       $intOffset  偏移量：從第幾筆開始匯入，預設為 `0` 即第 1 筆
     * @param  integer       $intGroup   分組：SQL insert 時每次執行的筆數，預設為 `200`
     * @return object
     */
    public function insertBookDataToDB($strFile, $intCount = null, $intOffset = 0, $intGroup = 200)
    {
        $strFunction = __FUNCTION__;

        $strSourceFileName = "{$this->_srcStoragePath}{$strFile}";

        $objReturns = new stdClass;
        $objReturns->status  = true;
        $objReturns->message = '';

        try
        {
            $objFromSpreadsheet = IOFactory::load($strSourceFileName);

            $intFromSheetIndex = 0;
            $objFromWorkSheet = $objFromSpreadsheet->getSheet($intFromSheetIndex);

            $intMaxColumn = 11;
            $intMaxRow = 5219;

            $intRow = 2;

            $intBeginRow = $intRow + $intOffset;
            $intEndRow   = is_null($intCount) ? $intMaxRow : $intCount + $intBeginRow - 1;
            if ($intEndRow > $intMaxRow)
            {
                $intEndRow = $intMaxRow;
            }

            $intInserted = 0;

            $intGroupCounter = 0;
            $intGroupRow     = 0;

            for ($row = $intBeginRow; $row <= $intEndRow; $row++)
            {
                $intCol = 0;

                $arrMade = [];

                for ($col = 1; $col <= $intMaxColumn; $col++)
                {
                    $strValue = trim($objFromWorkSheet->getCellByColumnAndRow($col, $row)->getValue());
                    $arrMade[$intCol++] = ($strValue == '') ? null : $strValue;
                }

                $arrMade[7] = str_replace('-', '', $arrMade[7]);

                if (!is_null($arrMade[9]))
                {
                    $arrMade[9] = date('Y-m-d', strtotime($arrMade[9]));
                }

                $arrParams = [
                    'No'          => $arrMade[0],
                    'Name'        => $arrMade[3],
                    'Author'      => $arrMade[4],
                    'Illustrator' => $arrMade[5],
                    'Translator'  => $arrMade[6],
                    'Series'      => $arrMade[8],
                    'Publisher'   => $arrMade[2],
                    'StorageDate' => $arrMade[9],
                    'StorageType' => is_null($arrMade[10]) ? null : 2,
                    'Deleted'     => is_null($arrMade[1]) ? 0 : 1,    // 使用 0 和 1 分別代替 false 和 true，避免 PDOStatement::bindParam 將 false 轉為空字串
                    'Notes'       => $this->_makeStorageNotes($arrMade[10]),
                    'ISN'         => $arrMade[7]
                ];

                if ($intGroupRow === 0)
                {
                    BookModel::getInstance()->beginTransaction();
                }

                BookModel::getInstance()->addOne($arrParams);
                $intInserted++;

                $intGroupRow++;
                if ($intGroupRow === $intGroup || $row === $intEndRow)
                {
                    BookModel::getInstance()->commit();
                    $intGroupCounter++;
                    $intGroupRow = 0;
                }
            }

            $objReturns->message = "新增 {$intInserted} 筆書籍資料";

            $strAssignedCount = is_null($intCount) ? '全部書籍資料' : " {$intInserted} 筆書籍資料";
            $intBeginRow = $intOffset + 1;
            $strLogMessage = "來源檔案：{$strSourceFileName}；指定新增{$strAssignedCount}，從原檔案第 {$intBeginRow} 筆起，每 {$intGroup} 筆寫入資料庫；實際{$objReturns->message}";
            Logger::getInstance()->logInfo($strLogMessage);
        }
        catch (Throwable $ex)
        {
            $exCode = $ex->getCode();
            $exMsg  = $ex->getMessage();

            $objReturns->status  = false;
            $objReturns->message = "Exception: ({$exCode}) {$exMsg}";

            $strLogMessage = "{$this->_className}::{$strFunction} {$objReturns->message}";
            Logger::getInstance()->logError($strLogMessage);
        }

        return $objReturns;
    }

    /**
     * 轉換入庫事由附註
     *
     * @param  string  $strValue  原始入庫事由
     * @return string
     */
    protected function _makeStorageNotes($strValue)
    {
        switch ($strValue)
        {
            case null:
                return null;

            case '楊老師藏書':
            case '今泉吉晴解說':
                return $strValue;

            default:
                return "{$strValue}贈書";
        }
    }
}
