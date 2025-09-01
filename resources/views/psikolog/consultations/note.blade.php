@extends('layouts.psychologist')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('psikolog.consultations.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    &larr; Kembali ke Jadwal Konsultasi
                </a>
            </div>

            <form action="{{ route('psikolog.consultations.note.store', $session) }}" method="POST">
                @csrf
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-6 border-b">
                        <h1 class="text-2xl font-bold text-gray-800">Catatan Sesi Konsultasi</h1>
                        <p class="text-gray-600 mt-1">Sesi dengan: <span class="font-semibold">{{ $session->user->name }}</span></p>
                        <p class="text-sm text-gray-500">
                            Jadwal: {{ \Carbon\Carbon::parse($session->session_start_time)->isoFormat('dddd, D MMMM YYYY') }}
                            <span class="text-gray-400 mx-1">|</span>
                            Status Sesi: <span class="font-medium text-gray-700 capitalize">{{ $session->status }}</span>
                        </p>
                    </div>

                    <div class="p-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Tuliskan catatan klinis atau ringkasan sesi di sini:
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="15" 
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Contoh: Pasien mengungkapkan kekhawatiran tentang tekanan kerja..."
                        >{{ old('notes', $session->note->notes ?? '') }}</textarea>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Simpan Catatan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
