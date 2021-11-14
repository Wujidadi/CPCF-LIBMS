<?php

namespace App;

/**
 * 自訂常數類別
 */
class Constant
{
    /**
     * 系統名稱
     *
     * @var string
     */
    const SystemName = '圖書管理系統';

    /**
     * 客戶全稱
     *
     * @var string
     */
    const CustomerFullName = '財團法人毛毛蟲兒童哲學基金會';

    /**
     * 顯示資料每頁最大筆數
     *
     * @var integer
     */
    const MaxDataCountPerPage = 200;

    /**
     * 顯示資料預設頁碼
     *
     * @var integer
     */
    const DefaultPageNumber = 1;

    /**
     * 顯示資料每頁預設筆數
     *
     * @var integer
     */
    const DefaultPageLimit = 10;

    /**
     * 查詢借閱紀錄的時間起訖最大間距秒數（180 天）
     *
     * @var integer
     */
    const MaxRecordsDateRange = 15552000;
}
