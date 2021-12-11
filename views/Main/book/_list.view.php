<div class="container-fluid page-book page-book-list">
    <div class="form">
        <div class="row form-column" style="align-items: stretch !important">
            <div class="col form-column formcol-trunk">
                <div class="row form-column w-100">
                    <div class="col form-column formcol-label p-0">
                        <select class="form-select" v-model="mainSearchType">
                            <option value="No">書號</option>
                            <option value="Name">書名</option>
                        </select>
                    </div>
                    <div class="col form-column formcol-input pe-0">
                        <input type="text" class="w-100 h-100 p-2" v-model="mainSearchKey" v-on:keypress.enter="searchBook">
                    </div>
                </div>
                <template v-if="searchByBookName">
                    <div class="row form-column w-100 mt-2">
                        <div class="col form-column formcol-label p-0">
                            <input type="checkbox" id="searchWithBookMaker" class="form-check-input" v-model="searchWithBookMaker">
                            <label class="form-check-label ps-2" for="searchWithBookMaker">創作者</label>
                        </div>
                        <div class="col form-column formcol-input pe-0">
                            <input type="text" class="w-100 h-100 p-2" v-model="bookMaker" v-on:keypress.enter="searchBook" v-bind:disabled="!searchWithBookMaker">
                        </div>
                    </div>
                    <div class="row form-column w-100 mt-2">
                        <div class="col form-column formcol-label p-0">
                            <input type="checkbox" id="searchWithPublisher" class="form-check-input" v-model="searchWithPublisher">
                            <label class="form-check-label ps-2" for="searchWithPublisher">出版者</label>
                        </div>
                        <div class="col form-column formcol-input pe-0">
                            <input type="text" class="w-100 h-100 p-2" v-model="publisher" v-on:keypress.enter="searchBook" v-bind:disabled="!searchWithPublisher">
                        </div>
                    </div>
                </template>
            </div>
            <div class="col form-column formcol-side ps-0" style="height: initial !important">
                <div class="btn btn-primary btn-text-center w-100 h-100" v-on:click="searchBook">查詢</div>
            </div>
        </div>
    </div>
    <template v-if="bookList.length > 0" v-cloak>
        <hr>
        <div class="row">
            <div class="table">
                <div class="thead">
                    <div class="row tr">
                        <div class="col th data-column data-work"></div>
                        <div class="col th data-column data-book-no"    >書號</div>
                        <div class="col th data-column data-book-name"  >書名</div>
                        <div class="col th data-column data-author"     >作者</div>
                        <div class="col th data-column data-illustrator">繪者</div>
                        <div class="col th data-column data-editor"     >編者</div>
                        <div class="col th data-column data-translator" >譯者</div>
                        <div class="col th data-column data-publisher"  >出版者</div>
                        <!-- <div class="col th data-column data-category"   >分類</div>
                        <div class="col th data-column data-location"   >架位</div> -->
                        <div class="col th data-column data-function"></div>
                    </div>
                </div>
                <div class="tbody">
                    <div class="info-container" v-for="book in bookList">
                        <div class="main-info-container row">
                            <div class="col td data-column data-work">
                                <div class="btn btn-info btn-text-center w-100 px-2 py-1 me-1 fs-r0.85" role="button"
                                     data-bs-toggle="collapse" v-bind:data-bs-target="getToggleTargetTag(book.Id)">展開</div>
                                <div class="btn btn-warning btn-text-center w-100 px-2 py-1 me-1 fs-r0.85" v-on:click="borrowBook(book.No)">借書</div>
                            </div>
                            <div class="col td data-column data-book-no"    >{{ book.No }}</div>
                            <div class="col td data-column data-book-name"  >{{ book.Name }}</div>
                            <div class="col td data-column data-author"     >{{ book.Author }}</div>
                            <div class="col td data-column data-illustrator">{{ book.Illustrator }}</div>
                            <div class="col td data-column data-editor"     >{{ book.Editor }}</div>
                            <div class="col td data-column data-translator" >{{ book.Translator }}</div>
                            <div class="col td data-column data-publisher"  >{{ book.Publisher }}</div>
                            <!-- <div class="col td data-column data-category"   >{{ book.CategoryId }}</div>
                            <div class="col td data-column data-location"   >{{ book.LocationId }}</div> -->
                            <div class="col td data-column data-function">
                                <div class="btn btn-success btn-text-center w-100 px-2 py-1 me-1 fs-r0.85">編輯</div>
                                <div class="btn btn-danger btn-text-center w-100 px-2 py-1 fs-r0.85">刪除</div>
                            </div>
                        </div>
                        <div class="detail-info-box collapse-container collapse" v-bind:id="toggleTargetTage(book.Id)">
                            <div class="detail-info-container">
                                <div class="detail-info-table">
                                    <div class="row tr">
                                        <div class="col th label-column label-book-oriname">原文書名</div>
                                        <div class="col td info-column  info-book-oriname" >{{ book.OriginalName }}</div>
                                        <div class="col th label-column label-book-series" >系列/叢書名</div>
                                        <div class="col td info-column  info-book-series"  >{{ book.Series }}</div>
                                    </div>
                                    <div class="row tr">
                                        <div class="col th label-column label-book-edition">版本別</div>
                                        <div class="col td info-column  info-book-edition" >{{ book.Edition }}</div>
                                        <div class="col th label-column label-book-print"  >印刷別</div>
                                        <div class="col td info-column  info-book-print"   >{{ book.Print }}</div>
                                        <div class="col th label-column label-publish-date">出版日期</div>
                                        <div class="col td info-column  info-publish-date" >{{ book.PublishDate }}</div>
                                    </div>
                                    <div class="row tr">
                                        <div class="col th label-column label-isn"     >ISBN/ISSN</div>
                                        <div class="col td info-column  info-isn"      >{{ book.ISN }}</div>
                                        <div class="col th label-column label-ean"     >EAN</div>
                                        <div class="col td info-column  info-ean"      >{{ book.EAN }}</div>
                                        <div class="col th label-column label-category">分類</div>
                                        <div class="col td info-column  info-category" >{{ book.CategoryId }}</div>
                                        <div class="col th label-column label-location">架位</div>
                                        <div class="col td info-column  info-location" >{{ book.LocationId }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
