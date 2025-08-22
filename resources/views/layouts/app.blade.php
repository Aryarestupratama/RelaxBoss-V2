<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="{{ asset('storage/components/icon-relaxboss1.png') }}" type="image/png">

    <!-- Vite Scripts and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-screen bg-slate-50">
        
        <!-- Navbar Bawaan Breeze untuk User yang Login -->
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- RelaxMate Chatbot Component (jika Anda punya) -->
        @include('partials.relaxmate-global')
    </div>
    
    <footer class="py-4 text-center text-sm text-gray-500">
        <p>&copy; {{ date('Y') }} RelaxBoss. Seluruh hak cipta dilindungi.</p>
    </footer>

    @stack('scripts')
</body>
</html>
