class StatisticData {

    static chartsContainers = {
        availabilityStatistic: '.availability-statistic-container',
        pagesStatistic: '.pages-statistic-container',
        psiStatistic: '.psi-statistic-container',
    };
    static canvasContainers = {
        availabilityStatistic: '.availability-statistic-chart',
        pagesStatistic: '.pages-statistic-chart',
        psiStatistic: '.psi-statistic-chart',
    };

    static pagesSelect = {
        pagesStatistic: '#pages-statistic-tab #page-select',
        psiStatistic: '#psi-statistic-tab #page-select',
    };

    static requestUrls = {
        availabilityStatistic: '',
        pagesStatistic: '/pages',
        psiStatistic: '/psi',
    };

    static prevPageLinkClass = '.prev-page';

    static nextPageLinkClass = '.next-page';

    static otherPageLinkClass = '.other-page';

    static dateFromOutputName = 'date_from';

    static dateToOutputName = 'date_to';

    totalPagesCount = {
        availabilityStatistic: 1,
        pagesStatistic: 1,
        psiStatistic: 1,
    };

    currentPages = {
        availabilityStatistic: 1,
        pagesStatistic: 1,
        psiStatistic: 1,
    };

    selectedPage = {
        pagesStatistic: '',
        psiStatistic: '',
    };

    currentChartType = 'availabilityStatistic';

    dateFrom = '';

    dateTo = '';

    constructor(siteId, baseUrl) {
        if (!window.jQuery) {
            throw new Error(''); // TODO
        }
        this.siteId = siteId;
        this.baseUrl = baseUrl;
        let pagesData = $(this.constructor.canvasContainers['availabilityStatistic']).data();
        this.totalPagesCount['availabilityStatistic'] = pagesData.totalPageCount;
        this.currentPages['availabilityStatistic'] = pagesData.currentPage;

    }

    setDate() {
        this.dateFrom = $(`input[name=${this.constructor.dateFromOutputName}]`).val();
        this.dateTo = $(`input[name=${this.constructor.dateToOutputName}]`).val();
    }

    initHandlers() {
        let body = $('body');
        for (let chartType in this.constructor.chartsContainers) {
            let prevPageSelector = `${this.constructor.chartsContainers[chartType]} ${this.constructor.prevPageLinkClass}`;
            body.on('click', prevPageSelector, () => {
                this.prevPageHandle(chartType);
            });
            let nextPageSelector = `${this.constructor.chartsContainers[chartType]} ${this.constructor.nextPageLinkClass}`;
            body.on('click', nextPageSelector, () => {
                this.nextPageHandle(chartType);
            });
            let otherPageSelector = `${this.constructor.chartsContainers[chartType]} ${this.constructor.otherPageLinkClass}`;
            body.on('click', otherPageSelector, (e) => {
                this.otherPageHandle(e.target, chartType);
            });
        }
        for (let pageSelect in this.constructor.pagesSelect) {
            body.on('change', this.constructor.pagesSelect[pageSelect], (e) => {
                this.changePage(e.target, pageSelect);
            });
        }
    }

    prevPageHandle(chartType) {
        this.currentChartType = chartType;
        if (this.currentPages[chartType] > 1) {
            this.currentPages[chartType]--;
            this.baseRequest(chartType);
        }
    }

    nextPageHandle(chartType) {
        this.currentChartType = chartType;
        if (this.currentPages[chartType] < this.totalPagesCount[chartType]) {
            this.currentPages[chartType]++;
            this.baseRequest(chartType);
        }
    }

    otherPageHandle(otherLinkContainer, chartType) {
        this.currentChartType = chartType;
        this.currentPages[chartType] = parseInt(otherLinkContainer.innerText);
        this.baseRequest(chartType);
    }

    baseRequest(chartType) {
        this.currentChartType = chartType;
        let data = {
            page: this.currentPages[chartType],
            dateFrom: this.dateFrom,
            dateTo: this.dateTo
        };
        if (chartType !== 'availabilityStatistic') {
            data.sitePage = this.selectedPage[chartType];
        }
        $.ajax(
            {
                url: location.href.replace('#', '') + this.constructor.requestUrls[chartType],
                method: 'get',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    // TODO loader ends
                    if (response !== false) {
                        // $(this.constructor.chartsContainers[chartType]).empty();
                        $(this.constructor.chartsContainers[chartType]).html(response);
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

    changeDatetime(dateFrom, dateTo) {
        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
        this.baseRequest(this.currentChartType);
    }

    handlerChartClick(datetime, type) {

    }

    changePage(target, pageSelect) {
        if ($(target).val()!=='Выберите страницу'){
            this.selectedPage[pageSelect] = $(target).val();
            this.currentPages[pageSelect]    = 1;
            this.baseRequest(pageSelect);
        }
    }
}
