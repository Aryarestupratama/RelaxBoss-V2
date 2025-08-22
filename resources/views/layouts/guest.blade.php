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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Container Utama -->
        <div class="min-h-screen flex flex-col md:flex-row bg-gray-100">

            <!-- Bagian Kiri (Branding) - Terlihat di layar medium ke atas -->
            <div class="hidden md:flex md:w-1/2 bg-[#007BFF] text-white p-12 flex-col justify-between">
                <div>
                    <a href="/" class="font-bold text-2xl">
                        RelaxBoss
                    </a>
                    <h1 class="mt-12 text-4xl font-bold leading-tight">
                        Temukan Ketenangan, Raih Potensi Penuh Anda.
                    </h1>
                    <p class="mt-4 text-blue-100">
                        Bergabunglah dengan ribuan orang lainnya yang telah mengubah hidup mereka.
                    </p>
                </div>
                <div class="text-sm text-blue-200">
                    &copy; {{ date('Y') }} RelaxBoss. All Rights Reserved.
                </div>
            </div>

            <!-- Bagian Kanan (Form) -->
            <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-6">
                <!-- Logo untuk Tampilan Mobile -->
                <div class="md:hidden mb-6">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
                </div>

                <!-- Card Form -->
                <div class="w-full sm:max-w-md bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-8 md:px-10">
                         {{ $slot }}
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
