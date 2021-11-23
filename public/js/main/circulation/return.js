const vueApp = Vue.createApp({
    data() {
        return {
            bookNo: null,
            requestFlag: false,
            circulationData: {},
            bookReturningMessage: ''
        };
    },
    methods: {
        isset(variable) {
            return (this[variable] === undefined || this[variable] === null || this[variable] === '') ? false : true;
        },
        // matchContext() {
        //     return (PageContext === 'return') ? true : false;
        // },
        init() {
            this.requestFlag = false;
            this.circulationData = {
                // 'Total': 0,
                // 'Borrower': {
                //     "Id": null,
                //     "No": null,
                //     "Name": null,
                //     "Membership": null,
                //     "Disabled": null
                // },
                // 'Record': []
            };
        },
        reset() {
            this.bookReturningMessage = '';
            this.bookNo = null;
            this.init();
        },
        getParam() {
            let b = getParameter('b');
            if (b !== undefined && b !== null && b !== '') {
                this.bookNo = b;
            }
        },
        returnBook() {
            if (!this.isset('bookNo')) {
                alert('請輸入圖書編號！')
            } else {
                this.bookReturningMessage = '';
                axios.patch(`/api/return/book/${this.bookNo}?no`)
                .then(response => {
                    const resData = response.data;
                    if (resData.Code === 200 && resData.Message === 'OK' && resData.Data.Returned === 1) {
                        const book = resData.Data.Book;
                        const borrower = resData.Data.Borrower;
                        this.bookReturningMessage =
                            `<span class="returned-book-info">【${this.bookNo}】《${book.Name}》</span>歸還完畢！` +
                            `（原借閱者：<span class="returned-book-borrower">【${borrower.No} 號】${borrower.Name}</span>）`;
                    }
                })
                .catch(error => {
                    if (error.response !== undefined) {
                        const errRes = error.response;
                        if (errRes.status !== undefined && errRes.status === 409) {
                            if (errRes.data !== undefined) {
                                const errData = errRes.data;
                                if (errData.Code !== undefined && errData.Code === 192) {
                                    alert('該書籍未借出！');
                                }
                            }
                        }
                    }
                });
            }
        }
    },
    created() {
        this.getParam();
        this.init();
    }
});

const vueModel = vueApp.mount('#app');
