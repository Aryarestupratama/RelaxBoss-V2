@extends('layouts.opening')

@section('content')

    {{-- HERO SECTION --}}
        <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 min-h-screen flex items-center bg-white overflow-hidden" id="beranda">
            <div class="absolute inset-0 -z-0">
                <div class="absolute -top-48 -left-48 w-[32rem] h-[32rem] bg-blue-100/50 rounded-full blur-3xl opacity-60"></div>
                <div class="absolute -bottom-48 -right-48 w-[32rem] h-[32rem] bg-teal-100/40 rounded-full blur-3xl opacity-50"></div>
            </div>
            
            <div class="relative z-10 container mx-auto px-4">
                <div class="flex flex-col-reverse md:flex-row items-center gap-12">
                    <div class="w-full md:w-1/2 text-center md:text-left" data-aos="fade-right">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4 leading-tight">
                            <span class="block text-gray-700 font-medium">Stres di kantor?</span>
                            <span class="bg-gradient-to-r from-[#007BFF] to-cyan-500 bg-clip-text text-transparent">Relax-in aja, boss.</span>
                        </h1>
                        <h2 class="text-gray-600 text-lg md:text-xl max-w-xl mx-auto md:mx-0 mb-8 leading-relaxed" data-aos-delay="100">
                            Ambil kembali kendali atas hari Anda. Platform kami membantu Anda mengubah tekanan menjadi produktivitas yang tenang dan fokus yang tajam.
                        </h2>
                        <div class="mt-8 flex flex-col sm:flex-row justify-center md:justify-start items-center gap-4" data-aos-delay="200">
                            <a href="{{ route('register') }}" class="inline-block bg-gradient-to-r from-[#007BFF] to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-bold text-lg px-8 py-4 rounded-full transition-all duration-300 shadow-lg shadow-blue-500/20 transform hover:scale-105">
                                Coba Asesmen Stres Gratis
                            </a>
                        </div>
                        <p class="text-sm text-gray-500 mt-4" data-aos-delay="300">
                            Dipercaya oleh 5.000+ profesional. Tanpa kartu kredit.
                        </p>
                    </div>
                    <div class="w-full md:w-1/2 flex justify-center" data-aos="fade-left" data-aos-delay="100">
                        {{-- BARIS INI YANG DIUBAH --}}
                        <img src="{{ asset('build/assets/icon-relaxboss.png') }}" alt="Ilustrasi seorang profesional bermeditasi" class="w-full max-w-md lg:max-w-lg"/>
                    </div>
                </div>
            </div>
        </section>
    {{-- END HERO SECTION --}}

    {{-- FEATURE SECTION --}}
        <section id="fitur" class="py-24 sm:py-32 bg-slate-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8" data-aos="fade-up">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-4">
                        Ekosistem Lengkap untuk <span class="bg-gradient-to-r from-[#007BFF] to-cyan-500 bg-clip-text text-transparent">Kesejahteraan Mental Anda</span>
                    </h2>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Jelajahi bagaimana setiap pilar RelaxBoss dirancang untuk mendukung perjalanan Anda menuju ketenangan dan produktivitas.
                    </p>
                </div>

                {{-- Menggunakan Alpine.js untuk sistem Tab --}}
                <div x-data="{ activeTab: 'asesmen' }" class="max-w-6xl mx-auto">
                    
                    {{-- Tombol-tombol Tab (5 Fitur Baru) --}}
                    <div class="flex flex-wrap justify-center border-b border-gray-200 mb-12">
                        <button @click="activeTab = 'asesmen'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'asesmen', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'asesmen'}" class="px-5 py-3 font-medium text-base md:text-lg border-b-2 transition-colors duration-300 focus:outline-none">
                            Asesmen Stres
                        </button>
                        <button @click="activeTab = 'relaxmate'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'relaxmate', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'relaxmate'}" class="px-5 py-3 font-medium text-base md:text-lg border-b-2 transition-colors duration-300 focus:outline-none">
                            AI RelaxMate
                        </button>
                        <button @click="activeTab = 'tugas'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'tugas', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tugas'}" class="px-5 py-3 font-medium text-base md:text-lg border-b-2 transition-colors duration-300 focus:outline-none">
                            Tugas & Fokus
                        </button>
                        <button @click="activeTab = 'komunitas'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'komunitas', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'komunitas'}" class="px-5 py-3 font-medium text-base md:text-lg border-b-2 transition-colors duration-300 focus:outline-none">
                            Komunitas Terpandu
                        </button>
                        <button @click="activeTab = 'konsultasi'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'konsultasi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'konsultasi'}" class="px-5 py-3 font-medium text-base md:text-lg border-b-2 transition-colors duration-300 focus:outline-none">
                            Konsultasi Psikolog
                        </button>
                    </div>

                    {{-- Konten untuk setiap Tab --}}
                    <div class="min-h-[250px] text-center max-w-3xl mx-auto">
                        {{-- 1. Konten Asesmen Stres --}}
                        <div x-show="activeTab === 'asesmen'" x-transition>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Pahami Tingkat Stres Anda Secara Akurat</h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Ketahui level stres Anda dengan kuis terukur, dapatkan peringatan dini, serta rekomendasi dan rangkuman AI untuk penanganan pertama.
                            </p>
                        </div>
                        {{-- 2. Konten AI RelaxMate --}}
                        <div x-show="activeTab === 'relaxmate'" x-transition x-cloak>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Pendamping AI Kapan Saja, Di Mana Saja</h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Dapatkan dukungan emosional dan bimbingan terstruktur dari chatbot yang dirancang sesuai standar kerangka psikologi profesional.
                            </p>
                        </div>
                        {{-- 3. Konten Tugas & Fokus --}}
                        <div x-show="activeTab === 'tugas'" x-transition x-cloak>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Kelola Energi, Bukan Sekadar Daftar Tugas</h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Atur prioritas tugas dengan matriks Eisenhower, tingkatkan fokus dengan timer Pomodoro 5:1, dan dapatkan analisis tugas dari AI.
                            </p>
                        </div>
                        {{-- 4. Konten Komunitas Terpandu --}}
                        <div x-show="activeTab === 'komunitas'" x-transition x-cloak>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Bertumbuh Bersama dalam Program Terstruktur</h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Ikuti program terarah seperti "Mengatasi Susah Tidur dalam 7 Hari" yang didampingi langsung oleh pembimbing dan psikolog profesional.
                            </p>
                        </div>
                        {{-- 5. Konten Konsultasi Psikolog --}}
                        <div x-show="activeTab === 'konsultasi'" x-transition x-cloak>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Sesi Profesional Langsung dengan Psikolog Ahli</h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Jadwalkan sesi konsultasi personal yang aman, terstruktur, dan rahasia dengan psikolog profesional pilihan Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    {{-- END FEATURE SECTION --}}
    
    {{-- REVIEW SECTION -- --}}
        <section id="review" class="py-24 sm:py-32 bg-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8" data-aos="fade-up">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold tracking-tight text-gray-900 mb-4">
                        Dipercaya oleh Profesional <span class="bg-gradient-to-r from-[#007BFF] to-cyan-400 bg-clip-text text-transparent">Seperti Anda</span>
                    </h2>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Pengalaman nyata dari para pengguna yang telah menjadikan RelaxBoss sebagai partner kesehatan mental mereka.
                    </p>
                </div>
                
                <div class="max-w-3xl mx-auto">
                    <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-10 text-center">
                        
                        {{-- BAGIAN IKON YANG DIUBAH --}}
                        <div class="text-6xl text-[#007BFF] mb-6">
                            <i class="fa-solid fa-quote-left"></i>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-800">Kisah Pengguna Akan Segera Hadir</h3>
                        <p class="text-gray-600 mt-3 max-w-lg mx-auto">
                            Bagian ini akan diisi dengan testimoni dan cerita nyata setelah peluncuran resmi. Nantikan kisah inspiratif mereka!
                        </p>
                    </div>
                </div>
            </div>
        </section>
    {{-- END REVIEW SECTION --}}

    {{-- FINAL CTA SECTION --}}
        <section class="bg-slate-50 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2210%22%20height%3D%2210%22%20viewBox%3D%220%200%2010%2010%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Cpath%20d%3D%22M.5%204.5v-4h1v4h4v1h-4v4h-1v-4h-4v-1h4z%22%20fill%3D%22%23e2e8f0%22%20fill-rule%3D%22evenodd%22%20clip-rule%3D%22evenodd%22/%3E%3C/svg%3E')]">
            <div class="container mx-auto py-24 sm:py-32 px-4 text-center" data-aos="fade-up">
                <h2 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                    Transformasi Anda Dimulai 
                    <span class="bg-gradient-to-r from-[#007BFF] to-cyan-400 bg-clip-text text-transparent">Sekarang.</span>
                </h2>
                <p class="mt-6 text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                    Bergabunglah dengan 5.000+ profesional yang telah menemukan kembali fokus, ketenangan, dan produktivitas mereka bersama RelaxBoss.
                </p>
                <div class="mt-10 flex flex-col items-center gap-y-4">
                    <a href="{{ route('register') }}" class="inline-block bg-gradient-to-r from-[#007BFF] to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-bold text-lg px-8 py-4 rounded-full transition-all duration-300 shadow-2xl shadow-blue-500/20 transform hover:scale-105">
                        Mulai Perjalanan Anda - Gratis
                    </a>
                    <p class="text-sm text-gray-500">
                        Tanpa komitmen, hanya manfaat.
                    </p>
                </div>
            </div>
        </section>
    {{-- END FINAL CTA SECTION --}}

@endsection

@push('styles')
<style>
    /* Menghilangkan x-cloak setelah inisialisasi Alpine */
    [x-cloak] { display: none !important; }
</style>
@endpush
