<tbody class="border-b border-gray-200 divide-y divide-gray-200 text-sm sm:border-t"
       data-total-page-count="{{$totalPageCount}}">
@foreach($rowsData as $report)
    <tr class="performance-report" data-report-id="{{$report->id}}">
        <td class="py-6 pr-8">
            <a href="#"
               class="text-indigo-600">{{$report->domain}}</a>
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->path}}
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->monitoring_time}}
        </td>
        <td class="py-1 sm:table-cell text-left">
            {{$report->total_score}}
        </td>
        <td class="py-1 sm:table-cell">
            {{$report->getViewStrategy()}}
        </td>
    </tr>
@endforeach
</tbody>
