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
                    {{-- Ganti dengan path ke ilustrasi Anda --}}
                    <img src="https://placehold.co/600x500/E0F2FE/334155?text=Ilustrasi+Anda" alt="Ilustrasi seorang profesional bermeditasi" class="w-full max-w-md lg:max-w-lg"/>
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
                    Ekosistem untuk <span class="bg-gradient-to-r from-[#007BFF] to-cyan-500 bg-clip-text text-transparent">Ketenangan Anda</span>
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Pilih fitur untuk melihat bagaimana RelaxBoss dapat membantu Anda secara spesifik.
                </p>
            </div>

            <div x-data="{ activeTab: 'relaxmate' }" class="max-w-5xl mx-auto">
                {{-- Tombol-tombol Tab --}}
                <div class="flex flex-wrap justify-center border-b border-gray-200 mb-12">
                    <button @click="activeTab = 'relaxmate'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'relaxmate', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'relaxmate'}" class="px-6 py-3 font-medium text-lg border-b-2 transition-colors duration-300 focus:outline-none">AI RelaxMate</button>
                    <button @click="activeTab = 'tugas'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'tugas', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tugas'}" class="px-6 py-3 font-medium text-lg border-b-2 transition-colors duration-300 focus:outline-none">Manajemen Tugas</button>
                    <button @click="activeTab = 'akademi'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'akademi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'akademi'}" class="px-6 py-3 font-medium text-lg border-b-2 transition-colors duration-300 focus:outline-none">Akademi</button>
                    <button @click="activeTab = 'telehealth'" :class="{'border-[#007BFF] text-[#007BFF]': activeTab === 'telehealth', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'telehealth'}" class="px-6 py-3 font-medium text-lg border-b-2 transition-colors duration-300 focus:outline-none">Telehealth</button>
                </div>

                {{-- Konten untuk setiap Tab --}}
                <div class="min-h-[250px] text-center max-w-3xl mx-auto">
                    <div x-show="activeTab === 'relaxmate'" x-transition>
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Partner Cerdas untuk Keseharian Anda</h3>
                        <p class="text-lg text-gray-600 leading-relaxed">Dapatkan dukungan emosional & bimbingan kapan saja Anda butuh, tanpa harus menunggu jadwal.</p>
                    </div>
                    <div x-show="activeTab === 'tugas'" x-transition style="display: none;">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Kelola Energi, Bukan Hanya Tugas</h3>
                        <p class="text-lg text-gray-600 leading-relaxed">Kelola energi dan fokus dengan tenang, bukan sekadar mencentang daftar tugas yang tak ada habisnya.</p>
                    </div>
                    <div x-show="activeTab === 'akademi'" x-transition style="display: none;">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Belajar & Bertumbuh Sesuai Kecepatan Anda</h3>
                        <p class="text-lg text-gray-600 leading-relaxed">Ubah dari 'membaca pasif' menjadi 'pengalaman belajar' yang bisa langsung diterapkan.</p>
                    </div>
                    <div x-show="activeTab === 'telehealth'" x-transition style="display: none;">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Konsultasi Profesional yang Aman & Terstruktur</h3>
                        <p class="text-lg text-gray-600 leading-relaxed">Bangun kepercayaan, keamanan, dan struktur seperti kunjungan klinik profesional dari rumah Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- END FEATURE SECTION --}}
    
    {{-- REVIEW SECTION --}}
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
                    <div class="text-6xl text-[#007BFF] mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.892 8.892 0 01-4.13-1.054c-.235-.113-.539.138-.433.376.24.55.613 1.04.98 1.454.14.158.04.41-.15.48-1.9.7-4.06-1.12-4.06-3.256 0-3.866 3.582-7 8-7s8 3.134 8 7zM2 10c0 3.866 3.582 7 8 7a8.892 8.892 0 004.13-1.054c.235-.113.539.138.433.376-.24.55-.613 1.04-.98 1.454-.14.158-.04.41.15.48 1.9.7 4.06-1.12 4.06-3.256 0-3.866-3.582-7-8-7S2 6.134 2 10z" clip-rule="evenodd" /></svg>
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
    <section class="bg-slate-50">
        <div class="container mx-auto py-24 sm:py-32 px-4 text-center" data-aos="fade-up">
            <h2 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                Transformasi Anda Dimulai 
                <span class="bg-gradient-to-r from-[#007BFF] to-cyan-400 bg-clip-text text-transparent">Sekarang.</span>
            </h2>
            <p class="mt-6 text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Bergabunglah dengan ribuan profesional yang telah menemukan kembali fokus, ketenangan, dan produktivitas mereka bersama RelaxBoss.
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
