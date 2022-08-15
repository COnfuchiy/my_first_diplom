<form class="mt-5">
    <div>
        <label for="chat_name" class="block text-sm text-gray-700  dark:text-gray-700">
            {{__('telegram.chat_name')}}
        </label>
        <input placeholder="{{__('telegram.chat_name_placeholder')}}" name="chat_name" id="chat_name" type="text" required
               class="block w-full px-3 py-2 mt-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40">
    </div>

    <div class="mt-4">
        <label for="related_username" class="block text-sm text-gray-700 dark:text-gray-200">
            {{__('telegram.related_username')}}
        </label>
        <input placeholder="@username" type="text" name="related_username" id="related_username" required
               class="block w-full px-3 py-2 mt-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40">
    </div>

    <div class="mt-4">
        <h1 class="text-xs font-medium text-gray-400">{{__('telegram.chat_type')}}</h1>

        <div class="mt-4 space-y-5">
            <div class="flex items-center space-x-3 cursor-pointer" @click="chat_type =!chat_type">
                <p class="text-gray-500">{{__('telegram.type_private')}}</p>

                <div class="bg-indigo-500 relative w-10 h-5 transition duration-200 ease-linear rounded-full">
                    <label for="chat_type"
                           @click="chat_type =!chat_type"
                           class="absolute left-0 w-5 h-5 mb-2 transition duration-100 ease-linear transform bg-white border-2 rounded-full cursor-pointer"
                           :class="[chat_type ? 'translate-x-full border-indigo-500' : 'translate-x-0 border-indigo-500']"></label>
                    <input type="checkbox" name="chat_type" id="chat_type"
                           class="hidden w-full h-full rounded-full appearance-none active:outline-none focus:outline-none"/>
                </div>

                <p class="text-gray-500">{{__('telegram.type_group')}}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end mt-6">
        <button type="button"
                @click="submitForm"
                class="px-3 py-2 text-sm tracking-wide text-white capitalize transition-colors duration-200 transform bg-indigo-500 rounded-md dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:bg-indigo-700 hover:bg-indigo-600 focus:outline-none focus:bg-indigo-500 focus:ring focus:ring-indigo-300 focus:ring-opacity-50">
            {{__('form.add')}}
        </button>
    </div>
</form>
