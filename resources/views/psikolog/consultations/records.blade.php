@extends('layouts.psychologist')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('psikolog.consultations.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    &larr; Kembali ke Jadwal Konsultasi
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6 border-b">
                    <h1 class="text-2xl font-bold text-gray-800">Rekam Medis Pasien</h1>
                    <p class="text-gray-600 mt-1">Sesi dengan: <span class="font-semibold">{{ $session->user->name }}</span></p>
                    <p class="text-sm text-gray-500">Jadwal: {{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}</p>
                </div>

                <div class="p-6">
                    @if($quizAttempts->isEmpty())
                        <div class="text-center text-gray-500 py-8">
                            <p>Pasien belum membagikan rekam medis untuk sesi ini.</p>
                        </div>
                    @else
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Hasil Asesmen yang Dibagikan</h2>
                        <div class="space-y-6">
                            @foreach ($quizAttempts as $attempt)
                                <div class="border rounded-lg p-4">
                                    <p class="text-sm text-gray-500 mb-2">Asesmen diambil pada: {{ $attempt->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                                    
                                    @if(isset($attempt->results['summary']) && !empty($attempt->results['summary']))
                                        <div class="prose prose-sm max-w-none mb-4">
                                            <h4 class="font-semibold">Ringkasan Masalah (dari AI)</h4>
                                            <p>{{ $attempt->results['summary'] }}</p>
                                        </div>
                                    @else
                                        <p class="italic text-gray-500 mb-4">Tidak ada ringkasan yang tersedia.</p>
                                    @endif

                                    {{-- [BARU] Menampilkan detail skor asesmen --}}
                                    @if(is_array($attempt->results) && count(array_filter(array_keys($attempt->results), 'is_numeric')) > 0)
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-3">Detail Skor Asesmen</h4>
                                            <div class="space-y-2">
                                                @foreach ($attempt->results as $key => $result)
                                                    @if(is_numeric($key) && is_array($result))
                                                        @php
                                                            $level = strtolower($result['interpretation'] ?? '');
                                                            $bgColor = match($level) {
                                                                'normal', 'ringan' => 'bg-green-100',
                                                                'sedang' => 'bg-yellow-100',
                                                                'parah', 'sangat parah' => 'bg-red-100',
                                                                default => 'bg-gray-100',
                                                            };
                                                            $textColor = match($level) {
                                                                'normal', 'ringan' => 'text-green-800',
                                                                'sedang' => 'text-yellow-800',
                                                                'parah', 'sangat parah' => 'text-red-800',
                                                                default => 'text-gray-800',
                                                            };
                                                        @endphp
                                                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-md">
                                                            <div class="font-medium text-gray-700">{{ $result['category'] ?? 'Tidak diketahui' }}</div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-sm font-semibold text-gray-500">Skor: {{ $result['score'] ?? 'N/A' }}</span>
                                                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $bgColor }} {{ $textColor }}">
                                                                    {{ $result['interpretation'] ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

