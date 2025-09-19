<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- [DIUBAH] Judul halaman dibuat dinamis --}}
    <title>{{ $title ?? 'Selamat Datang' }} - Relaxboss</title>

    {{-- [BARU] Favicon untuk logo di tab browser --}}
    <link rel="icon" src="{{ asset('build/assets/icon-relaxboss.png') }}">

    <!-- Aset Tambahan untuk Desain -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts & CSS Utama (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    
    <!-- Container Utama -->
    <div class="min-h-screen flex flex-col md:flex-row bg-slate-50">

        <!-- Bagian Kiri (Branding) -->
        <div class="hidden md:flex md:w-1/2 bg-[#007BFF] text-white p-12 flex-col justify-between relative overflow-hidden">
            <!-- Elemen Dekoratif SVG -->
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/10 rounded-full mix-blend-overlay"></div>
            <div class="absolute -bottom-24 -right-12 w-96 h-96 bg-white/5 rounded-full mix-blend-overlay"></div>
            
            <div class="relative z-10" data-aos="fade-right">
                <a href="/" class="font-bold text-2xl tracking-wider">
                    RelaxBoss
                </a>
                <h1 class="mt-12 text-4xl font-bold leading-tight">
                    Temukan Ketenangan, Raih Potensi Penuh Anda.
                </h1>
                <p class="mt-4 text-blue-100 max-w-md">
                    Bergabunglah dengan ribuan orang lainnya yang telah mengubah hidup mereka.
                </p>
            </div>
            <div class="relative z-10 text-sm text-blue-200">
                &copy; {{ date('Y') }} RelaxBoss. All Rights Reserved.
            </div>
        </div>

        <!-- Bagian Kanan (Form) -->
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-6" data-aos="fade-left" data-aos-delay="200">
            <!-- Logo untuk Tampilan Mobile -->
            <div class="md:hidden mb-6">
                <a href="/" class="font-bold text-2xl tracking-wider text-[#007BFF]">
                    RelaxBoss
                </a>
            </div>

            <!-- Card Form -->
            <div class="w-full sm:max-w-md bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-8 md:px-10">
                      {{ $slot }}
                </div>
            </div>
        </div>

    </div>

    <!-- Inisialisasi AOS & SweetAlert -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });

        // Sistem Notifikasi Menggunakan SweetAlert
        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        @endif
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
        @endif
    </script>
</body>
</html>

