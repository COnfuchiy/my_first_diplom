<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('sites.create') }}
        </h1>
    </x-slot>
    <div class="max-w-4xl mx-auto py-10">
        <!-- Validation Errors -->
        <x-view-validation-errors class="mb-4" :errors="$errors"/>
        <form method="POST" class="site-form" action="{{ route('sites.store') }}">
            @csrf
            @include('components.sites-list.form-add')
        </form>
    </div>
</x-app-layout>
<script src="/js/site-form-add.js"></script>
