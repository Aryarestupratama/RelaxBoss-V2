<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Relaxboss - Ketenangan untuk Profesional</title>

    {{-- [BARU] Favicon untuk logo di tab browser --}}
    <link rel="icon" src="{{ asset('build/assets/icon-relaxboss.png') }}">

    <!-- Aset Tambahan -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- Scripts & CSS Utama (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    {{-- [DIUBAH] Style untuk menandai link navigasi yang aktif & mencegah layout shift --}}
    <style>
        /* * Menambahkan border transparan ke semua link navigasi.
         * Ini memastikan ada ruang untuk border aktif tanpa menyebabkan konten melompat (layout shift).
         */
        nav a[href^="#"] {
            border-bottom-width: 2px;
            border-color: transparent;
            transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out;
        }

        .nav-active {
            color: #007BFF;
            font-weight: 600;
            border-color: #007BFF; /* Ini akan memunculkan underline yang selaras dengan warna teks aktif */
        }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased bg-white">
    
    @php
        // Logika untuk menentukan item navigasi untuk halaman depan
        $navItems = [
            ['label' => 'Beranda', 'href' => '#beranda'],
            ['label' => 'Fitur', 'href' => '#fitur'],
            ['label' => 'Review', 'href' => '#review'],
        ];
    @endphp

    <!-- Memanggil Komponen Navbar -->
    {{-- Pastikan komponen navbar ini me-render <nav> dan <a> dengan href="#..." --}}
    <x-navbar :navItems="$navItems" />

    <!-- Konten Utama Halaman (diisi oleh welcome.blade.php) -->
    <main>
        @yield('content')
    </main>

    <!-- Memanggil Komponen Footer -->
    <x-footer />

    <!-- Inisialisasi AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
        });
    </script>

    {{-- [BARU] Script untuk membuat link aktif saat section di-scroll --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Pilih semua link navigasi yang mengarah ke section (href="#...")
            // Catatan: Anda mungkin perlu menyesuaikan selector 'nav a' jika struktur HTML navbar Anda berbeda.
            const navLinks = document.querySelectorAll('nav a[href^="#"]');
            
            // Pilih semua section yang memiliki ID di dalam <main>
            const sections = document.querySelectorAll('main section[id]');

            // Opsi untuk Intersection Observer
            const observerOptions = {
                root: null, // Menggunakan viewport sebagai root
                rootMargin: '0px',
                threshold: 0.4 // Anggap aktif jika 40% section terlihat
            };

            // Fungsi yang akan dijalankan ketika section masuk/keluar viewport
            const observerCallback = (entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Dapatkan ID dari section yang sedang terlihat
                        const currentSectionId = entry.target.getAttribute('id');

                        // Hapus kelas 'nav-active' dari semua link
                        navLinks.forEach(link => {
                            link.classList.remove('nav-active');
                        });

                        // Cari link yang cocok dan tambahkan kelas 'nav-active'
                        const activeLink = document.querySelector(`nav a[href="#${currentSectionId}"]`);
                        if (activeLink) {
                            activeLink.classList.add('nav-active');
                        }
                    }
                });
            };

            // Buat instance Intersection Observer
            const observer = new IntersectionObserver(observerCallback, observerOptions);

            // Amati setiap section
            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>
</body>
</html>

