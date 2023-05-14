<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- AlpineJS -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>

        <!-- Animations -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/TextPlugin.min.js" defer></script>

        <!-- Icons -->
        <link href="{{ asset('fontawesome/css/fontawesome.css') }}" rel="stylesheet">
        <link href="{{ asset('fontawesome/css/brands.css') }}" rel="stylesheet">
        <link href="{{ asset('fontawesome/css/solid.css') }}" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-cover bg-left-top bg-[url('../../public/images/background3.svg')]">
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <livewire:scripts />
    </body>
</html>
