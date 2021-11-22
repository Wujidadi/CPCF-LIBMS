<div class="container-fluid page-circulation page-return">
    <div class="form">
        <div class="row form-column">
            <div class="col form-column formcol-label">圖書編號</div>
            <div class="col form-column formcol-input">
                <input type="text" class="w-100 h-100 p-2" v-model="bookNo" v-on:keyup.enter="returnBook">
            </div>
            <div class="col form-column formcol-tail">
                <div class="btn btn-warning w-100" v-on:click="returnBook">還書</div>
            </div>
        </div>
    </div>
    <div class="row message-field mt-4">
        <div class="col message-column" v-html="bookReturningMessage"></div>
    </div>
</div>
