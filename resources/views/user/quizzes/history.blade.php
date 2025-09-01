<x-app-layout>
    {{-- Mengatur judul tab browser --}}
    <x-slot name="title">
        {{ __('Riwayat Asesmen') }}
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Asesmen Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- [IMPROVISASI] Header Halaman yang lebih visual -->
            <div class="mb-10 flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 flex items-center justify-center rounded-full flex-shrink-0">
                    <i class="fa-solid fa-timeline text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Riwayat Asesmen Anda</h1>
                    <p class="mt-1 text-gray-600">Lacak perkembangan kesejahteraan Anda dari waktu ke waktu.</p>
                </div>
            </div>

            <!-- Daftar Riwayat -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200/50">
                <ul class="divide-y divide-gray-200">
                    @forelse ($attempts as $attempt)
                        @php
                            // [IMPROVISASI] Logika untuk menampilkan status stres secara langsung
                            $dominantStress = collect($attempt->results)->sortByDesc('score')->first();
                            $stressLevel = $dominantStress['interpretation'] ?? null;
                            $stressColor = match(strtolower($stressLevel)) {
                                'normal', 'ringan' => 'green',
                                'sedang' => 'yellow',
                                'parah', 'sangat parah', 'tinggi' => 'red',
                                default => 'gray',
                            };
                        @endphp
                        <li class="p-4 sm:p-6 hover:bg-slate-50 transition-colors duration-200">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="text-blue-500 mt-1">
                                        <i class="fa-solid fa-file-waveform fa-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-blue-600">{{ $attempt->quiz->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            Diambil pada: {{ $attempt->created_at->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex-shrink-0 flex items-center gap-4">
                                    {{-- [IMPROVISASI] Badge status stres untuk pemindaian cepat --}}
                                    @if($stressLevel)
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{$stressColor}}-100 text-{{$stressColor}}-800 hidden md:inline-block">
                                        {{ $stressLevel }}
                                    </span>
                                    @endif
                                    {{-- [IMPROVISASI] Tombol yang lebih menarik secara visual --}}
                                    <a href="{{ route('quizzes.result', $attempt) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold text-xs shadow-md hover:bg-blue-700 transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                            @if($attempt->ai_summary)
                            <div class="mt-4 pt-4 border-t border-gray-200 ml-9">
                                <p class="text-sm text-gray-500 italic"><strong class="text-gray-600">Ringkasan AI:</strong> "{{ Str::limit($attempt->ai_summary, 150) }}"</p>
                            </div>
                            @endif
                        </li>
                    @empty
                        {{-- [IMPROVISASI] Tampilan kosong yang lebih menarik dengan CTA --}}
                        <li class="p-12 text-center text-gray-500">
                            <div class="w-20 h-20 mx-auto bg-slate-100 text-slate-400 flex items-center justify-center rounded-full mb-6">
                                <i class="fa-solid fa-folder-open text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Riwayat Anda Masih Kosong</h3>
                            <p class="mt-1 mb-6">Anda belum pernah mengambil asesmen apa pun.</p>
                            <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold shadow-md hover:bg-blue-700 transition">
                                Ambil Asesmen Pertama Anda
                            </a>
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Paginasi -->
            <div class="mt-8">
                {{ $attempts->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
