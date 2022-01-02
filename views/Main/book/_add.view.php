<div class="container-fluid page-book page-book-add">
    <div class="form">
        <div class="row form-column">
            <div class="col form-column formcol-label p-0">書號</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" placeholder="必填" v-model="bookNumber">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">書名</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" placeholder="必填" v-model="bookName">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">原文書名</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="originalBookName">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">作者</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="author">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">繪者</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="illustrator">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">編者</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="editor">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">譯者</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="translator">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">系列/叢書名</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="series">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">出版者</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2" v-model="publisher">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col col-publish-date">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">出版日期</div>
                    <div class="col form-column formcol-input pe-0">
                        <div class="row d-flex align-items-center w-100">
                            <span class="col p-1 fit-content">西元</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="publishYear" v-on:keyup="checkYear('publish')">
                            <span class="col px-1 fit-content">年</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="publishMonth" v-on:keyup="checkMonth('publish')">
                            <span class="col px-1 fit-content">月</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="publishDay" v-on:keyup="checkDay('publish')">
                            <span class="col p-1 fit-content">日</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-edition">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">版次</div>
                    <div class="col form-column formcol-input p-0">
                        <input type="text" class="form-control w-100 h-100 p-2 text-center" v-model="edition">
                    </div>
                </div>
            </div>
            <div class="col col-print">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">刷次</div>
                    <div class="col form-column formcol-input p-0">
                        <input type="text" class="form-control w-100 h-100 p-2 text-center" v-model="print">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col col-storage-date">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">入庫日期</div>
                    <div class="col form-column formcol-input pe-0">
                        <div class="row d-flex align-items-center w-100">
                            <span class="col p-1 fit-content">西元</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="storageYear" v-on:keyup="checkYear('storage')">
                            <span class="col px-1 fit-content">年</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="storageMonth" v-on:keyup="checkMonth('storage')">
                            <span class="col px-1 fit-content">月</span>
                            <input type="text" class="form-control col h-100 p-2 text-center" v-model="storageDay" v-on:keyup="checkDay('storage')">
                            <span class="col p-1 fit-content">日</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col col-storage-type">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">入庫原因</div>
                    <div class="col form-column formcol-input p-0">
                        <select class="form-select w-100 h-100 p-2" v-bind:class="{ 'text-secondary': storageType < 1 }" v-model="storageType">
                            <option value="0" disabled>選擇入庫原因類型 (必選)</option>
                            <option v-for="storageType in storageTypes" v-bind:value.number="storageType.Id">{{ storageType.Name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- 書籍分類及架位功能暫時不啟用 -->
        <div class="row form-column mt-2">
            <div class="col col-category">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">分類</div>
                    <div class="col form-column formcol-input">
                        <input type="text" class="form-control col h-100 p-2 text-center" v-model="bookCategory" disabled>
                    </div>
                </div>
            </div>
            <div class="col col-location">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">架位</div>
                    <div class="col form-column formcol-input">
                        <input type="text" class="form-control col h-100 p-2 text-center" v-model="bookLocation" disabled>
                    </div>
                </div>
            </div>
        </div>
        <!---->
        <div class="row form-column mt-2">
            <div class="col col-isn">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">ISBN/ISSN</div>
                    <div class="col form-column formcol-input">
                        <input type="text" class="form-control col h-100 p-2 text-center" v-model="isn">
                    </div>
                </div>
            </div>
            <div class="col col-ean">
                <div class="row d-flex align-items-center">
                    <div class="col form-column formcol-label p-0">國際商品條碼</div>
                    <div class="col form-column formcol-input">
                        <input type="text" class="form-control col h-100 p-2 text-center" v-model="ean">
                    </div>
                </div>
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">附註</div>
            <div class="col form-column formcol-input pe-0">
                <textarea class="form-control textarea-noresize" v-model="bookNotes"></textarea>
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">其他條碼1</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2 text-center" v-model="barcode1">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">其他條碼2</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2 text-center" v-model="barcode2">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label p-0">其他條碼3</div>
            <div class="col form-column formcol-input pe-0">
                <input type="text" class="form-control w-100 h-100 p-2 text-center" v-model="barcode3">
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col col-submit">
                <div class="row d-flex align-items-center ps-2 pe-1">
                    <div class="btn btn-primary btn-text-center w-100 px-2 py-1" v-on:click="submit">送出</div>
                </div>
            </div>
            <div class="col col-cancel">
                <div class="row d-flex align-items-center px-1">
                    <div class="btn btn-secondary btn-text-center w-100 px-2 py-1" v-on:click="cancel">取消</div>
                </div>
            </div>
        </div>
    </div>
</div>
