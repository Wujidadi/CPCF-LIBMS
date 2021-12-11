const vueApp = Vue.createApp({
    data() {
        return {
            book: {
                total: 0,
                page: 0
            },
            bookList: [],
            mainSearchType: 'Name',
            mainSearchKey: '',
            bookMaker: '',
            publisher: '',
            searchWithBookMaker: false,
            searchWithPublisher: false,
            allowedMainSearchType: [ 'No', 'Name' ],
            pageLimit: DefaultPageLimit,
            pageNumber: DefaultPageNumber,
            searchUrl: ''
        };
    },
    computed: {
        searchByBookName() {
            return this.mainSearchType === 'Name' ? true : false;
        },
        emptySearchKey() {
            if (!this.isset('mainSearchKey') &&
                (!this.searchWithBookMaker || (this.searchWithBookMaker && !this.isset('bookMaker'))) &&
                (!this.searchWithPublisher || (this.searchWithPublisher && !this.isset('publisher')))) {
                return true;
            } else {
                return false;
            }
        },
        searchWithBookMakerCertainly() {
            return (this.searchByBookName && this.searchWithBookMaker && this.isset('bookMaker')) ? true : false;
        },
        searchWithPublisherCertainly() {
            return (this.searchByBookName && this.searchWithPublisher && this.isset('publisher')) ? true : false;
        }
    },
    watch: {
        mainSearchType(newVal, oldVal) {
            let paramObj = {};
            if (newVal !== oldVal) {
                paramObj.t = newVal;
                if (newVal === 'No') {
                    paramObj.m = null;
                    paramObj.r = null;
                }
                replaceParameter(paramObj);
            }
        }
    },
    methods: {
        isset(variable) {
            return (this[variable] === undefined || this[variable] === null || this[variable] === '') ? false : true;
        },
        // matchContext() {
        //     return (PageContext === 'BookList') ? true : false;
        // },
        init() {
            //
        },
        reset() {
            this.mainSearchKey = '';
        },
        getParam() {
            let t = getParameter('t');    // Type
            let k = getParameter('k');    // Key
            let m = getParameter('m');    // Maker
            let r = getParameter('r');    // Publisher
            let c = getParameter('c');    // Count
            let p = getParameter('p');    // Page

            if (t !== undefined && t !== null && t !== '' && this.allowedMainSearchType.indexOf(t) > -1) {
                this.mainSearchType = t;
            }
            if (k !== undefined && k !== null && k !== '') {
                this.mainSearchKey = decodeURIComponent(k);
            }
            if (m !== undefined && m !== null && k !== '') {
                this.bookMaker = decodeURIComponent(m);
                this.searchWithBookMaker = true;
            }
            if (r !== undefined && r !== null && k !== '') {
                this.publisher = decodeURIComponent(r);
                this.searchWithPublisher = true;
            }
            if (c !== undefined && c !== null && c !== '' && !isNaN(c)) {
                this.pageLimit = parseInt(c);
            }
            if (p !== undefined && p !== null && p !== '' && !isNaN(p)) {
                this.pageNumber = parseInt(p);
            }
        },
        buildSearchUrl() {
            this.searchUrl = `/api/books` + ((this.emptySearchKey || !this.isset('mainSearchKey')) ? '/all?' : `/${this.mainSearchType}/${this.mainSearchKey}?`);
            if (this.searchWithBookMakerCertainly) {
                this.searchUrl += `m=${this.bookMaker}&`;
            }
            if (this.searchWithPublisherCertainly) {
                this.searchUrl += `r=${this.publisher}&`;
            }
            this.searchUrl += `c=${this.pageLimit}&p=${this.pageNumber}`;
        },
        rebuildUrlBeforeSearching() {
            let paramObj = {};

            if (this.isset('mainSearchKey')) {
                paramObj.k = this.mainSearchKey;
            } else {
                paramObj.k = null;
            }

            if (this.searchWithBookMakerCertainly) {
                paramObj.m = this.bookMaker;
            } else {
                paramObj.m = null;
            }

            if (this.searchWithPublisherCertainly) {
                paramObj.r = this.publisher;
            } else {
                paramObj.r = null;
            }

            if (this.pageLimit !== DefaultPageLimit) {
                paramObj.c = this.pageLimit;
            } else {
                paramObj.c = null;
            }

            if (this.pageNumber !== DefaultPageNumber) {
                paramObj.p = this.pageNumber;
            } else {
                paramObj.p = null;
            }

            replaceParameter(paramObj);
        },
        clearBookData() {
            this.totalCount = 0;
            this.totalPage = 0;
            this.bookList = [];
        },
        searchBook() {
            this.buildSearchUrl();
            this.rebuildUrlBeforeSearching();
            // echo(this.searchUrl);
            this.clearBookData();
            axios.get(this.searchUrl)
            .then(response => {
                // echo(response);
                const resData = response.data;
                this.totalCount = resData.Data.Total.Count;
                this.totalPage = resData.Data.Total.Page;
                if (resData.Code === 200 && resData.Message === 'OK' && resData.Data.List.length > 0) {
                    this.bookList = resData.Data.List;
                }
            })
            .catch(error => {
                console.warn(error);
            });
        },
        toggleTargetTage(bookId) {
            return `book${bookId}`;
        },
        getToggleTargetTag(bookId) {
            return `#book${bookId}`;
        },
        borrowBook(bookNo) {
            const url = `/circulation/borrow?b=${bookNo}`;
            window.open(url, '_blank').focus();
        }
    },
    created() {
        this.getParam();
        this.init();
        this.searchBook();
    }
});

const vueModel = vueApp.mount('#app');
