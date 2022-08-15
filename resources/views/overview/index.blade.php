<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('monitoring.overview') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-view-data-table table-type="overview" :rowsData="$rowsData"
                                   :totalPageCount="$totalPageCount"></x-view-data-table>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="/js/sortingDataTable.js"></script>
<script>
    $(function () {
        let dataTable = new SortingDataTable('overview', {{$totalPageCount}});
        dataTable.initHandlers();
    });
</script>
