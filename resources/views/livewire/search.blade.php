<x-app-layout>
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <!-- Disabled for the time being during the design stage -->
        @if (Route::has('login') && false)
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right">
            @auth
            <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

            @if (Route::has('register'))
            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
            @endif
            @endauth
        </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <!-- Logo -->
            <div class="flex justify-center">
            </div>

            <!-- Search -->
            <div class="mt-16">
                <x-text-input wire:model.debounce.500ms="search" />
            </div>
        </div>
    </div>
</x-app-layout>
