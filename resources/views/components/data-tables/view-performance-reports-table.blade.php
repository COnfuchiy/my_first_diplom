<div class="p-6 bg-white border-b border-gray-200">
    <table class="data-table mt-4 w-full text-gray-500 sm:mt-6">
        <thead class=" text-sm text-gray-500 text-left">
        <tr>
            <x-sortable-column column-name="domain" default-sort-type="asc" :is-active="false"
                               class="w-2/6 lg:w-1/3 pr-8 py-3 font-normal">
                {{__('sites.domain')}}
            </x-sortable-column>
            <th scope="col" class="w-2/6 pr-8 py-3 font-normal sm:table-cell">
                {{__('sites.path')}}
            </th>
            <x-sortable-column column-name="monitoring-date" default-sort-type="desc" :is-active="true"
                               class="w-2/6 py-3 font-normal sm:table-cell">
                {{__('monitoring.monitoring_date')}}
            </x-sortable-column>
            <th scope="col" class="w-2/6 py-3 font-normal text-right">
                {{__('monitoring.total_score')}}
            </th>
            <th scope="col" class="w-2/6 py-3 font-normal text-right">
                {{__('monitoring.strategy')}}
            </th>
        </tr>
        </thead>
        @include('components.data-tables.performance-reports-table')
    </table>
    <x-data-table-pagination :totalPageCount="$totalPageCount"></x-data-table-pagination>
</div>
