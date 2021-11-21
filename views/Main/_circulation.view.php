<?php

use App\Constant;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col i-col-7.25 h-100 d-flex justify-content-start align-items-center"><?= Constant::MemberCall ?>編號</div>
        <div class="col i-col-86.75 h-100 d-flex align-items-center">
            <input type="text" class="w-100 h-100 p-2" v-model="memberNo" v-on:keyup.enter="getCirculationByMember()">
        </div>
        <div class="col i-col-6 h-100 d-flex align-items-center">
            <div class="btn btn-primary w-100" v-on:click="getCirculationByMember()">查詢</div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col i-col-7.25 h-100 d-flex justify-content-start align-items-center">圖書編號</div>
        <div class="col i-col-86.75 h-100 d-flex align-items-center">
            <input type="text" class="w-100 h-100 p-2" v-model="bookNo" v-on:keyup.enter="borrowBook" v-bind:disabled="forbidBorrow">
        </div>
        <div class="col i-col-6 h-100 d-flex align-items-center">
            <div class="btn btn-warning w-100" v-on:click="borrowBook" v-bind:class="{ disabled: forbidBorrow }">借書</div>
        </div>
    </div>
    <template v-if="requestFlag" v-cloak>
        <div class="row mt-2">
            <borrow-hint v-bind:circulation-data="{
                Total:      circulationData.Total,
                Borrower:   circulationData.Borrower,
                Record:     circulationData.Record,
                Borrowable: remainBorrowable
            }"></borrow-hint>
            <div class="col i-col-6 h-100 d-flex align-items-center">
                <div class="btn btn-danger w-100" v-on:click="reset">清除</div>
            </div>
        </div>
        <div class="row">
            <div class="fs-r1" v-if="circulationData.Total > 0">
                <hr>
                <div class="thead">
                    <div class="row fw-bold">
                        <div class="col i-col-5   d-flex justify-content-center align-items-center">書號</div>
                        <div class="col i-col-20  d-flex justify-content-center align-items-center">書名</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">作者</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">繪者</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">編者</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">譯者</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">出版者</div>
                        <div class="col i-col-7.5 d-flex justify-content-center align-items-center">分類</div>
                        <div class="col i-col-5   d-flex justify-content-center align-items-center">架位</div>
                        <div class="col i-col-10  d-flex justify-content-center align-items-center">借出時間</div>
                        <div class="col i-col-5   d-flex justify-content-center align-items-center"></div>
                    </div>
                </div>
                <div class="tbody">
                    <div class="row" v-for="data in circulationData.Record">
                        <div class="col i-col-5   d-flex justify-content-center align-items-center">{{ data.Book.No }}</div>
                        <div class="col i-col-20  d-flex align-items-center">{{ data.Book.Name }}</div>
                        <div class="col i-col-9.5 d-flex align-items-center">{{ data.Book.Author }}</div>
                        <div class="col i-col-9.5 d-flex align-items-center">{{ data.Book.Illustrator }}</div>
                        <div class="col i-col-9.5 d-flex align-items-center">{{ data.Book.Editor }}</div>
                        <div class="col i-col-9.5 d-flex align-items-center">{{ data.Book.Translator }}</div>
                        <div class="col i-col-9.5 d-flex justify-content-center align-items-center">{{ data.Book.Publisher }}</div>
                        <div class="col i-col-7.5 d-flex justify-content-center align-items-center">{{ data.Book.CategoryId }}</div>
                        <div class="col i-col-5   d-flex justify-content-center align-items-center">{{ data.Book.LocationId }}</div>
                        <div class="col i-col-10  d-flex justify-content-center align-items-center">{{ data.BorrowedAt.replace(/:[^:]+$/g, '') }}</div>
                        <div class="col i-col-5   d-flex justify-content-center align-items-center">
                            <div class="btn btn-info w-100" v-on:click="returnBook(data.Book.Id)">還書</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
