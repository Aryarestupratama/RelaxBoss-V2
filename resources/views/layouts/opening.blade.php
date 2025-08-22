<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale-1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Ketenangan untuk Profesional</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Font Awesome (untuk ikon di footer) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

        @stack('styles')
    </head>
    <body class="font-sans text-gray-800 antialiased bg-white">
        
        @php
            // Logika untuk menentukan item navigasi kini ada di sini,
            // dan dikirim sebagai prop ke komponen navbar.
            $navItems = [
                ['label' => 'Beranda', 'href' => '#beranda'],
                ['label' => 'Fitur', 'href' => '#fitur'],
                ['label' => 'Review', 'href' => '#review'],
            ];
        @endphp

        <!-- Memanggil Komponen Navbar -->
        <x-navbar :navItems="$navItems" />

        <!-- MAIN CONTENT -->
        <main>
            @yield('content')
        </main>

        <!-- Memanggil Komponen Footer -->
        <x-footer />

    </body>
</html>
