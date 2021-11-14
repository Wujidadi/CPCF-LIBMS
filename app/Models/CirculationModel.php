<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;
use Libraries\Logger;
use App\ExceptionCode;
use App\Model;

/**
 * 書籍借閱紀錄資料表模型
 */
class CirculationModel extends Model
{
    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $_tableName = 'Circulation';

    protected $_className;

    protected static $_uniqueInstance = null;

    /** @return self */
    public static function getInstance(): self
    {
        if (self::$_uniqueInstance == null) self::$_uniqueInstance = new self();
        return self::$_uniqueInstance;
    }

    protected function __construct()
    {
        parent::__construct('DEFAULT');
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 創建單筆借閱紀錄
     *
     * @param  integer  $bookId    書籍 ID
     * @param  integer  $memberId  借閱者/會員 ID
     * @return integer
     */
    public function insertRecord(int $bookId, int $memberId): int
    {
        $functionName = __FUNCTION__;

        $time = MsTime();

        try
        {
            $sql = <<<SQL
            INSERT INTO public."{$this->_tableName}" (
                "BookId",
                "MemberId",
                "BorrowedAt"
            ) VALUES (
                :bookId,
                :memberId,
                :borrowedAt
            )
            SQL;

            $bind = [
                'bookId'     => $bookId,
                'memberId'   => $memberId,
                'borrowedAt' => $time
            ];

            return $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }

    /**
     * 依書籍 ID 查詢借閱紀錄
     *
     * @param  integer  $bookId  書籍 ID
     * @param  string   $from    查詢起始時間
     * @param  string   $to      查詢終止時間
     * @param  integer  $limit   查詢資料限制筆數
     * @param  integer  $offset  查詢資料偏移量
     * @return array
     */
    public function selectHistoryByBookId(int $bookId, string $from, string $to, int $limit, int $offset): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $sql = <<<SQL
            SELECT
                *
            FROM public."{$this->_tableName}"
            WHERE
                "BookId" = :bookId AND
                (
                    "BorrowedAt" BETWEEN :from AND :to OR
                    "ReturnedAt" BETWEEN :from AND :to
                )
            LIMIT :limit
            OFFSET :offset
            SQL;

            $bind = [
                'bookId' => $bookId,
                'from'   => $from,
                'to'     => $to,
                'limit'  => [ $limit,  PDO::PARAM_INT ],
                'offset' => [ $offset, PDO::PARAM_INT ]
            ];

            return $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }

    /**
     * 依借閱者/會員 ID 查詢借閱紀錄
     *
     * @param  integer  $bookId  借閱者/會員 ID
     * @param  string   $from    查詢起始時間
     * @param  string   $to      查詢終止時間
     * @param  integer  $limit   查詢資料限制筆數
     * @param  integer  $offset  查詢資料偏移量
     * @return array
     */
    public function selectHistoryByMemberId(int $memberId, string $from, string $to, int $limit, int $offset): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $sql = <<<SQL
            SELECT
                *
            FROM public."{$this->_tableName}"
            WHERE
                "MemberId" = :memberId AND
                (
                    "BorrowedAt" BETWEEN :from AND :to OR
                    "ReturnedAt" BETWEEN :from AND :to
                )
            LIMIT :limit
            OFFSET :offset
            SQL;

            $bind = [
                'memberId' => $memberId,
                'from'   => $from,
                'to'     => $to,
                'limit'    => [ $limit,  PDO::PARAM_INT ],
                'offset'   => [ $offset, PDO::PARAM_INT ]
            ];

            return $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }

    /**
     * 依書籍 ID 查詢最新一筆未歸還的紀錄
     *
     * @param  integer  $bookId  書籍 ID
     * @return array
     */
    public function selectLastUnreturnedRecord(int $bookId): array
    {
        $functionName = __FUNCTION__;

        try
        {
            $sql = <<<SQL
            SELECT
                *
            FROM public."{$this->_tableName}"
            WHERE
                "BookId" = :bookId AND
                "ReturnedAt" IS NULL
            ORDER BY
                "BorrowedAt" DESC,
                "Id" DESC
            LIMIT 1
            SQL;

            $bind = [
                'bookId' => $bookId
            ];

            return $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }

    /**
     * 更新單筆借閱紀錄
     *
     * 由於 `Circulation` 資料表中只有歸還時間（`ReturnedAt`）欄位有更新意義，本方法事實上專用於還書
     *
     * @param  integer  $id  借閱紀錄 ID
     * @return integer
     */
    public function updateRecord(int $id): int
    {
        $functionName = __FUNCTION__;

        $time = MsTime();

        try
        {
            $sql = <<<SQL
            UPDATE public."{$this->_tableName}" SET
                "ReturnedAt" = :returnedAt
            WHERE
                "Id" = :id
            SQL;

            $bind = [
                'id'         => $id,
                'returnedAt' => $time
            ];

            return $this->_db->query($sql, $bind);
        }
        catch (PDOException $ex)
        {
            if ($this->_db->inTransaction())
            {
                $this->_db->rollBack();
            }

            $exCode = $ex->getCode();
            $exMessage = $ex->getMessage();

            $logMessage = "{$this->_className}::{$functionName} PDOException({$exCode}): {$exMessage}";
            Logger::getInstance()->logError($logMessage);

            throw new Exception($exMessage, ExceptionCode::PDO);
        }
    }
}
