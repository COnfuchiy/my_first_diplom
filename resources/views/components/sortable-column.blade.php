@props(['columnName','columnDisplayName','defaultSortType','isActive'])

<th scope="col" {{$attributes}}>
    <a href="#" class="{{$columnName}}-sorting" data-sort="{{$defaultSortType}}"
       data-sort-default="{{$defaultSortType}}">
        {{$slot}}
        <img class="{{!$isActive?'hidden':''}} h-5 w-5 inline {{$columnName}}-sorting-indicator"
             src="{{$defaultSortType==='asc'?'/img/arrow-up.svg':'/img/arrow-down.svg'}}" alt=""/>
    </a>
</th>
