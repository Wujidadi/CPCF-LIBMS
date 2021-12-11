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
        //     return (PageContext === 'Borrow') ? true : false;
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
            replaceParameter({
                m: null,
                b: null
            });
            this.init();
        },
        getParam() {
            let m = getParameter('m');    // Member
            let b = getParameter('b');    // Book

            if (m !== undefined && m !== null && m !== '') {
                this.memberNo = m;
            }
            if (b !== undefined && b !== null && b !== '') {
                this.bookNo = b;
            }
        },
        getCirculationByMember(memNo = null) {
            if (memNo !== null) {
                this.memberNo = memNo;
            }

            replaceParameter({
                m: this.isset('memberNo') ? this.memberNo : null
            });

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
                    if (error.response !== undefined) {
                        const errRes = error.response;
                        if (errRes.status !== undefined) {
                            if (errRes.status === 400 && errRes.data !== undefined) {
                                const errData = errRes.data;
                                if (errData.Code !== undefined ) {
                                    if (errData.Code === 80) {
                                        alert('輸入格式錯誤！');
                                    }
                                }
                            }
                        }
                    }
                });
            }
        },
        borrowBook() {
            let notFilled = [];
            let alertMsg = '';

            let newQueryParam = {
                m: this.memberNo,
                b: this.bookNo
            };

            if (!this.isset('memberNo')) {
                newQueryParam.m = null;
                notFilled.push(MemberCall);
            }
            if (!this.isset('bookNo')) {
                newQueryParam.b = null;
                notFilled.push('圖書');
            }

            replaceParameter(newQueryParam);

            if (notFilled.length > 1) {
                let lastElm = notFilled.pop(notFilled);
                alertMsg = [ notFilled.join('、'), lastElm ].join('及');
            } else if (notFilled.length === 1) {
                alertMsg = notFilled[0];
            }

            if (alertMsg !== '') {
                alert(`請輸入${alertMsg}編號！`);
            } else {
                this.init();
                replaceParameter({ m: this.memberNo, b: this.bookNo });

                axios.post(`/api/borrow/book/${this.bookNo}/${this.memberNo}`)
                .then(response => {
                    const resData = response.data;
                    if (resData.Code === 200 && resData.Message === 'OK' && resData.Data === 1) {
                        this.getCirculationByMember();
                    }
                })
                .catch(error => {
                    if (error.response !== undefined) {
                        const errRes = error.response;
                        if (errRes.status !== undefined) {
                            if (errRes.status === 409 && errRes.data !== undefined) {
                                const errData = errRes.data;
                                if (errData.Code !== undefined && errData.Code === 143) {
                                    alert('該書籍已借出！');
                                }
                            } else if (errRes.status === 400 && errRes.data !== undefined) {
                                const errData = errRes.data;
                                if (errData.Code !== undefined ) {
                                    if (errData.Code === 80) {
                                        alert('輸入格式錯誤！');
                                    } else if (errData.Code === 169) {
                                        alert('該圖書不存在！');
                                    } else if (errData.Code === 182) {
                                        // alert('借閱者不存在！');
                                        this.requestFlag = true;
                                    }
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
                if (resData.Code === 200 && resData.Message === 'OK' && resData.Data.Returned === 1) {
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
    props: [ 'circulationData', 'memberNo' ],
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
                html = `${MemberCall}【${this.getMember()} 號】不存在！`;
            }
            return html;
        }
    },
    methods: {
        getMember() {
            return this.circulationData.Borrower?.No || this.memberNo;
        }
    },
    created() {
        this.maxBorrowable = DefaultBorrowableCount;
    }
});

const vueModel = vueApp.mount('#app');
