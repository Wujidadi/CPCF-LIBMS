/**
 * **Member** 的中文定義，依客戶（組織）性質不同，可能為 **會員**、**借閱者**、**人員**、**員工** 等  
 * 應與後端 `App::Constant` 的 `MemberCall` 一致
 *
 * @type string
 */
const MemberCall = '借閱者';

/**
 * 預設每人最大可借書數量  
 * 應與後端 `App::Constant` 的 `DefaultBorrowableCount` 一致
 *
 * @type integer
 */
const DefaultBorrowableCount = 7;

/**
 * 顯示資料每頁預設筆數  
 * 應與後端 `App::Constant` 的 `DefaultPageLimit` 一致
 *
 * @type integer
 */
const DefaultPageLimit = 10;

/**
 * 顯示資料預設頁碼  
 * 應與後端 `App::Constant` 的 `DefaultPageNumber` 一致
 *
 * @type integer
 */
const DefaultPageNumber = 1;
