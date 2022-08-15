<div class="telegram-chats p-6 bg-white border-b border-gray-200">
    <table class="w-full text-gray-500" >
        <thead class="text-sm text-gray-500 text-left ">
        <tr>
            <th scope="col" class="w-1/6 pr-8 py-3 font-normal">
                {{__('Telegram ID')}}
            </th>
            <th scope="col" class="w-1/5 pr-8 py-3 font-normal sm:table-cell">
                {{__('telegram.chat_name')}}
            </th>
            <th scope="col" class="w-1/5 py-3 font-normal sm:table-cell">
                {{__('telegram.related_username')}}
            </th>
            <th scope="col" class="w-1/6 py-3 font-normal sm:table-cell">
                {{__('telegram.chat_type')}}
            </th>
            <th scope="col" class="w-1/6 py-3 font-normal">
                {{__('telegram.confirmation')}}
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody class="border-b border-gray-200 divide-y divide-gray-200 text-sm sm:border-t" style="line-height: 40px">
            @foreach($telegramChats as $chat)
                @include('components.user-settings.telegram-chats-table-row')
            @endforeach
        </tbody>
    </table>
</div>
