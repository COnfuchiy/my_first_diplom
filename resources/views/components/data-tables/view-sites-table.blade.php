<div class="site-list p-6 bg-white border-b border-gray-200">
    <table class="data-table mt-4 w-full text-gray-500 sm:mt-6">
        <thead class="text-sm text-gray-500 text-left">
        <tr>
            <x-sortable-column column-name="domain" default-sort-type="asc" :is-active="false"
                               class="sm:w-1/5 lg:w-1/3 font-normal">
                {{__('sites.domain')}}
            </x-sortable-column>
            <th scope="col" class="w-1/6 font-normal sm:table-cell">
                {{__('monitoring.availability_reports')}}
            </th>
            <th scope="col" class="w-1/5 font-normal sm:table-cell">
                {{__('monitoring.performance_reports')}}
            </th>
            <th scope="col" class="font-normal sm:table-cell">
                {{__('monitoring.statistic')}}
            </th>
            <x-sortable-column column-name="date" default-sort-type="desc" :is-active="true"
                               class="w-1/6 font-normal text-right sm:table-cell">
                {{__('monitoring.create_date')}}
            </x-sortable-column>
            <th scope="col" class="w-1/6 font-normal text-right">
                {{__('sites.activity')}}
            </th>
            <th scope="col" class="w-1/7 font-normal text-right">
                {{__('form.delete')}}
            </th>
        </tr>
        </thead>
        @include('components.data-tables.sites-table')
    </table>
    <x-data-table-pagination :totalPageCount="$totalPageCount" :currentPage="$currentPage"></x-data-table-pagination>
</div>
