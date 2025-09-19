<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        Cari Psikolog
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div x-data="{
                search: '',
                selectedSpecialization: 'all',
                psychologists: {{ json_encode($psychologists) }},
                get filteredPsychologists() {
                    return this.psychologists.filter(p => {
                        const nameMatch = p.full_name.toLowerCase().includes(this.search.toLowerCase());
                        const specMatch = this.selectedSpecialization === 'all' || p.specializations.some(s => s.id == this.selectedSpecialization);
                        return nameMatch && specMatch;
                    });
                }
            }">

                <!-- Header Halaman & Filter -->
                <div class="text-center mb-12" data-aos="fade-down">
                    {{-- Gambar RelaxMate Consultation --}}
                    <div class="mb-6 flex justify-center">
                        <img src="{{ asset('build/assets/relaxmate-consultation.png') }}" 
                            alt="RelaxMate Consultation" 
                            class="max-w-full h-auto w-[90%] sm:w-[70%] md:w-[60%] lg:w-[45%] xl:w-[40%] object-contain drop-shadow-md">
                    </div>

                    <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight sm:text-5xl">
                        Temukan Psikolog Anda
                    </h1>
                    <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                        Jelajahi daftar profesional berlisensi kami yang siap membantu Anda dalam perjalanan kesehatan mental Anda.
                    </p>

                    <div class="mt-8 max-w-2xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Input Pencarian -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-gray-400"></i>
                            </div>
                            <input x-model.debounce.300ms="search" type="text" 
                                placeholder="Cari nama psikolog..." 
                                class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-full bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <!-- Filter Spesialisasi -->
                        <select x-model="selectedSpecialization" 
                                class="block w-full py-3 px-4 border border-gray-300 rounded-full bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition">
                            <option value="all">Semua Bidang Keahlian</option>
                            @foreach ($specializations as $specialization)
                                <option value="{{ $specialization->id }}">{{ $specialization->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!-- Grid Kartu Psikolog -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="psychologist in filteredPsychologists" :key="psychologist.id">
                        {{-- [IMPROVISASI] Kartu Psikolog yang didesain ulang total --}}
                        <div class="bg-white shadow-xl rounded-2xl overflow-hidden h-full flex flex-col border border-slate-200/50 transition-transform duration-300 hover:-translate-y-2" data-aos="fade-up">
                            <div class="p-6 flex flex-col flex-grow">
                                <!-- Bagian Header Kartu -->
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <img class="h-20 w-20 rounded-full object-cover ring-4 ring-blue-100" :src="psychologist.profile_picture ? `/storage/${psychologist.profile_picture}` : `https://placehold.co/80x80/007BFF/FFFFFF?text=${psychologist.name.charAt(0)}`" :alt="psychologist.name">
                                        <div>
                                            <h2 class="text-lg font-bold text-gray-900" x-text="psychologist.full_name"></h2>
                                            <p class="text-sm text-blue-600 font-semibold" x-text="psychologist.psychologist_profile.title"></p>
                                        </div>
                                    </div>
                                    <!-- Status Ketersediaan -->
                                    <div x-show="psychologist.psychologist_profile.is_available" class="flex-shrink-0 flex items-center gap-2 text-xs font-semibold text-green-600">
                                        <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                                        Tersedia
                                    </div>
                                    <div x-show="!psychologist.psychologist_profile.is_available" class="flex-shrink-0 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                        <span class="h-2 w-2 bg-gray-400 rounded-full"></span>
                                        Offline
                                    </div>
                                </div>
                                
                                <!-- Info Detail dengan Ikon -->
                                <div class="text-sm text-gray-600 space-y-2 border-y py-4 my-4">
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-briefcase w-4 text-center text-blue-500"></i><span x-text="`${psychologist.psychologist_profile.years_of_experience} tahun pengalaman`"></span></div>
                                    <div class="flex items-center gap-2"><i class="fa-solid fa-map-marker-alt w-4 text-center text-blue-500"></i><span x-text="`Domisili: ${psychologist.psychologist_profile.domicile}`"></span></div>
                                </div>

                                <!-- Bidang Keahlian -->
                                <div>
                                    <h3 class="text-xs font-semibold text-gray-400 uppercase mb-2">Bidang Keahlian</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="spec in psychologist.specializations.slice(0, 3)" :key="spec.id">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" x-text="spec.name"></span>
                                        </template>
                                        <template x-if="psychologist.specializations.length > 3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700" x-text="`+${psychologist.specializations.length - 3} lainnya`"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bagian Bawah Kartu (Harga & Tombol Aksi) -->
                            <div class="px-6 pb-6 bg-slate-50 border-t mt-auto flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">Mulai dari</p>
                                    <p class="font-bold text-gray-800" x-text="`Rp ${new Intl.NumberFormat('id-ID').format(Math.min(...psychologist.services.map(s => s.price_per_session)))}`"></p>
                                </div>
                                <a :href="`/psychologists/${psychologist.id}`" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition text-sm">
                                    Lihat Profil
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Pesan Jika Tidak Ditemukan -->
                <div x-show="filteredPsychologists.length === 0" class="text-center py-16 px-6 bg-white rounded-2xl shadow-xl border border-slate-200/50" x-cloak>
                    <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                        <i class="fa-solid fa-user-slash text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Tidak Ada Psikolog Ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter bidang keahlian Anda.</p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
