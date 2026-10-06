<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FinderCampus') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Toast Flash Messages -->
    @if(session('success') || session('error') || session('warning'))
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
            @if(session('success'))
                <div data-flash class="pointer-events-auto flex items-start gap-3 bg-white border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div data-flash class="pointer-events-auto flex items-start gap-3 bg-white border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif
            @if(session('warning'))
                <div data-flash class="pointer-events-auto flex items-start gap-3 bg-white border border-amber-200 text-amber-800 px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Navbar -->
    <x-navbar />

    <!-- Page Content -->
    <main class="flex-grow w-full max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (isset($header) || isset($breadcrumb))
            <header class="mb-6 px-4 sm:px-0">
                @if (isset($breadcrumb))
                    {{ $breadcrumb }}
                @endif
                @if (isset($header))
                    <div class="mt-2 text-xl font-bold text-gray-800">
                        {{ $header }}
                    </div>
                @endif
            </header>
        @endif

        <div class="px-4 sm:px-0">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <x-footer />
</body>
</html>
