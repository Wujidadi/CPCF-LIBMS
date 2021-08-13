# LIBMS, CPCF

財團法人毛毛蟲兒童哲學基金會圖書管理系統專案，基於 Tarascanta 框架 Beta 版。


## 開發日誌

### 2021 年 8 月 13 日
* 更新 DBAPI 及 Model 父類別，變更 migration 類別和資料夾結構、更新相關程式，並同步 Tarascanta Beta。
* 初步整理原始書單。

### 2021 年 8 月 12 日
* 引入 Bootstrap SCSS 檔案。
* 變更 SCSS 檔案引用結構及輸出的 CSS 檔。
* 修改 SCSS 檔案架構；HTML 樣式儘量以 Bootstrap CSS class 指定。
* 放棄自製 autoload，引入 composer，重新編排自製 migration 機制，並同步 Tarascanta Beta。
* 新增 bin 資料夾，並同步 Tarascanta Beta。

### 2021 年 8 月 11 日
* 開始建構專案基本頁面。
* 移除各個 demo 類別及相關檔案，重整路由及 autoload、sass to css map 檔案。

### 2021 年 8 月 10 日
* 更新 Logger 及 Framework facades libraries 並同步 Tarascanta Beta。
* 加入前端 Bootstrap 及 Vue.js libraries

### 2021 年 8 月 9 日
* 完成 migration 機制並同步 Tarascanta Beta。

### 2021 年 8 月 8 日
* 正式始動，完成 Docker 容器部署。
