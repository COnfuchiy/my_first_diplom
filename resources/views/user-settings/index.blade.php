<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('telegram.telegram_chats') }}
        </h2>
    </x-slot>
    <div class="p-12">
        @include('components.user-settings.telegram-modal-add')

        @include('components.user-settings.view-telegram-chats-table')

        @include('components.user-settings.telegram-modal-confirm')

    </div>
</x-app-layout>
<script src="/js/user-settings/ConfirmCounter.js"></script>
<script src="/js/user-settings/TelegramFunctions.js"></script>
<script src="/js/user-settings/user-settings.js"></script>
