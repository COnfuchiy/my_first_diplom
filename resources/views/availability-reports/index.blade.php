<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('monitoring.availability_reports') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('components.reports-filter-bar')
                </div>
                <div class="ml-6 w-full flex items-center justify-start">
                    <div class="items-baseline mr-6"><p>{{__('monitoring.search_by_date')}}</p></div>
                    @include('components.datetimepicker')
                </div>
                <div class="report-modal">
                    @include('components.show-report-modal')
                </div>
                <x-view-data-table table-type="availabilityReports" :rowsData="$rowsData"
                                   :totalPageCount="$totalPageCount"></x-view-data-table>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="/js/sortingDataTable.js"></script>
<script>
    $(function () {
        document.dataTable = new SortingDataTable('availabilityReports', {{$totalPageCount}});
        document.dataTable.initHandlers();
        $('body').on('click', '.availability-report', function (e) {
            let id = $(this).data('report-id');
            if (id) {
                $.ajax(
                    {
                        url: `/user-control-panel/availability-reports/${id}/`,
                        method: 'get',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: (response) => {
                            // TODO loader ends
                            if (response) {
                                $('.show-report').click();
                                $('.report-info').html(response);
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
        });
    });
</script>
