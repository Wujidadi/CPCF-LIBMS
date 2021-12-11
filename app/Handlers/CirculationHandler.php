<?php

namespace App\Handlers;

use Exception;
use Libraries\Logger;
use App\ExceptionCode;
use App\Exceptions\CirculationException;
use App\Models\CirculationModel;
use App\Models\BookModel;
use App\Models\MemberModel;

/**
 * 書籍流通（借還書）資料處理器
 */
class CirculationHandler
{
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
        $this->_className = basename(__FILE__, '.php');
    }

    /**
     * 查詢借閱紀錄
     *
     * @param  integer   $id           書籍或借閱者/會員 ID
     * @param  boolean   $getByMember  是否以借閱者/會員 ID 進行查詢
     * @param  double[]  $range        起訖時間
     * @param  integer   $limit        查詢資料限制筆數
     * @param  integer   $offset       查詢資料偏移量
     * @return array
     */
    public function getRecords(int $id, bool $getByMember, array $range, int $limit, int $offset): array
    {
        $functionName = __FUNCTION__;

        $from = MsTime($range[0]);
        $to = MsTime($range[1]);

        if ($getByMember)
        {
            $result = CirculationModel::getInstance()->selectHistoryByMemberId($id, $from, $to, $limit, $offset);
        }
        else
        {
            $result = CirculationModel::getInstance()->selectHistoryByBookId($id, $from, $to, $limit, $offset);
        }

        # 移除時區標記
        $bookList = array_map(function($row) {
            $row['BorrowedAt'] = preg_replace(TimeZoneSuffix, '', $row['BorrowedAt']);
            $row['ReturnedAt'] = preg_replace(TimeZoneSuffix, '', $row['ReturnedAt']);
            return $row;
        }, $result);

        return [
            'Total' => count($result),
            'List'  => $bookList
        ];
    }

    /**
     * 依借閱者/會員編號查詢未歸還（借出中）書籍資料
     *
     * @param  string  $memberNo  借閱者編號
     * @return array
     */
    public function getBorrowingRecordsByMember(string $memberNo): array
    {
        $functionName = __FUNCTION__;

        $borrower = [
            'Id'         => null,
            'No'         => $memberNo,
            'Name'       => null,
            'Membership' => null,
            'Disabled'   => null
        ];
        $records = [];

        $result = CirculationModel::getInstance()->selectBorrowingRecordsByMemberNo($memberNo);
        if (count($result) > 0)
        {
            $borrower = [
                'Id'         => $result[0]['BorrowerId'],
                'No'         => $result[0]['BorrowerNo'],
                'Name'       => $result[0]['BorrowerName'],
                'Membership' => $result[0]['BorrowerMembership'],
                'Disabled'   => $result[0]['BorrowerDisabled']
            ];
            foreach ($result as $row)
            {
                $records[] = [
                    'Id' => $row['RecordId'],
                    'Book' => [
                        'Id'           => $row['BookId'],
                        'No'           => $row['BookNo'],
                        'Name'         => $row['BookName'],
                        'OriginalName' => $row['OriginalBookName'],
                        'Author'       => $row['Author'],
                        'Illustrator'  => $row['Illustrator'],
                        'Editor'       => $row['Editor'],
                        'Translator'   => $row['Translator'],
                        'Series'       => $row['Series'],
                        'Publisher'    => $row['Publisher'],
                        'Deleted'      => $row['Deleted'],
                        'CategoryId'   => $row['CategoryId'],
                        'LocationId'   => $row['LocationId']
                    ],
                    # 移除時區標記
                    'BorrowedAt' => preg_replace(TimeZoneSuffix, '', $row['BorrowedAt']),
                    'ReturnedAt' => preg_replace(TimeZoneSuffix, '', $row['ReturnedAt'])
                ];
            }
        }
        else
        {
            $result = MemberModel::getInstance()->selectOneByMemberNo($memberNo);
            if (count($result) > 0)
            {
                $borrower = [
                    'Id'         => $result[0]['Id'],
                    'No'         => $result[0]['No'],
                    'Name'       => $result[0]['Name'],
                    'Membership' => $result[0]['Membership'],
                    'Disabled'   => $result[0]['Disabled']
                ];
            }
        }

        return [
            'Total'    => count($records),
            'Borrower' => $borrower,
            'Record'   => $records
        ];
    }

    /**
     * 查詢書籍當前流通狀態
     *
     * @param  integer  $bookId  書籍 ID
     * @return array
     */
    public function getBookStatus(int $bookId): array
    {
        $functionName = __FUNCTION__;

        $data = [
            'Borrowed' => false,
            'RecordId' => null,
            'Borrower' => null,
            'BorrowedAt' => null
        ];

        $result = CirculationModel::getInstance()->selectLastUnreturnedRecord($bookId);
        if (count($result) > 0)
        {
            $data = [
                'Borrowed' => true,
                'RecordId' => $result[0]['Id'],
                'Borrower' => $result[0]['MemberId'],
                'BorrowedAt' => $result[0]['BorrowedAt']
            ];
        }

        return $data;
    }

    /**
     * 借書
     *
     * @param  array  $param  書籍 ID 與借閱者/會員 ID 陣列
     * @return integer|false
     */
    public function borrowBook(array $param): mixed
    {
        $functionName = __FUNCTION__;

        $result = false;

        $bookId = $param['BookId'];
        $memberId = $param['MemberId'];

        $this->_checkBookExistence($bookId);
        $this->_checkMemberExistence($memberId);

        $bookStatus = $this->getBookStatus($bookId);
        if (!$bookStatus['Borrowed'])
        {
            $result = CirculationModel::getInstance()->insertRecord($bookId, $memberId);
        }

        return $result;
    }

    /**
     * 還書
     *
     * @param  array   $param    書籍 ID 或書號陣列
     * @param  string  $context  上下文類型：`Id` 或 `No`
     * @return integer|false
     */
    public function returnBook(array $param, string $context): mixed
    {
        $functionName = __FUNCTION__;

        $result = false;

        if ($context === 'Id')
        {
            $bookId = $param['BookId'];
        }
        else
        {
            $bookNo = $param['BookNo'];
            $book = BookModel::getInstance()->selectOneByNo($bookNo);
            if (is_array($book) && count($book) > 0)
            {
                $bookId = $book[0]['Id'];
            }
            else
            {
                $bookId = false;
            }
        }

        if ($bookId)
        {
            $this->_checkBookExistence($bookId);

            $bookStatus = $this->getBookStatus($bookId);
            if ($bookStatus['Borrowed'])
            {
                $bookInfo = null;
                $borrower = null;

                $book = BookModel::getInstance()->selectOneById($bookId);
                if (is_array($book) && count($book) > 0)
                {
                    $bookInfo = [
                        'Id'   => $bookId,
                        'No'   => $bookNo ?? $book[0]['No'],
                        'Name' => $book[0]['Name']
                    ];
                }

                $member = MemberModel::getInstance()->selectOneByMemberId($bookStatus['Borrower']);
                if (is_array($member) && count($member) > 0)
                {
                    $borrower = [
                        'Id'   => $bookStatus['Borrower'],
                        'No'   => $member[0]['No'],
                        'Name' => $member[0]['Name']
                    ];
                }

                $result = [
                    'Book' => $bookInfo,
                    'Borrower' => $borrower,
                    'Returned' => CirculationModel::getInstance()->updateRecord($bookStatus['RecordId'])
                ];
            }
        }

        return $result;
    }

    /**
     * 確認書籍存在於資料庫中
     *
     * @param  integer  $bookId  書籍 ID
     * @return void
     */
    public function _checkBookExistence(int $bookId): void
    {
        $functionName = __FUNCTION__;

        $result = BookModel::getInstance()->countById($bookId);
        if ($result <= 0)
        {
            throw new CirculationException('Book not exists', ExceptionCode::BookNotExist, ['Id' => $bookId]);
        }
    }

    /**
     * 確認借閱者/會員存在於資料庫中
     *
     * @param  integer  $bookId  借閱者/會員 ID
     * @return void
     */
    public function _checkMemberExistence(int $memberId): void
    {
        $functionName = __FUNCTION__;

        $result = MemberModel::getInstance()->countById($memberId);
        if ($result <= 0)
        {
            throw new CirculationException('Member not exists', ExceptionCode::MemberNotExist, ['Id' => $memberId]);
        }
    }
}
