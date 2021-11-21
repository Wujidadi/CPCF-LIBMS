<div class="container-fluid page-circulation-borrow">
    <div class="form">
        <div class="row form-column">
            <div class="col form-column formcol-label"><?= App\Constant::MemberCall ?>編號</div>
            <div class="col form-column formcol-input">
                <input type="text" class="w-100 h-100 p-2" v-model="memberNo" v-on:keyup.enter="getCirculationByMember()">
            </div>
            <div class="col form-column formcol-tail">
                <div class="btn btn-primary w-100" v-on:click="getCirculationByMember()">查詢</div>
            </div>
        </div>
        <div class="row form-column mt-2">
            <div class="col form-column formcol-label">圖書編號</div>
            <div class="col form-column formcol-input">
                <input type="text" class="w-100 h-100 p-2" v-model="bookNo" v-on:keyup.enter="borrowBook" v-bind:disabled="forbidBorrow">
            </div>
            <div class="col form-column formcol-tail">
                <div class="btn btn-warning w-100" v-on:click="borrowBook" v-bind:class="{ disabled: forbidBorrow }">借書</div>
            </div>
        </div>
    </div>
    <template v-if="requestFlag" v-cloak>
        <div class="row message-field mt-2">
            <borrow-hint v-bind:circulation-data="{
                Total:      circulationData.Total,
                Borrower:   circulationData.Borrower,
                Record:     circulationData.Record,
                Borrowable: remainBorrowable
            }"></borrow-hint>
            <div class="col message-column msgcol-tail">
                <div class="btn btn-danger w-100" v-on:click="reset">清除</div>
            </div>
        </div>
        <div class="row">
            <div class="table" v-if="circulationData.Total > 0">
                <hr>
                <div class="thead">
                    <div class="row tr fw-bold">
                        <div class="col th data-column data-book-no"    >書號</div>
                        <div class="col th data-column data-book-name"  >書名</div>
                        <div class="col th data-column data-author"     >作者</div>
                        <div class="col th data-column data-illustrator">繪者</div>
                        <div class="col th data-column data-editor"     >編者</div>
                        <div class="col th data-column data-translator" >譯者</div>
                        <div class="col th data-column data-publisher"  >出版者</div>
                        <div class="col th data-column data-category"   >分類</div>
                        <div class="col th data-column data-location"   >架位</div>
                        <div class="col th data-column data-borrowed-at">借出時間</div>
                        <div class="col th data-column data-function"   ></div>
                    </div>
                </div>
                <div class="tbody">
                    <div class="row tr" v-for="data in circulationData.Record">
                        <div class="col td data-column data-book-no"    >{{ data.Book.No }}</div>
                        <div class="col td data-column data-book-name"  >{{ data.Book.Name }}</div>
                        <div class="col td data-column data-author"     >{{ data.Book.Author }}</div>
                        <div class="col td data-column data-illustrator">{{ data.Book.Illustrator }}</div>
                        <div class="col td data-column data-editor"     >{{ data.Book.Editor }}</div>
                        <div class="col td data-column data-translator" >{{ data.Book.Translator }}</div>
                        <div class="col td data-column data-publisher"  >{{ data.Book.Publisher }}</div>
                        <div class="col td data-column data-category"   >{{ data.Book.CategoryId }}</div>
                        <div class="col td data-column data-location"   >{{ data.Book.LocationId }}</div>
                        <div class="col td data-column data-borrowed-at">{{ data.BorrowedAt.replace(/:[^:]+$/g, '') }}</div>
                        <div class="col td data-column data-function"   >
                            <div class="btn btn-info w-100" v-on:click="returnBook(data.Book.Id)">還書</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
