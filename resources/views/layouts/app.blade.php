<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Payments') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('Snapay.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            [x-cloak] { display: none !important; }
            @media (max-width: 640px) {
                .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                .no-scrollbar::-webkit-scrollbar { display: none; }
            }
        </style>
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div x-data class="min-h-screen bg-gray-100">
            <div class="flex">
                @include('layouts.sidebar')

                <div class="flex-1 min-w-0">
                    @include('layouts.navigation')

                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-secondary">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="p-4 sm:p-6 lg:p-8">
                        {{ $slot }}
                    </main>
                </div>
            </div>

            <!-- Mobile sidebar overlay -->
            <div x-cloak x-show="$store.ui.mobileSidebar" x-transition.opacity class="fixed inset-0 bg-black/40 z-40 lg:hidden" @click="$store.ui.mobileSidebar = false"></div>

            <!-- Mobile sidebar drawer -->
            <aside x-cloak x-show="$store.ui.mobileSidebar" x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 lg:hidden overflow-y-auto">
                @include('layouts.sidebar-mobile')
            </aside>
        </div>
    </body>
</html>
