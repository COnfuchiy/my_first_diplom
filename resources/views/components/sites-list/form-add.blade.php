<div class="shadow sm:rounded-md sm:overflow-hidden">
    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
        <h2 class="text-xl font-bold text-gray-900">{{__('sites.main_properties')}}</h2>
        <div class="grid grid-cols-3 gap-6">
            <!-- Domain -->
            <div class="col-span-3">
                <label for="domain" class="block text-sm font-medium text-gray-700">
                    {{__('sites.domain')}}
                </label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="domain-protocol inline-flex items-center py-2 px-3 rounded-l-md border border-gray-300 bg-gray-50 text-gray-500 text-sm">https://</span>
                    <input id="domain"
                           class="outline-none border border-gray-300 border-l-0 px-2 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300"
                           type="text" name="domain" value="{{old('domain',$site->domain)}}" required
                           placeholder="www.example.com" autofocus/>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900">{{__('sites.select_page_input_type')}}</h2>
        <!-- Sitemap radio-tapok -->
        <div class="mt-4 space-y-4">
            <div class="flex items-center">
                <input id="page-select-type-manual"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" type="radio"
                       name="page_select_type"
                       @if(old('page_select_type') ==="manual" || $site->sitemap_url===null && sizeof($site->pages))
                       checked
                       @endif
                       value="manual"
                />
                <label for="page-select-type-manual" class="ml-3 block text-sm font-medium text-gray-700"
                >{{__('sites.page_select_manual_label')}}</label>
            </div>
            <div class="flex items-center">
                <input id="page_select_type_sitemap"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" type="radio"
                       name="page_select_type"
                       value="sitemap"
                       @if(old('page_select_type') ==="sitemap" || $site->sitemap_url!==null)
                       checked
                    @endif
                />
                <label for="page_select_type_sitemap"
                       class="ml-3 block text-sm font-medium text-gray-700">{{__('sites.page_select_sitemap_label')}}</label>
            </div>
        </div>
        <!-- Manual input -->
        <div class="col-span-6 sm:col-span-4" style="display: none">
            <label for="manual-input-text-area" class="block text-sm font-medium text-gray-700"
            >{{__('sites.manual_input_label')}}</label>
            <div class="mt-1">
                    <textarea rows="4" name="manual_input_text_area" id="manual-input-text-area"
                              class="outline-none border border-gray-300 p-2 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md">{{old('manual_input_text_area',join("\n",$site->getPagesToArray()))}}</textarea>
            </div>
        </div>
        <!-- Sitemap url -->
        <div class="col-span-6 sm:col-span-4" style="display: none">
            <label for="sitemap-url"
                   class="block text-sm font-medium text-gray-700">{{__('sites.sitemap_url_label')}}</label>
            <div class="mt-1">
                <input type="text" name="sitemap_url" id="sitemap-url" value="{{old('sitemap_url',$site->sitemap_url)}}"
                       class="outline-none border border-gray-300 p-2 shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"/>
            </div>
        </div>
        <h2 class="text-xl font-bold text-gray-900">{{__('sites.monitoring_frequency')}}</h2>
        <!-- Domain frequency -->
        <div class="col-span-6 sm:col-span-4">
            <label for="domain-frequency"
                   class="block text-sm font-medium text-gray-700">{{__('sites.monitoring_frequency_label')}}</label>

            <input id="domain-frequency"
                   class="outline-none border border-gray-300 p-2 mt-1 disabled:bg-slate-50 disabled:text-slate-500 disabled:border-slate-200 disabled:shadow-none block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" name="monitoring_period" min="5" {{isset($site->id) && $site->id?'disabled':''}}
                   value="{{old('monitoring_period',$site->monitoring_period)}}" required/>
        </div>

        <h2 class="text-xl font-bold text-gray-900">{{__('sites.telegram_chat')}}</h2>

        <!-- Telegram chat -->
        <div>
            <label for="telegram-chat"
                   class="block text-sm font-medium text-gray-700">{{__('sites.telegram_chat_label')}}</label>

            <select id="telegram-chat"
                    class="hover:outline-none cursor-pointer outline-none border border-gray-300 p-2 mt-1 block w-full bg-white rounded-md shadow-sm sm:text-sm"
                    name="chat_id">
                <option>{{__('sites.choose_telegram_chat')}}</option>
                @foreach($chats as $chat)
                    <option value="{{$chat->telegram_id}}"
                            @if(old('chat_id')===$chat->telegram_id || $site->chat_id===$chat->telegram_id)
                            selected
                        @endif
                    >{{$chat->chat_name}}</option>
                @endforeach
            </select>
        </div>

        <h2 class="text-2xl font-bold text-gray-900">{{__('sites.optional_properties')}}</h2>
        <!-- Request timeout -->
        <div class="col-span-6 sm:col-span-4">

            <label for="timeout"
                   class="block text-sm font-medium text-gray-700">{{__('sites.request_timeout_label')}}</label>
            <input id="timeout" min="0" max="10"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" name="timeout" value="{{old('timeout', $site->timeout)}}"/>
        </div>

        <h2 class="text-xl font-bold text-gray-900">{{__('sites.ssl_settings')}}</h2>

        <!-- SSL Check -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="ssl-check"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                       type="checkbox" value="1" name="ssl_check"
                       @if(old('ssl_check') || $site->ssl_check)
                       checked
                    @endif
                />
            </div>
            <div class="ml-3 text-sm">
                <label for="ssl-check"
                       class="font-medium text-gray-700">{{__('sites.ssl_verification_label')}}</label>
            </div>
        </div>

        <!-- SSL notify number days -->
        <div style="display: none">
            <label for="ssl-notify-num-days"

                   class="block text-sm font-medium text-gray-700">{{__('sites.number_day_ssl_notify_label')}}</label>

            <input id="ssl-notify-num-days"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" name="ssl_notify_num_days"
                   value="{{old('ssl_notify_num_days', $site->ssl_notify_num_days)}}"/>
        </div>
        <h2 class="text-xl font-bold text-gray-900">{{__('sites.page_speed_insight_monitoring')}}</h2>
        <!-- PSI mobile check -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="seo-psi-mobile-check"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                       type="checkbox" value="1" name="seo_psi_mobile_check"
                       @if(old('seo_psi_mobile_check') || $site->seo_psi_mobile_check)
                       checked
                    @endif
                />
            </div>
            <div class="ml-3 text-sm">
                <label for="seo-psi-mobile-check"
                       class="font-medium text-gray-700">{{__('sites.psi_mobile_performance')}}</label>
            </div>
        </div>
        <!-- PSI mobile minimal value -->
        <div style="display: none">
            <label for="seo-psi-mobile-min-value"
                   class="block text-sm font-medium text-gray-700">{{__('sites.psi_mobile_minimal_value')}}</label>

            <input id="seo-psi-mobile-min-value"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" min="0" max="100"
                   value="{{old('seo_psi_mobile_min_value', $site->seo_psi_mobile_min_value)}}"
                   name="seo_psi_mobile_min_value"/>
        </div>
        <!-- PSI desktop check -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="seo-psi-desktop-check"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                       type="checkbox" value="1" name="seo_psi_desktop_check"
                       @if(old('seo_psi_desktop_check') || $site->seo_psi_desktop_check)
                       checked
                    @endif
                />
            </div>
            <div class="ml-3 text-sm">
                <label for="seo-psi-desktop-check"
                       class="font-medium text-gray-700">{{__('sites.psi_desktop_performance')}}</label>
            </div>
        </div>
        <!-- PSI desktop minimal value -->
        <div style="display: none">
            <label for="seo-psi-desktop-min-value"
                   class="block text-sm font-medium text-gray-700">{{__('sites.psi_desktop_minimal_value')}}</label>

            <input id="seo-psi-desktop-min-value"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" min="0" max="100"
                   value="{{old('seo_psi_desktop_min_value', $site->seo_psi_desktop_min_value)}}"
                   name="seo_psi_desktop_min_value">
        </div>

        <!-- Meta-tags check -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="meta-tags-check"
                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                       type="checkbox" value="1" name="meta_check"
                       @if(old('meta_check') || $site->meta_check)
                       checked
                    @endif
                />
            </div>
            <div class="ml-3 text-sm">
                <label for="meta-tags-check"
                       class="font-medium text-gray-700">{{__('sites.check_meta_tags_label')}}</label>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-900">{{__('sites.reports_clear_time')}}</h2>
        <!-- Availability report clear number days -->
        <div>
            <label for="availability-report-clear-num-days"
                   class="block text-sm font-medium text-gray-700">{{__('sites.availability_reports_clear_time')}}</label>

            <input id="availability-report-clear-num-days"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" min="0"
                   value="{{old('availability_report_clear_num_days', $site->availability_report_clear_num_days)}}"
                   name="availability_report_clear_num_days"/>
        </div>
        <!-- Performance report clear number days -->
        <div>
            <label for="performance-report-clear-num-days"
                   class="block text-sm font-medium text-gray-700">{{__('sites.performance_reports_clear_time')}}</label>

            <input id="performance-report-clear-num-days"
                   class="outline-none border border-gray-300 p-2 mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                   type="number" min="0"
                   value="{{old('performance_report_clear_num_days', $site->performance_report_clear_num_days)}}"
                   name="performance_report_clear_num_days"/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit"
                    class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('form.create') }}
            </button>
        </div>
    </div>
</div>

