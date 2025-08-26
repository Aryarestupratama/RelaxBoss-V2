<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Panel Psikolog</title>

    <!-- Scripts and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="relative h-screen flex overflow-hidden bg-slate-100">

        <!-- Sidebar Khusus Psikolog -->
        <x-sidebar.psychologist />

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <!-- Header untuk Tampilan Mobile -->
            <header class="bg-white/80 backdrop-blur-lg shadow-sm md:hidden sticky top-0 z-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <a href="{{ route('psikolog.dashboard') }}">
                            <span class="font-bold text-xl text-[#007BFF]">RelaxBoss</span>
                        </a>
                        <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>