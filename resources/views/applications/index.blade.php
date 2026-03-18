<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Applications</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:application-tracker />
            <livewire:application-modal />
            <livewire:application-detail />
        </div>
    </div>
</x-app-layout>
