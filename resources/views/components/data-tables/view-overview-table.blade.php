<div class="p-6 bg-white border-b border-gray-200">
    <table class="data-table mt-4 w-full text-gray-500 sm:mt-6">
        <thead class="text-sm text-gray-500 text-left">
        <tr>
            <th class="sm:w-2/5 lg:w-1/4 pr-8 py-3 font-normal">
                {{__('sites.domain')}}
            </th>
            <th scope="col" class="w-1/5 pr-8 py-3 font-normal text-left">
                {{__('monitoring.status')}}
            </th>
            <th scope="col" class="w-1/5 pr-8 py-3 font-normal sm:table-cell">
                {{__('monitoring.availability_reports')}}
            </th>
            <th scope="col" class="font-normal sm:table-cell ml-0.5">
                {{__('monitoring.performance_reports')}}
            </th>
            <th class="w-0 py-3 font-normal text-right">
                {{__('monitoring.statistic')}}
            </th>
            <th class="w-0 py-3 font-normal text-right">
                {{__('monitoring.last_monitoring_date')}}
            </th>
        </tr>
        </thead>
        @include('components.data-tables.overview-table')
    </table>
    <x-data-table-pagination :totalPageCount="$totalPageCount"></x-data-table-pagination>
</div>
