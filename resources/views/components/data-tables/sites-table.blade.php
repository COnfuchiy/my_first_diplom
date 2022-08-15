<tbody class="border-b border-gray-200 divide-y divide-gray-200 text-sm sm:border-t"
       data-total-page-count="{{$totalPageCount}}">
@foreach($rowsData as $site)
    <tr class="user-site" data-site-id="{{$site->id}}">
        <td class="sm:w-1/5 lg:w-1/3">
            <a href="{{route('sites.edit',['site'=>$site->id])}}"
               class="text-indigo-600">{{$site->domain}}</a>
        </td>
        <td class="w-1/6 sm:table-cell">
            <a href="{{route('availability-reports.index',['searchRequest'=>$site->domain])}}"
               class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="w-1/5 sm:table-cell">
            <a href="{{route('performance-reports.index',['searchRequest'=>$site->domain])}}"
               class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="sm:table-cell">
            <a href="{{route('statistic',['siteId'=>$site->id])}}"
               class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="w-1/6 text-right">
            {{$site->created_at->toDateTimeString()}}
        </td>
        <td class="w-1/6 sm:table-cell">
            <div class="flex items-center justify-end w-full">
                <label for="toggle-site-active" class="flex items-center cursor-pointer">
                    <!-- toggle -->
                    <div class="relative">
                        <!-- input -->
                        <input type="checkbox" id="toggle-site-active"
                               class="toggle-site-active sr-only"
                               @if($site->is_active)
                               checked
                                @endif
                        >
                        <!-- line -->
                        <div class="toggle-label block bg-gray-200 w-14 h-8 rounded-full"></div>
                        <!-- dot -->
                        <div
                                class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"
                                style="background-color: white"></div>
                    </div>
                </label>
            </div>
        </td>
        <td class="pl-4 w-1/7 font-medium text-right whitespace-nowrap">
            <button type="button" class="button button-site-delete flex items-center px-5 py-2.5 font-medium tracking-wide text-black capitalize rounded-md  hover:bg-red-200 hover:fill-current hover:text-red-600  focus:outline-none  transition duration-300 transform active:scale-95 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px">
                    <path d="M0 0h24v24H0V0z" fill="none"></path>
                    <path d="M15.5 4l-1-1h-5l-1 1H5v2h14V4zM6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9z"></path>
                </svg>
            </button>
        </td>
    </tr>
@endforeach
</tbody>
