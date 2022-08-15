<div class="relative flex items-center w-full h-12 rounded-lg bg-white overflow-hidden">
    <div class="grid place-items-center h-full w-12 text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    <input
        class="mx-4 px-4 py-2 rounded-md shadow-sm border-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-blue-200 focus:ring-opacity-0 block mt-1 w-full"
        type="text"
        id="search"
        placeholder="{{__('form.page_search_placeholder')}}"
            value="{{request('searchRequest','')}}"
    />
    <button type="button" class="search-button inline-flex items-center px-5 py-2 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        {{__('form.search')}}
    </button>
</div>
