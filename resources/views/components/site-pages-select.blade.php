{{--<label for="page-select" class="block text-sm font-medium text-gray-700"></label>--}}
<select id="page-select" name="page-select" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
    <option>{{__('form.select_page')}}</option>
    @foreach($sitePages as $page)
        <option>{{$page}}</option>
    @endforeach
</select>
