<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('sites.sites_list') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('components.sites-list.filter-bar')
                </div>
                <div class="ml-6 mt-6 flex w-full">
                    <a href="{{route('sites.create')}}"
                       class="inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-white">
                        {{__('sites.add_site_button')}}</a>
                </div>
                <x-view-data-table table-type="sites" :rowsData="$rowsData" :currentPage="$currentPage"
                                   :totalPageCount="$totalPageCount"></x-view-data-table>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="/js/sortingDataTable.js"></script>
<script>
    $(function () {
        document.dataTable = new SortingDataTable('sites', {{$totalPageCount}},{{$currentPage}});
        document.dataTable.initHandlers();
    });
</script>
