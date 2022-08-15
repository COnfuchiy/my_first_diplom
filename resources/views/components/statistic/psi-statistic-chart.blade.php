<div class="psi-statistic-chart max-h-2xl max-w-3xl mx-auto sm:px-6 lg:px-8" data-current-page="{{$currentPage}}"
     data-total-page-count="{{$totalPageCount}}">
    <canvas id="psiStatisticChart"></canvas>
</div>
<div class="statistic-pagination">
    <x-statistic-pagination :currentPage="$currentPage" :totalPageCount="$totalPageCount"
                            :timeInterval="$timeInterval"
                            :monitoringCount="$monitoringCount"></x-statistic-pagination>
</div>
<script>
    document.psiChartDatasets = [
            @if($site->seo_psi_desktop_check)
        {
            label: '{{__('monitoring.desktop_strategy')}}',
            data: {!! json_encode($psiDesktopTotalScore) !!},
            borderColor: 'red',
            backgroundColor: 'rgba(75, 192, 192,0.7)',
            pointStyle: 'circle',
            pointRadius: 10,
            pointHoverRadius: 15,
        },
            @endif
            @if($site->seo_psi_mobile_check)
        {
            label: '{{__('monitoring.mobile_strategy')}}',
            data: {!! json_encode($psiMobileTotalScore) !!},
            borderColor: 'blue',
            backgroundColor: 'rgba(75, 192, 192,0.7)',
            pointStyle: 'circle',
            pointRadius: 10,
            pointHoverRadius: 15,
        }
        @endif
    ];
    document.psiChartLabels = {!! json_encode($labels) !!};
    document.psiMobileChartData = {!! json_encode($psiMobileData) !!};
    document.psiDesktopChartData = {!! json_encode($psiDesktopData) !!};
    document.psiPagesStatisticChart = new Chart($('#psiStatisticChart')[0].getContext('2d'), {
        type: 'line',
        data: {
            labels: document.psiChartLabels,
            datasets: document.psiChartDatasets
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
                    max: 100
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
                            let outputArray = ['Общий рейтинг производительности: '+context.formattedValue];
                            if (context.dataset.label === "Десктопная"){
                                outputArray.push('FCP: '+ document.psiDesktopChartData[index]['FCP']);
                                outputArray.push('TTI: '+ document.psiDesktopChartData[index]['TTI']);
                                outputArray.push('speedIndex: '+ document.psiDesktopChartData[index]['speedIndex']);
                                outputArray.push('TBT: '+ document.psiDesktopChartData[index]['TBT']);
                                outputArray.push('LCP: '+ document.psiDesktopChartData[index]['LCP']);
                                outputArray.push('CLS: '+ document.psiDesktopChartData[index]['CLS']);
                            }
                            else {
                                outputArray.push('FCP: '+ document.psiMobileChartData[index]['FCP']);
                                outputArray.push('TTI: '+ document.psiMobileChartData[index]['TTI']);
                                outputArray.push('speedIndex: '+ document.psiMobileChartData[index]['speedIndex']);
                                outputArray.push('TBT: '+ document.psiMobileChartData[index]['TBT']);
                                outputArray.push('LCP: '+ document.psiMobileChartData[index]['LCP']);
                                outputArray.push('CLS: '+ document.psiMobileChartData[index]['CLS']);
                            }
                            return outputArray;
                        }
                    }
                }
            },
            onClick(e) {
                const activePoints = psiPagesStatisticChart.getElementsAtEventForMode(e, 'nearest', {
                    intersect: true
                }, false)
                const [{
                    index,
                    datasetIndex
                }] = activePoints;
                console.log(psiChartLabels[index]);
                console.log(datasetIndex);
            }
        }
    });

    document.statisticData.totalPagesCount['psiStatistic'] = {{$totalPageCount}};
    document.statisticData.currentPages['psiStatistic'] = {{$currentPage}};
</script>
