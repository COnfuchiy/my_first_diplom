<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('monitoring.site_statistic',['site'=>$site->domain])}}
        </h2>
    </x-slot>
    <div class="">
        <div class="ml-6 w-full flex items-center justify-start">
            <div class="items-baseline mr-6"><p>{{__('monitoring.search_by_date')}}</p></div>
            @include('components.datetimepicker')
        </div>
    <!-- Tabs -->
        <ul id="statistic-tabs" class="inline-flex w-full px-1 pt-2 ">
            <li class="px-4 py-2 -mb-px font-semibold text-gray-800 border-b-2 border-blue-400 rounded-t opacity-50"><a
                    id="default-tab" href="#availability-statistic-tab">{{__('monitoring.availability_statistic')}}</a></li>
            <li class="px-4 py-2 font-semibold text-gray-800 rounded-t opacity-50"><a href="#pages-statistic-tab">{{__('monitoring.pages_statistic')}}</a>
            </li>
            <li class="px-4 py-2 font-semibold text-gray-800 rounded-t opacity-50"><a href="#psi-statistic-tab">{{__('monitoring.performance_statistic')}}</a>
            </li>
        </ul>


        <div id="tab-contents">
            <div id="availability-statistic-tab" class="availability-statistic-container">
                @include('components.statistic.availability-statistic-chart')
            </div>
            <div id="pages-statistic-tab" class="hidden p-4">
                @include('components.site-pages-select')
                <div class="pages-statistic-container">
                </div>
            </div>
            <div id="psi-statistic-tab" class="hidden p-4">
                @include('components.site-pages-select')
                <div class="psi-statistic-container">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="/js/statisticData.js"></script>
<script>
    document.statisticData = new StatisticData({{$totalPageCount}});
    document.statisticData.initHandlers();
</script>

<script>
    let tabsLinks = $("#statistic-tabs a");
    tabsLinks.each(function () {
        $(this).on("click", function (e) {
            e.preventDefault();

            let tabName = $(this).attr("href");

            let tabContents = $("#tab-contents");

            for (let i = 0; i < tabContents[0].children.length; i++) {

                tabsLinks[i].parentElement.classList.remove("border-blue-400", "border-b", "-mb-px", "opacity-100");
                tabContents[0].children[i].classList.remove("hidden");
                if ("#" + tabContents[0].children[i].id === tabName) {
                    continue;
                }
                tabContents[0].children[i].classList.add("hidden");

            }
            e.target.parentElement.classList.add("border-blue-400", "border-b-4", "-mb-px", "opacity-100");
        });
    });
    $("#default-tab").click();
</script>
