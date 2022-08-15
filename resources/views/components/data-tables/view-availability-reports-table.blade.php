<div class="p-6 bg-white border-b border-gray-200">
    <table class="data-table mt-4 w-full text-gray-500 sm:mt-6">
        <thead class="text-sm text-gray-500 text-left">
        <tr>
            <x-sortable-column column-name="domain" default-sort-type="asc" :is-active="false"
                               class="lg:w-1/4 pr-8 py-3 font-normal text-left">
                {{__('sites.domain')}}
            </x-sortable-column>
            <th scope="col" class="lg:w-1/5 pr-8 py-3 font-normal text-left sm:table-cell">
                {{__('sites.path')}}
            </th>
            <x-sortable-column column-name="last-date" default-sort-type="desc" :is-active="true"
                               class="w-1/6 py-3 font-normal text-left sm:table-cell">
                {{__('monitoring.monitoring_date')}}
            </x-sortable-column>
            <th scope="col" class="w-1/6 py-3 font-normal text-left">
                {{__('monitoring.returned_code')}}
            </th>
            <th scope="col" class="w-1/6 py-3 font-normal text-left">
                {{__('monitoring.monitoring_sequence')}}
            </th>
        </tr>
        </thead>
        @include('components.data-tables.availability-reports-table')
    </table>
    <x-data-table-pagination :totalPageCount="$totalPageCount"></x-data-table-pagination>
</div>
