<tbody class="border-b border-gray-200 divide-y divide-gray-200 text-sm sm:border-t" style="line-height: 2.25rem"
       data-total-page-count="{{$totalPageCount}}">
@foreach($rowsData as $site)
    <tr class="user-site" data-site-id="{{$site->id}}">
        <td class="sm:w-1/5 lg:w-1/6">
            <a href="{{route('sites.index',['searchRequest'=>$site->domain])}}"
               class="text-indigo-600">{{$site->domain}}</a>
        </td>
        <td class="w-1/6 sm:table-cell whitespace-nowrap">
            {{$site->success_monitored_pages_count}}/{{$site->error_monitored_pages_count}}/{{$site->not_monitored_pages_count}}

        </td>
        <td class="w-1/6 sm:table-cell">
            <a href="{{route('availability-reports.index',['searchRequest'=>$site->domain])}}"
               class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="sm:table-cell">
            <a href="{{route('performance-reports.index',['searchRequest'=>$site->domain])}}"
            class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="text-right">
            <a href="{{route('statistic',['siteId'=>$site->id])}}"
               class="text-indigo-600">{{__('form.view')}}</a>
        </td>
        <td class="w-1/6 text-right">
            @if($site->last_monitoring_time)
                {{$site->last_monitoring_time}}
            @else
                -
            @endif
        </td>
    </tr>
@endforeach
</tbody>
