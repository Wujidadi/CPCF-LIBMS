const vueApp = Vue.createApp({
    data() {
        return {
            // 固定項
            bookListPage: '/books?t=No',
            storageTypes: StorageTypes,
            requiredField: [
                'bookNumber',
                'bookName'
            ],
            alteredFieldName: {
                'bookNumber': '書號',
                'bookName': '書名',
                'storageType': '入庫原因'
            },

            // 變動項
            initialized: false,
            unfulfilledField: [],

            // 書籍資料
            bookNumber: null,
            bookName: null,
            originalBookName: null,
            author: null,
            illustrator: null,
            editor: null,
            translator: null,
            series: null,
            publisher: null,
            publishYear: null,
            publishMonth: null,
            publishDay: null,
            publishDate: null,
            publishDateType: 0,
            edition: null,
            print: null,
            storageYear: null,
            storageMonth: null,
            storageDay: null,
            storageDate: null,
            storageType: 0,
            bookCategory: null,
            bookLocation: null,
            isn: null,
            ean: null,
            barcode1: null,
            barcode2: null,
            barcode3: null,
            bookNotes: null,

            // 原始書籍資料（限編輯頁）
            originalBookData: {}
        };
    },
    computed: {
        requiredHint() {
            return (PageContext === 'AddBook' || this.initialized) ? '必填' : '';
        }
    },
    methods: {
        isset(variable) {
            return (this[variable] === undefined || this[variable] === null || this[variable] === '') ? false : true;
        },
        init() {
            switch (PageContext) {
                case 'AddBook':
                    const today = new Date();
                    this.storageYear = today.getFullYear();
                    this.storageMonth = today.getMonth() + 1;
                    this.storageDay = today.getDate();
                    break;

                case 'EditBook':
                default:
                    console.log(BookData);
                    this.bookNumber       = this.originalBookData.bookNumber       = BookData.No;
                    this.bookName         = this.originalBookData.bookName         = BookData.Name;
                    this.originalBookName = this.originalBookData.originalBookName = BookData.OriginalName !== null ? BookData.OriginalName : '';
                    this.author           = this.originalBookData.author           = BookData.Author       !== null ? BookData.Author       : '';
                    this.illustrator      = this.originalBookData.illustrator      = BookData.Illustrator  !== null ? BookData.Illustrator  : '';
                    this.editor           = this.originalBookData.editor           = BookData.Editor       !== null ? BookData.Editor       : '';
                    this.translator       = this.originalBookData.translator       = BookData.Translator   !== null ? BookData.Translator   : '';
                    this.series           = this.originalBookData.series           = BookData.Series       !== null ? BookData.Series       : '';
                    this.publisher        = this.originalBookData.publisher        = BookData.Publisher    !== null ? BookData.Publisher    : '';
                    this.publishDate      = this.originalBookData.publishDate      = BookData.PublishDate;
                    this.publishDateType  = this.originalBookData.publishDateType  = BookData.PublishDateType;
                    const publishDate = this.parsePublishDate();
                    this.publishYear  = publishDate.year;
                    this.publishMonth = publishDate.month;
                    this.publishDay   = publishDate.day;
                    this.edition          = this.originalBookData.edition          = BookData.Edition      !== null ? BookData.Edition      : '';
                    this.print            = this.originalBookData.print            = BookData.Print        !== null ? BookData.Print        : '';
                    this.storageDate      = this.originalBookData.storageDate      = BookData.StorageDate;
                    this.storageType      = this.originalBookData.storageType      = BookData.StorageType;
                    const storageDate = this.parseStorageDate();
                    this.storageYear  = storageDate.year;
                    this.storageMonth = storageDate.month;
                    this.storageDay   = storageDate.day;
                    this.bookCategory     = this.originalBookData.bookCategory     = BookData.CategoryId;    // 待商榷
                    this.bookLocation     = this.originalBookData.bookLocation     = BookData.LocationId;    // 待商榷
                    this.isn              = this.originalBookData.isn              = BookData.ISN          !== null ? BookData.ISN          : '';
                    this.ean              = this.originalBookData.ean              = BookData.EAN          !== null ? BookData.EAN          : '';
                    this.barcode1         = this.originalBookData.barcode1         = BookData.Barcode1     !== null ? BookData.Barcode1     : '';
                    this.barcode2         = this.originalBookData.barcode2         = BookData.Barcode2     !== null ? BookData.Barcode2     : '';
                    this.barcode3         = this.originalBookData.barcode3         = BookData.Barcode3     !== null ? BookData.Barcode3     : '';
                    this.bookNotes        = this.originalBookData.bookNotes        = BookData.Notes        !== null ? BookData.Notes        : '';
                    break;
            }
            this.initialized = true;
        },
        checkYear(type) {
            const dataName = `${type}Year`;
            if (this.isset(dataName)) {
                let data = Number(String(this[dataName]).replace(/[^\d]/g, ''));
                this[dataName] = data > 0 ? data : new Date().getFullYear();
            }
        },
        checkMonth(type) {
            const dataName = `${type}Month`;
            if (this.isset(dataName)) {
                let data = Number(String(this[dataName]).replace(/[^\d]/g, ''));
                while (data > 12) {
                    data = Math.floor(data / 10);
                }
                this[dataName] = data > 0 ? data : new Date().getMonth() + 1;
            }
        },
        checkDay(type) {
            const dataName = `${type}Day`;
            const monthDataName = `${type}Month`;
            const yearDataName = `${type}Year`;

            if (this.isset(dataName)) {
                let data = Number(String(this[dataName]).replace(/[^\d]/g, ''));
                let maxDay = 30;

                if ([1, 3, 5, 7, 8, 10, 12].indexOf(this[monthDataName]) > -1) {
                    maxDay = 31;
                } else if (this[monthDataName] === 2) {
                    if ((this[yearDataName] % 100 !== 0 && this[yearDataName] % 4 === 0) ||
                        (this[yearDataName] % 100 === 0 && this[yearDataName] % 400 === 0)) {
                        maxDay = 29;
                    } else {
                        maxDay = 28;
                    }
                }

                while (data > maxDay) {
                    data = Math.floor(data / 10);
                }

                this[dataName] = data > 0 ? data : new Date().getDate();
            }
        },
        parsePublishDate() {
            let date = {
                year: null,
                month: null,
                day: null
            };

            if (this.publishDateType > 0 && this.publishDateType <= 3) {
                const matches = this.publishDate.match(/^(\d{4,})\-(\d{2})\-(\d{2})$/);
                if (matches !== null && matches.length > 1) {
                    date.year = Number(matches[1]);
                    if (this.publishDateType > 1) {
                        date.month = Number(matches[2]);
                    }
                    if (this.publishDateType > 2) {
                        date.day = Number(matches[3]);
                    }
                }
            }

            return date;
        },
        parseStorageDate() {
            let date = {
                year: null,
                month: null,
                day: null
            };

            if (this.isset('storageDate')) {
                const matches = this.storageDate.match(/^(\d{4,})\-(\d{2})\-(\d{2})$/);
                if (matches !== null && matches.length > 1) {
                    date.year = Number(matches[1]);
                    date.month = Number(matches[2]);
                    date.day = Number(matches[3]);
                }
            }

            return date;
        },
        buildPublishDate() {
            if (this.isset('publishYear')) {
                this.publishDateType = 3;
                this.publishDate = this.publishYear;

                if (this.isset('publishMonth')) {
                    this.publishDateType = 2;
                    this.publishDate += '-' + padding(this.publishMonth, '0', 2);

                    if (this.isset('publishDay')) {
                        this.publishDateType = 1;
                        this.publishDate += '-' + padding(this.publishDay, '0', 2);
                    } else {
                        this.publishDate += '-01';
                    }
                } else {
                    this.publishDate += '-01-01'
                }
            }
        },
        buildStorageDate() {
            if (this.isset('storageYear')) {
                this.storageDate = this.storageYear;

                if (this.isset('storageMonth')) {
                    this.storageDate += '-' + padding(this.storageMonth, '0', 2);

                    if (this.isset('storageDay')) {
                        this.storageDate += '-' + padding(this.storageDay, '0', 2);
                    } else {
                        this.storageDate += '-01';
                    }
                } else {
                    this.storageDate += '-01'
                }
            }
        },
        buildFormData() {
            this.buildPublishDate();
            this.buildStorageDate();
            return {
                No: this.bookNumber,
                Name: this.bookName,
                OriginalName: this.originalBookName,
                Author: this.author,
                Illustrator: this.illustrator,
                Editor: this.editor,
                Translator: this.translator,
                Series: this.series,
                Publisher: this.publisher,
                PublishDate: this.publishDate,
                PublishDateType: this.publishDateType,
                Edition: this.edition,
                Print: this.print,
                StorageDate: this.storageDate,
                StorageType: this.storageType,
                Notes: this.bookNotes,
                ISN: this.isn,
                EAN: this.ean,
                Barcode1: this.barcode1,
                Barcode2: this.barcode2,
                Barcode3: this.barcode3,
                CategoryId: this.bookCategory,
                LocationId: this.bookLocation
            };
        },
        requiredNotFulfilled() {
            this.unfulfilledField = [];
            this.requiredField.forEach(field => {
                if (!this.isset(field)) {
                    this.unfulfilledField.push(field);
                }
            });
            if (this.storageType === 0) {
                this.unfulfilledField.push('storageType');
            }
            console.log(this.unfulfilledField);
            return (this.unfulfilledField.length > 0) ? true : false;
        },
        submit() {
            if (this.requiredNotFulfilled()) {
                let notFulfilled = [];
                this.unfulfilledField.forEach(field => {
                    notFulfilled.push(this.alteredFieldName[field]);
                });
                alert(notFulfilled.join('、') + '未填！');
            } else {
                let formData = this.buildFormData();

                axios.post(`/api/book`, formData)
                .then(response => {
                    const resData = response.data;
                    if (resData.Code === 200 && resData.Message === 'OK') {
                        if(confirm('新增書籍成功！是否繼續新增下一本書籍？')) {
                            location.reload();
                        } else {
                            location.href = this.bookListPage;
                        }
                    }
                })
                .catch(error => {
                    if (error.response !== undefined) {
                        const errRes = error.response;
                        if (errRes.status !== undefined) {
                            console.warn(errRes.status);
                            console.warn(errRes.data);
                            // if (errRes.status === 409 && errRes.data !== undefined) {
                            //     const errData = errRes.data;
                            //     if (errData.Code !== undefined && errData.Code === 143) {
                            //         alert('該書籍已借出！');
                            //     }
                            // } else if (errRes.status === 400 && errRes.data !== undefined) {
                            //     const errData = errRes.data;
                            //     if (errData.Code !== undefined ) {
                            //         if (errData.Code === 80) {
                            //             alert('輸入格式錯誤！');
                            //         } else if (errData.Code === 169) {
                            //             alert('該圖書不存在！');
                            //         } else if (errData.Code === 182) {
                            //             // alert('借閱者不存在！');
                            //             this.requestFlag = true;
                            //         }
                            //     }
                            // }
                        }
                    }
                });
            }
        },
        cancel() {
            if (confirm('確定放棄正在新增的書籍資料？')) {
                location.href = this.bookListPage;
            }
        }
    },
    mounted() {
        this.init();
    }
});

const vueModel = vueApp.mount('#app');
