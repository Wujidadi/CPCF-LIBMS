const vueApp = Vue.createApp({
    data() {
        return {
            memberNo: null,
            deepMemberNo: null,
            bookNo: null,
            requestFlag: false,
            circulationData: {},
            maxBorrowable: null    // Todo: 每人可借數量資料表
        };
    },
    computed: {
        remainBorrowable() {
            return this.maxBorrowable - this.circulationData.Total;
        },
        forbidBorrow() {
            return (this.circulationData.Total < DefaultBorrowableCount) ? false : true;
        }
    },
    methods: {
        isset(variable) {
            return (this[variable] === undefined || this[variable] === null || this[variable] === '') ? false : true;
        },
        // matchContext() {
        //     return (PageContext === 'circulation') ? true : false;
        // },
        init() {
            this.requestFlag = false;
            this.circulationData = {
                'Total': 0,
                'Borrower': {
                    "Id": null,
                    "No": null,
                    "Name": null,
                    "Membership": null,
                    "Disabled": null
                },
                'Record': []
            };
        },
        reset() {
            this.memberNo = null;
            this.deepMemberNo = null;
            this.bookNo = null;
            this.init();
        },
        getParam() {
            let b = getParameter('b');
            if (b !== undefined && b !== null && b !== '') {
                this.bookNo = b;
            }
        },
        getCirculationByMember(memNo = null) {
            if (memNo !== null) {
                this.memberNo = memNo;
            }
            if (!this.isset('memberNo')) {
                alert(`請輸入${MemberCall}編號！`);
            } else {
                this.init();
                axios.get(`/api/records/member/${this.memberNo}/borrowing`)
                .then(response => {
                    const resData = response.data;
                    if (resData.Code === 200 && resData.Message === 'OK' &&
                        resData.Data.Total !== undefined && resData.Data.Borrower !== undefined && resData.Data.Record !== undefined) {
                        this.circulationData = resData.Data;
                        this.deepMemberNo = this.memberNo;
                        this.requestFlag = true;
                    }
                })
                .catch(error => {
                    console.warn(error);
                });
            }
        },
        borrowBook() {
            let notFilled = [];
            let alertMsg = '';

            if (!this.isset('memberNo')) {
                notFilled.push(MemberCall);
            }
            if (!this.isset('bookNo')) {
                notFilled.push('圖書');
            }

            if (notFilled.length > 1) {
                let lastElm = notFilled.pop(notFilled);
                alertMsg = [ notFilled.join('、'), lastElm ].join('及');
            } else if (notFilled.length === 1) {
                alertMsg = notFilled[0];
            }

            if (alertMsg !== '') {
                alert(`請輸入${alertMsg}編號！`);
            } else {
                axios.post(`/api/borrow/book/${this.bookNo}/${this.memberNo}`)
                .then(response => {
                    const resData = response.data;
                    if (resData.Code === 200 && resData.Message === 'OK' && resData.Data === 1) {
                        this.getCirculationByMember();
                    }
                })
                .catch(error => {
                    // console.warn(error.response);
                    // console.warn(error.response.data);
                    // console.warn(error.response.data.status);
                    if (error.response !== undefined) {
                        const errRes = error.response;
                        if (errRes.status !== undefined && errRes.status === 409) {
                            if (errRes.data !== undefined) {
                                const errData = errRes.data;
                                if (errData.Code !== undefined && errData.Code === 143) {
                                    alert('該書籍已借出！');
                                }
                            }
                        }
                    }
                });
            }
        },
        returnBook(bookId) {
            axios.patch(`/api/return/book/${bookId}`)
            .then(response => {
                const resData = response.data;
                if (resData.Code === 200 && resData.Message === 'OK' && resData.Data === 1) {
                    this.getCirculationByMember(this.deepMemberNo);
                }
            })
            .catch(error => {
                console.warn(error);
            });
        }
    },
    created() {
        this.maxBorrowable = DefaultBorrowableCount;
        this.getParam();
        this.init();
    }
});

vueApp.component('borrow-hint', {
    template:
        `<div class="col message-column msgcol-body" v-bind:class="borrowHintStyle">` +
            `<p class="m-0 h-100 d-flex align-items-center" v-html="borrowHint"></p>` +
        `</div>`,
    props: [ 'circulationData' ],
    data() {
        return {
            maxBorrowable: null    // Todo: 每人可借數量資料表
        };
    },
    computed: {
        borrowHintStyle() {
            let className = 'borrow-hint';
            if (this.circulationData.Borrower.Id !== null) {
                if (this.circulationData.Borrowable > 0) {
                    className += '-safe';
                } else {
                    className += '-danger';
                }
            }
            return className;
        },
        borrowHint() {
            let html = '';
            if (this.circulationData.Borrower.Id !== null) {
                html =
                    `【${this.circulationData.Borrower.No} 號】${this.circulationData.Borrower.Name}：` +
                    `目前已借 <span class="borrowed-number mx-1">${this.circulationData.Total}</span> 本，` +
                    `可再借 <span class="borrowable-number mx-1">${this.circulationData.Borrowable}</span> 本`;
            } else {
                html = `${MemberCall}【${this.circulationData.Borrower.No} 號】不存在！`;
            }
            return html;
        }
    },
    created() {
        this.maxBorrowable = DefaultBorrowableCount;
    }
});

const vueModel = vueApp.mount('#app');
