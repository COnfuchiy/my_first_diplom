class SortingDataTable {
    // static props

    static tablesTypes = [
        'sites',
        'availabilityReports',
        'performanceReports',
        'overview',
    ];

    static arrowUpSvg = '/img/arrow-up.svg';

    static arrowDownSvg = '/img/arrow-down.svg';

    static sortingUrls = {
        sites: '/user-control-panel/sites',
        availabilityReports: '/user-control-panel/availability-reports',
        performanceReports: '/user-control-panel/performance-reports',
        overview: '/user-control-panel/overview'
    };

    static sortedColumnsData = {
        sites: [
            'domain',
            'availability',
            'date',
        ],
        availabilityReports: [
            'last-date',
            'domain',
        ],
        performanceReports: [
            'monitoring-date',
            'domain',
        ],
        overview: [],
    };

    static defaultSortColumn = {
        sites: {column: 'date', type: 'desc'},
        availabilityReports: {column: 'last-date', type: 'desc'},
        performanceReports: {column: 'monitoring-date', type: 'desc'},
        overview: {},
    };

    static dataContainerClass = '.data-table';

    static pagesLinksContainerClass = '.pages-links';

    static prevPageLinkClass = '.prev-page';

    static nextPageLinkClass = '.next-page';

    static otherPageLinkClass = '.other-page';

    static searchButtonClass = '.search-button';

    static searchInput = '#search';

    // pagination props

    totalPageCount = 1;

    currentPage = 1;

    // table props

    dateFrom = '';

    dateTo = '';

    currentTableType = '';

    currentSortColumn = '';

    currentSortType = '';

    currentSearchRequest = '';

    isActiveSearch = false;

    constructor(type, totalPageCount, page = 1) {
        if (!window.jQuery) {
            throw new Error(''); // TODO
        }
        if (this.constructor.tablesTypes.indexOf(type) === -1) {
            throw new Error(''); // TODO
        }
        this.currentTableType = type;
        this.totalPageCount = totalPageCount;
        this.currentPage = page;
        if (this.constructor.defaultSortColumn[this.currentTableType] !== []) {
            this.currentSortColumn = this.constructor.defaultSortColumn[this.currentTableType].column;
            this.currentSortType = this.constructor.defaultSortColumn[this.currentTableType].type;
        }
    }

    initHandlers() {
        let body = $('body');
        for (let column of this.constructor.sortedColumnsData[this.currentTableType]) {
            body.on('click', `.${column}-sorting`, (e) => {
                this.sortingHandler(column, e.target);
            });
        }
        body.on('click', this.constructor.prevPageLinkClass, (e) => {
            this.prevPageHandle();
        });
        body.on('click', this.constructor.nextPageLinkClass, (e) => {
            this.nextPageHandle();
        });
        body.on('click', this.constructor.otherPageLinkClass, (e) => {
            this.otherPageHandle(e.target);
        });
        body.on('click', this.constructor.searchButtonClass, (e) => {
            this.searchHandle();
        });
    }

    baseRequest() {
        let requestData = {
            page: this.currentPage,
            sortColumn: this.currentSortColumn,
            sortType: this.currentSortType,
        };
        if (this.isActiveSearch && this.currentSearchRequest) {
            requestData.searchRequest = this.currentSearchRequest;
        }
        if (this.dateTo && this.dateFrom) {
            requestData.dateTo = this.dateTo;
            requestData.dateFrom = this.dateFrom;
        }
        let url = this.constructor.sortingUrls[this.currentTableType];
        // TODO may be loader?
        $.ajax(
            {
                url: url,
                method: 'get',
                data: requestData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    // TODO loader ends
                    if (response !== false) {
                        // $(this.constructor.dataContainerClass).find('tbody').remove();
                        $(this.constructor.dataContainerClass).find('tbody').replaceWith(response);
                        this.updateSortingColumns();
                        let responseTotalPageCount = $(this.constructor.dataContainerClass).find('tbody').data('total-page-count');
                        if (responseTotalPageCount !== this.totalPageCount) {
                            this.totalPageCount = responseTotalPageCount;
                            this.resetPagination();
                        }
                        window.history.pushState(null, '', window.location.pathname.split('/').reverse()[0]+'?'+jQuery.param(requestData));
                    }
                },
                error: (e) => {
                    // TODO loader ends
                    console.log(e);
                    // throw new Error(''); TODO
                }
            }
        );
    }

    resetPagination() {
        this.currentPage = 1;
        let prevLinkContainer = $(this.constructor.prevPageLinkClass);
        let nextLinkContainer = $(this.constructor.nextPageLinkClass);
        if (this.currentPage >= this.totalPageCount) {
            nextLinkContainer.addClass('hidden');
            prevLinkContainer.addClass('hidden');
        } else {
            nextLinkContainer.removeClass('hidden');
            prevLinkContainer.addClass('hidden');
        }
        let pages = $(this.constructor.pagesLinksContainerClass);
        if (pages.children().length !== this.totalPageCount) {
            pages.empty();
            if (this.totalPageCount) {
                pages.append(`
                        <a href="#"
                           class="current-page border-indigo-500 text-indigo-600 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                            1
                        </a>
                        `);
                for (let i = 2; i <= this.totalPageCount; i++) {
                    pages.append(`
                            <a href="#"
                                class="other-page border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                                ${i}
                            </a>
                        `);
                }

            }
        }
    }

    sortingHandler(columnName, element) {
        this.currentSortColumn = columnName;
        this.currentSortType = $(element).data('sort') === 'asc' ? 'desc' : 'asc';
        $(element).data('sort', this.currentSortType);
        this.baseRequest()
        // reset sorting by class
    }

    swapSortingArrow(imgElement) {
        if (this.currentSortType !== 'asc') {
            imgElement.attr('src', this.constructor.arrowDownSvg);
        } else {
            imgElement.attr('src', this.constructor.arrowUpSvg);
        }
    }

    updateSortingColumns() {
        for (let column of this.constructor.sortedColumnsData[this.currentTableType]) {
            let columnContainer = $(`.${column}-sorting`);
            let imgElement = columnContainer.find('img');
            if (this.currentSortColumn === column) {
                columnContainer.addClass('font-semibold')
                imgElement.removeClass('hidden').addClass('inline');
                this.swapSortingArrow(imgElement);
            } else {
                columnContainer.removeClass('font-semibold');
                imgElement.removeClass('inline').addClass('hidden');
            }
        }
    }

    prevPageHandle() {
        let prevLinkContainer = $(this.constructor.prevPageLinkClass);
        let nextLinkContainer = $(this.constructor.nextPageLinkClass);
        if (this.currentPage > 1) {
            this.currentPage--;
            this.updatePaginationLinksState(this.currentPage - 1, this.currentPage);
            if (this.currentPage === 1) {
                prevLinkContainer.addClass('hidden');
                nextLinkContainer.removeClass('hidden');
            } else {
                prevLinkContainer.removeClass('hidden');
                nextLinkContainer.removeClass('hidden');
            }
            this.baseRequest();
        }
    }

    nextPageHandle() {
        let nextLinkContainer = $(this.constructor.nextPageLinkClass);
        let prevLinkContainer = $(this.constructor.prevPageLinkClass);
        if (this.currentPage < this.totalPageCount) {
            this.currentPage++;
            this.updatePaginationLinksState(this.currentPage - 1, this.currentPage - 2);
            if (this.currentPage === this.totalPageCount) {
                nextLinkContainer.addClass('hidden');
                prevLinkContainer.removeClass('hidden');
            } else {
                prevLinkContainer.removeClass('hidden');
                nextLinkContainer.removeClass('hidden');
            }
            this.baseRequest();
        }
    }

    otherPageHandle(otherLinkContainer) {
        let prevPage = this.currentPage;
        this.currentPage = parseInt(otherLinkContainer.innerText);
        this.updatePaginationLinksState(this.currentPage - 1, prevPage - 1);
        let prevLinkContainer = $(this.constructor.prevPageLinkClass);
        let nextLinkContainer = $(this.constructor.nextPageLinkClass);
        if (this.currentPage === 1) {
            prevLinkContainer.addClass('hidden');
            if (this.currentPage !== this.totalPageCount) {
                nextLinkContainer.removeClass('hidden');
            }
        } else {
            if (this.currentPage === this.totalPageCount) {
                nextLinkContainer.addClass('hidden');
                prevLinkContainer.removeClass('hidden');
            } else {
                prevLinkContainer.removeClass('hidden');
                nextLinkContainer.removeClass('hidden');
            }
        }
        this.baseRequest();
    }

    updatePaginationLinksState() {
        let pages = $(this.constructor.pagesLinksContainerClass);
        pages.empty();
        let pageRange = [];
        if (this.totalPageCount !== 0) {
            if (this.totalPageCount > 9) {
                if (this.currentPage <= 5) {
                    pageRange = [...this.range(1, 7)].concat(['...', this.totalPageCount]);
                } else {
                    if (this.currentPage + 5 > this.totalPageCount) {
                        pageRange = [1, '...'].concat([
                            ...this.range(this.totalPageCount - 6, this.totalPageCount)
                        ]);
                    } else {
                        pageRange = [1, '...'].concat([
                            ...this.range(this.currentPage - 2, this.currentPage + 2)
                        ]).concat(['...', this.totalPageCount]);
                    }
                }
            } else {
                pageRange = this.totalPageCount > 1 ? this.range(1, this.totalPageCount) : [1];
            }

        }
        for (let page of pageRange) {
            if (page === this.currentPage) {
                pages.append(`<a href="#" class="current-page border-indigo-500 text-indigo-600 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">${page}</a>`);
            } else {
                if (page === '...') {
                    pages.append(`<span class="border-transparent text-gray-500 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"> ... </span>`);
                } else {
                    pages.append(`<a href="#" class="other-page border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">${page}</a>`);
                }
            }

        }
        // let pages = $(this.constructor.pagesLinksContainerClass).children();
        // if (pages && currentPage in pages) {
        //     $(pages[currentPage])
        //         .removeClass('other-page border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300')
        //         .addClass('current-page border-indigo-500 text-indigo-600');
        //     $(pages[prevPageNum])
        //         .removeClass('current-page border-indigo-500 text-indigo-600')
        //         .addClass('other-page border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
        //
        // }
    }

    searchHandle() {
        this.currentSearchRequest = $(this.constructor.searchInput).val();
        if (this.currentSearchRequest) {
            this.isActiveSearch = true;
            this.baseRequest();
        } else {
            this.isActiveSearch = true;
        }
    }

    changeDatetime(dateFrom, dateTo) {
        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
        this.baseRequest();
    }

    range(from, to, step = 1) {
        return {
            * [Symbol.iterator]() {
                for (let val = from; val <= to; val += step) {
                    yield val;
                }
            }
        }
    }
}
