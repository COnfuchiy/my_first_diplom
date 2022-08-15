@props(['currentPage'=>1    ,'totalPageCount'])

<nav class="border-t border-gray-200 px-4 flex items-center justify-between sm:px-0">
    <div class="-mt-px w-0 flex-1 flex">
        <a href="#"
           class="{{$currentPage === 1 ?'hidden':''}} prev-page border-t-2 border-transparent pt-4 pr-1 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            <img class="inline mr-3 h-5 w-5 text-gray-400" src="/img/arrow-narrow-left.svg" alt=""/>
            {{__('pagination.previous')}}

        </a>
    </div>
    <div class="pages-links hidden md:-mt-px md:flex">
        @php
            $pageRange = [];
             if($totalPageCount!==0){
                 if($totalPageCount>9){
                      if($currentPage<=5){
                          $pageRange = [1,2,3,4,5,6,7,'...',$totalPageCount];
                      }
                      elseif($currentPage+5>$totalPageCount){
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
                <a href="#"
                   class="current-page border-indigo-500 text-indigo-600 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                    {{$page}}
                </a>
            @elseif($page ==='...')
                <span
                    class="border-transparent text-gray-500 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium"> ... </span>
            @else
                <a href="#"
                   class="other-page border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 border-t-2 pt-4 px-4 inline-flex items-center text-sm font-medium">
                    {{$page}}
                </a>
            @endif
        @endforeach
    </div>
    <div class="-mt-px w-0 flex-1 flex justify-end">
        <a href="#"
           class="{{$currentPage === $totalPageCount || $totalPageCount===0?'hidden':''}} next-page border-t-2 border-transparent pt-4 pl-1 inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            {{__('pagination.next')}}
            <img class="inline ml-3 h-5 w-5 text-gray-400" src="/img/arrow-narrow-right.svg" alt=""/>
        </a>
    </div>
</nav>
