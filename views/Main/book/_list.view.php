<div class="container-fluid page-book page-book-list">
    <div class="form">
        <div class="row form-column">
            <div class="col form-column formcol-label">
                <select class="form-control">
                    <option value="1">書名</option>
                    <option value="2">書號</option>
                </select>
            </div>
            <div class="col form-column formcol-input">
                <input type="text" class="w-100 h-100 p-2" v-model="mainSearchKey" v-on:keyup.enter="searchBook">
            </div>
            <div class="col form-column formcol-tail">
                <div class="btn btn-warning w-100" v-on:click="searchBook">還書</div>
            </div>
        </div>
    </div>
    <!-- <div class="row message-field mt-4">
        <div class="col message-column" v-html="bookReturningMessage"></div>
    </div> -->
</div>
