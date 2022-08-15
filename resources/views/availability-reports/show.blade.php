@php
    $carbonLastDatetime = Carbon\Carbon::createFromTimeString($report->last_monitoring_time);
@endphp
<div>{{__('monitoring.report_show_url',['url'=>$report->url])}}</div>
<div>{{__('monitoring.report_show_date',['date'=>$carbonLastDatetime->isoFormat('D MMMM YYYY'),'time'=>$carbonLastDatetime->isoFormat('h:mm:ss')])}}</div>
<div>{{__('monitoring.availability_report_show_code',['code'=>$report->http_code])}}</div>
@if($report->monitoring_sequence!==1)
    @php
        $carbonFirstDatetime = Carbon\Carbon::createFromTimeString($report->first_monitoring_time);
    @endphp
    <div>{{__('monitoring.availability_report_show_sequence',['sequence'=>$report->monitoring_sequence,'date'=>$carbonFirstDatetime->isoFormat('D MMMM YYYY'),'time'=>$carbonFirstDatetime->isoFormat('h:mm:ss')])}}</div>
@endif
@if($report->message)
    <div>{{__('monitoring.availability_report_show_message',['message'=>$report->message])}}</div>
@endif
