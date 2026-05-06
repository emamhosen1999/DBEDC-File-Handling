<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight iridescent-text">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <livewire:task-index />
</x-app-layout>
