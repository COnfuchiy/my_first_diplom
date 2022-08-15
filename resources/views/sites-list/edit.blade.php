<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('sites.update').' '.$site->domain }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto">
        <!-- Validation Errors -->
        <x-view-validation-errors class="mb-4" :errors="$errors"/>

        <form method="POST" action="{{ route('sites.update', ['site' => $site->id]) }}">
            @csrf
            @method('PATCH')
            @include('components.sites-list.form-add')
        </form>
    </div>
</x-app-layout>
<script src="/js/site-form-add.js"></script>
