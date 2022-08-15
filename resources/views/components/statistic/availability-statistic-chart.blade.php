<div class="availability-statistic-chart max-h-3xl max-w-4xl mx-auto sm:px-6 lg:px-8" data-current-page="{{$currentPage}}"
     data-total-page-count="{{$totalPageCount}}">
    <canvas id="statisticChart"></canvas>
</div>
<div class="statistic-pagination">
    <x-statistic-pagination :currentPage="$currentPage" :totalPageCount="$totalPageCount"
                            :timeInterval="$timeInterval"
                            :monitoringCount="$monitoringCount"></x-statistic-pagination>
</div>
<script>
    document.availabilityChartDatasets = [{
        label: '{{__('monitoring.error_report')}}',
        data: {!! json_encode($errorData) !!},
        backgroundColor: [
            'rgb(255, 99, 132)',
        ],
        borderWidth: 1
    }, {
        label: '{{__('monitoring.success_report')}}',
        data: {!! json_encode($successData) !!},
        backgroundColor: [
            'rgb(75, 192, 192)',
        ],
        borderWidth: 1
    }];
    document.availabilityChartLabels = {!! json_encode($labels) !!};
    document.availabilityStatisticChart = new Chart($('#statisticChart')[0].getContext('2d'), {
        type: 'bar',
        data: {
            labels: document.availabilityChartLabels,
            datasets:document.availabilityChartDatasets
        },
        options: {
            responsive: true,
            pointDotRadius: 10,
            bezierCurve: false,
            scaleShowVerticalLines: false,
            scaleGridLineColor: 'black',
            onClick(e) {
                const activePoints = document.availabilityStatisticChart.getElementsAtEventForMode(e, 'nearest', {
                    intersect: true
                }, false)
                const [{
                    index,
                    datasetIndex
                }] = activePoints;
                console.log(document.availabilityChartLabels[index]);
                console.log(datasetIndex);
            }
        }
    });
</script>
