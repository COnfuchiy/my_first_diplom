<div class="pages-statistic-chart max-h-2xl max-w-3xl mx-auto sm:px-6 lg:px-8" data-current-page="{{$currentPage}}"
     data-total-page-count="{{$totalPageCount}}">
    <canvas id="pageStatisticChart"></canvas>
</div>
<div class="statistic-pagination">
    <x-statistic-pagination :currentPage="$currentPage" :totalPageCount="$totalPageCount"
                            :timeInterval="$timeInterval"
                            :monitoringCount="$monitoringCount"></x-statistic-pagination>
</div>
<script>
    document.pagesChartDatasets = [{
        label: '{{__('monitoring.returned_code')}}',
        data: {!! json_encode($pageData) !!},
        borderColor: 'red',
        backgroundColor: 'rgba(75, 192, 192,0.7)',
        pointStyle: 'circle',
        pointRadius: 10,
        pointHoverRadius: 15,
    }];
    document.pagesErrorMessages = {!! json_encode($errorMessages) !!}
        document.pagesChartLabels = {!! json_encode($labels) !!};
    document.pagesStatisticChart = new Chart($('#pageStatisticChart')[0].getContext('2d'), {
        type: 'line',
        data: {
            labels: document.pagesChartLabels,
            datasets: document.pagesChartDatasets
        },
        options: {
            scales: {
                x: {
                    ticks: {
                        display: false
                    }
                },
                y: {
                    min: 0,
                    max: 550
                }
            },
            responsive: true,
            pointDotRadius: 10,
            bezierCurve: false,
            scaleShowVerticalLines: false,
            scaleGridLineColor: 'black',
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let index = context.element.$context.dataIndex;
                            let returnedCode = context.dataset.label + ': ' + context.formattedValue;
                            if (document.pagesErrorMessages[index]) {
                                let message = 'Ошибка: ' + document.pagesErrorMessages[index];
                                return [returnedCode, message];
                            }
                            return returnedCode;
                        }
                    }
                }
            },
            onClick(e) {
                const activePoints = pagesStatisticChart.getElementsAtEventForMode(e, 'nearest', {
                    intersect: true
                }, false)
                const [{
                    index,
                    datasetIndex
                }] = activePoints;
                console.log(pagesChartLabels[index]);
                console.log(datasetIndex);
            }
        }
    });

    document.statisticData.totalPagesCount['pagesStatistic'] = {{$totalPageCount}};
    document.statisticData.currentPages['pagesStatistic'] = {{$currentPage}};
</script>
