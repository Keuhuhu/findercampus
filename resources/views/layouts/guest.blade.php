<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FinderCampus') }}</title>

    <!-- Fonts -->
    
    

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 p-4">
    <div class="mb-6">
        <a href="/">
            <div class="text-3xl font-extrabold text-blue-800">Finder<span class="text-amber-500">Campus</span></div>
        </a>
    </div>

    <!-- Container utama dihapus agar tampilan form register/login yang mengaturnya sendiri -->
    <div class="w-full flex justify-center">
        {{ $slot }}
    </div>
</body>
</html>
