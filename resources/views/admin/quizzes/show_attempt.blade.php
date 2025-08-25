@extends('layouts.admin')

@section('content')
    <div>
        <!-- Header Halaman -->
        <div class="mb-6">
            <a href="{{ route('admin.quizzes.show', $attempt->quiz_id) }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Kelola Kuis</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-2">Detail Hasil Asesmen</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Detail & Rekomendasi -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Rekomendasi AI -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Rekomendasi Personal (AI)</h2>
                    <div class="mt-4 prose prose-sm max-w-none text-gray-600 leading-relaxed">
                        <p>{{ $attempt->ai_recommendation ?? 'Rekomendasi belum tersedia.' }}</p>
                    </div>
                </div>

                <!-- Rincian Skor -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Rincian Skor</h2>
                    <div class="space-y-4">
                        @foreach ($attempt->results as $subScale => $result)
                            <div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">{{ ucfirst($subScale) }}</span>
                                    <span class="font-bold">{{ $result['interpretation'] }}</span>
                                </div>
                                <div class="flex justify-between items-baseline mt-1">
                                    <span class="text-sm text-gray-500">Skor</span>
                                    <span class="text-lg font-mono font-bold">{{ $result['score'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Info & Ringkasan -->
            <div class="space-y-8">
                <!-- Info Pengerjaan -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi</h2>
                    <ul class="text-sm space-y-2 text-gray-600">
                        <li><strong>Pengguna:</strong> {{ $attempt->user->name ?? 'N/A' }}</li>
                        <li><strong>Email:</strong> {{ $attempt->user->email ?? 'N/A' }}</li>
                        <li><strong>Kuis:</strong> {{ $attempt->quiz->name }}</li>
                        <li><strong>Tanggal:</strong> {{ $attempt->created_at->format('d F Y, H:i') }}</li>
                    </ul>
                </div>

                <!-- Ringkasan AI -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Ringkasan Medis (AI)</h2>
                    <p class="mt-2 text-sm text-gray-600 italic">"{{ $attempt->ai_summary ?? 'Ringkasan belum tersedia.' }}"</p>
                </div>

                <!-- Konteks Pengguna -->
                @if($attempt->user_context)
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Konteks dari Pengguna</h2>
                    <p class="mt-2 text-sm text-gray-600 italic">"{{ $attempt->user_context }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection