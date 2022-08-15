@props(['currentPage'=>1,'totalPageCount','timeInterval','monitoringCount'])


<div class="sm:flex-1 sm:flex sm:items-center sm:justify-between">
    <div>
        <p class="text-sm text-gray-700">
            <span class="monitoring-times font-medium">{{__('monitoring.monitoring_times',['times'=>$monitoringCount,'diff_date'=>$timeInterval])}}</span>
        </p>
    </div>
    <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <a href="#"
               class="{{$currentPage === 1 ?'hidden':''}} prev-page relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Previous</span>
                <!-- Heroicon name: solid/chevron-left -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
            <span class="pages-links">
                @php
                $pageRange = [];
                if($totalPageCount!==0){
                    if($totalPageCount>9){
                         if($currentPage<=5){
                             $pageRange = [1,2,3,4,5,6,7,'...',$totalPageCount];
                         }
                         elseif($currentPage+ 5 > $totalPageCount){
                                 $pageRange = [1,'...',$totalPageCount-6,$totalPageCount-5,$totalPageCount-4,$totalPageCount-3,$totalPageCount-2, $totalPageCount-1,$totalPageCount];
                             }
                             else{
                                $pageRange = [1,'...',$currentPage-2,$currentPage-1,$currentPage,$currentPage+1,$currentPage+2,'...',$totalPageCount];

                             }
                    }
                    else{
                        $pageRange = $totalPageCount>1?range(1,$totalPageCount):[1];
                    }
                }
            @endphp
                @foreach($pageRange as $page)
                    @if($page === $currentPage)
                        <a href="#" aria-current="page"
                           class="other-page z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        {{$page}}</a>
                    @elseif($page ==='...')
                        <span
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"> ... </span>
                    @else
                        <a href="#"
                           class="other-page bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        {{$page}}
                    </a>
                    @endif
                @endforeach
            </span>
            <a href="#"
               class="{{$currentPage === $totalPageCount ?'hidden':''}} next-page relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <!-- Heroicon name: solid/chevron-right -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                     aria-hidden="true">
                    <path fill-rule="evenodd"
                          d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                          clip-rule="evenodd"/>
                </svg>
            </a>
        </nav>
    </div>
</div>

