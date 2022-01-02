const vueApp = Vue.createApp({
    data() {
        return {
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
            storageTypes: StorageTypes,
            bookCategory: null,
            bookLocation: null,
            isn: null,
            ean: null,
            barcode1: null,
            barcode2: null,
            barcode3: null,
            bookNotes: null,
            requiredField: [
                'bookNumber',
                'bookName'
            ],
            alteredFieldName: {
                'bookNumber': '書號',
                'bookName': '書名',
                'storageType': '入庫原因'
            },
            unfulfilledField: [],
            bookListPage: '/books?t=No'
        };
    },
    methods: {
        isset(variable) {
            return (this[variable] === undefined || this[variable] === null || this[variable] === '') ? false : true;
        },
        init() {
            const today = new Date();
            this.storageYear = today.getFullYear();
            this.storageMonth = today.getMonth() + 1;
            this.storageDay = today.getDate();
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
