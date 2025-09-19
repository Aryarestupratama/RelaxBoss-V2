<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'RelaxBoss') }}</title>

    <link rel="icon" href="{{ asset('storage/components/icon-relaxboss.png') }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- [PENTING] Hanya panggil Vite. Alpine.js akan di-bundle di dalamnya. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- SweetAlert2 (tetap di sini karena ini library terpisah) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-screen bg-slate-50">
        
        @include('layouts.navigation')

        <main class="pt-16">
            {{ $slot }}
        </main>

    </div>
    
    <footer class="py-4 text-center text-sm text-gray-500">
        <p>&copy; {{ date('Y') }} RelaxBoss. Seluruh hak cipta dilindungi.</p>
    </footer>

    <audio id="pomodoro-finish-sound" src="{{ asset('sounds/pomodoro-complete.mp3') }}" preload="auto"></audio>

    @stack('scripts')
</body>
</html>

