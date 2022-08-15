<tbody class="border-b border-gray-200 divide-y divide-gray-200 text-sm sm:border-t"
       data-total-page-count="{{$totalPageCount}}">
@foreach($rowsData as $report)
    <tr class="availability-report
            @if((int)$report->http_code>=400 || (int)$report->http_code<200)
                    bg-red-100
            @elseif((int)$report->http_code>300)
                    bg-yellow-100
            @else
                    bg-green-100
            @endif
        " data-report-id="{{$report->id}}">
        <td class="py-6 pr-8">
            <a href="#"
               class="text-indigo-600">{{$report->domain}}</a>
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->path}}
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->last_monitoring_time}}
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->http_code}}
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->monitoring_sequence}}
        </td>
    </tr>
@endforeach
</tbody>
