# LIBMS, CPCF

財團法人毛毛蟲兒童哲學基金會圖書管理系統專案，基於 Tarascanta 框架 Beta 版。


## 特別記事

* 《**一定 一定 要來接我喔**》原始資料的 ISBN 為「`樣本書`」。
* 《**月亮晚上做什麼**》原始資料的 ISBN 為「`”57324733X`」。
* 《**燒出寶石人生**》（無入庫日期）原始資料的 ISBN 為「`97898690474244`」（14 碼），應為「`9789860474244`」（13 碼）。
* 《**上學的第一天，我的肚子裡有蝴蝶**》（20201104 入庫）原始資料的 ISBN 為「`98709866215049`」（14 碼），應為「`9789866215049`」（13 碼）。
* ISN 欄位（ISBN 或 ISSN）中的 x 應一律轉大寫。


## 開發日誌

### 2021 年 11 月 21 日
* 借書前端頁面初步完成。

### 2021 年 11 月 14 日
* 增加 `InputChecker::_isNotAllowed` 方法。
* 完成借閱者（會員）資料 API 及輸入驗證。
* 完成借還書 API 及輸入驗證。

### 2021 年 11 月 12 日
* 在 `Framework/helpers` 定義集中加入 `TimeZoneSuffix` 常數，並同步 Tarascanta Beta。
* 完成書籍資料的輸入驗證。

### 2021 年 11 月 9 日
* 完成刪除書籍資料（軟刪除）API。

### 2021 年 11 月 8 日
* 調整各類別中函數及參數的型態宣告使更符合 PHP 8 的形式，且同步 Tarascanta Beta。
* 完成修改書籍資料 API。

### 2021 年 11 月 7 日
* 框架層級的變動（同步 Tarascanta Beta）：
  - 引入 HTTP request/response 處理類別。
  - 強化各類別及函數的型態宣告。
  - DBAPI 變動：
    1. 增強對陣列型態參數（`IN` 查詢）的支援。
    2. 允許查詢參數指定 `PDO::PARAM_STR` 以外的型態。
  - 在 `Framework/helpers` 定義集中加入 `SumWord` 方法。
* 完成新增書籍 API。

### 2021 年 11 月 6 日
* 完成除權限系統外各資料表的遷移檔，並建置資料表完畢。

### 2021 年 9 月 19 日
* 調整部分類別建構子的順序；存在繼承關係的類別方法，統一將 doc 註釋寫在父類別方法之前。
* 新增驗證器（Validator）及輸入驗證（InputChecker）類別。

### 2021 年 9 月 5 日
* 完成書籍資料查詢 API。
* 在 `canta` 工具中加入專案初始化指令，並同步 Tarascanta Beta。

### 2021 年 9 月 4 日
* 完成匯入 Excel 書單至資料庫的命令行工具。
* 以下 2 項同步 Tarascanta Beta：
  - 變更各 migration class 檔案中 SQL 語法 heredoc 字串的辨識符為 `SQL`（原為 `EOT`），以便在 IDE 中渲染 SQL 語句的顏色。
  - 執行 migration 時遇到 PDOException 的處理方式，由返回 `false` 改為拋出 Exception code 35。

### 2021 年 8 月 26 日
* 在 `canta` 工具中加入 `--verbose` 選項以允許查看原始執行訊息，並同步 Tarascanta Beta。

### 2021 年 8 月 23 日
* 引入 JavaScript 程式碼壓縮及混淆工具（基於 npm 套件 `webpack`、`webpack-cli` 及 `terser-webpack-plugin`）。
* 引入 CSS 壓縮工具（基於 npm 套件 `clean-css` 及 `clean-css-cli`）。

### 2021 年 8 月 15 日
* 改寫資料庫設定檔架構與 `DBAPI` 取得單一實例的方式，更新 `Migration`、`Model` 及其子類別的建構子與繼承方法，以適配多資料庫設定值的情形，並同步 Tarascanta Beta。

### 2021 年 8 月 14 日
* 將 `bin` 命令行腳本的邏輯移到 controller 中實作，腳本僅呼叫 controller 方法。
* 將 `bin` 及 `tools` 的進入點移到 bootstrap 資料夾，更新 bootstrap 檔案架構，並同步 Tarascanta Beta。

### 2021 年 8 月 13 日
* 更新 `DBAPI` 及 `Model` 父類別，變更 migration 類別和資料夾結構、更新相關程式，並同步 Tarascanta Beta。
* 初步整理原始書單。

### 2021 年 8 月 12 日
* 引入 Bootstrap SCSS 檔案。
* 變更 SCSS 檔案引用結構及輸出的 CSS 檔。
* 修改 SCSS 檔案架構；HTML 樣式儘量以 Bootstrap CSS class 指定。
* 放棄自製 autoload，引入 composer，重新編排自製 migration 機制，並同步 Tarascanta Beta。
* 新增 `bin` 資料夾，並同步 Tarascanta Beta。

### 2021 年 8 月 11 日
* 開始建構專案基本頁面。
* 移除各個 demo 類別及相關檔案，重整路由及 autoload、sass to css map 檔案。

### 2021 年 8 月 10 日
* 更新 `Logger` 及 Framework facades libraries 並同步 Tarascanta Beta。
* 加入前端 Bootstrap 及 Vue.js libraries

### 2021 年 8 月 9 日
* 完成 migration 機制並同步 Tarascanta Beta。

### 2021 年 8 月 8 日
* 正式始動，完成 Docker 容器部署。
