<div class="container-fluid page-circulation page-return">
    <div class="form">
        <div class="row form-column">
            <div class="col form-column formcol-label">圖書編號</div>
            <div class="col form-column formcol-input ps-0">
                <input type="text" class="w-100 h-100 p-2" v-model="bookNo" v-on:keypress.enter="returnBook">
            </div>
            <div class="col form-column formcol-tail ps-0">
                <div class="btn btn-primary w-100" v-on:click="returnBook">還書</div>
            </div>
        </div>
    </div>
    <div class="row message-field mt-4">
        <div class="col message-column" v-html="bookReturningMessage"></div>
    </div>
</div>
