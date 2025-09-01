@extends('layouts.admin')

@section('content')
    <div x-data="{ activeTab: 'profile' }">
        <div class="mb-6">
            <a href="{{ route('admin.psychologists.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali ke Daftar Psikolog</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-2">Edit Profil: {{ $psychologist->name }}</h1>
        </div>

        <!-- Tombol Tab -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-6">
                <button @click="activeTab = 'profile'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'profile' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Profil Profesional</button>
                <button @click="activeTab = 'services'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'services' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Layanan Konsultasi</button>
                <button @click="activeTab = 'schedule'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'schedule' }" class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm">Jadwal</button>
            </nav>
        </div>

        <!-- Konten Tab Profil -->
        <div x-show="activeTab === 'profile'" x-cloak>
            <form action="{{ route('admin.psychologists.update', $psychologist) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kolom Kiri -->
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Gelar</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $profile->title) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="str_number" class="block text-sm font-medium text-gray-700">Nomor STR</label>
                                <input type="text" name="str_number" id="str_number" value="{{ old('str_number', $profile->str_number) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('str_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="sipp_number" class="block text-sm font-medium text-gray-700">Nomor SIPP</label>
                                <input type="text" name="sipp_number" id="sipp_number" value="{{ old('sipp_number', $profile->sipp_number) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('sipp_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="domicile" class="block text-sm font-medium text-gray-700">Domisili</label>
                                <input type="text" name="domicile" id="domicile" value="{{ old('domicile', $profile->domicile) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('domicile')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="education" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir/Alumni</label>
                                <input type="text" name="education" id="education" value="{{ old('education', $profile->education) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('education')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="practice_location" class="block text-sm font-medium text-gray-700">Tempat Praktik</label>
                                <input type="text" name="practice_location" id="practice_location" value="{{ old('practice_location', $profile->practice_location) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('practice_location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="years_of_experience" class="block text-sm font-medium text-gray-700">Pengalaman (Tahun)</label>
                                <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience', $profile->years_of_experience) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('years_of_experience')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $profile->is_available) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <label for="is_available" class="ml-2 block text-sm text-gray-900">Tersedia untuk Konsultasi</label>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-4">
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio/Deskripsi Pribadi</label>
                                <textarea name="bio" id="bio" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('bio', $profile->bio) }}</textarea>
                                @error('bio')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="intro_template_message" class="block text-sm font-medium text-gray-700">Template Pesan Perkenalan</label>
                                <textarea name="intro_template_message" id="intro_template_message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('intro_template_message', $profile->intro_template_message) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bidang Keahlian</label>
                                <div class="mt-2 space-y-2 border p-4 rounded-md h-48 overflow-y-auto">
                                    @foreach($specializations as $specialization)
                                        <div class="flex items-center">
                                            <input id="spec_{{ $specialization->id }}" name="specializations[]" type="checkbox" value="{{ $specialization->id }}"
                                                   {{ in_array($specialization->id, old('specializations', $psychologist->specializations->pluck('id')->toArray())) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                            <label for="spec_{{ $specialization->id }}" class="ml-3 block text-sm text-gray-900">{{ $specialization->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('specializations')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t text-right -m-6 mt-6">
                        <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Konten Tab Layanan -->
        <div x-show="activeTab === 'services'" x-cloak>
            <form action="{{ route('admin.psychologists.services.store', $psychologist) }}" method="POST">
                @csrf
                <div class="bg-white shadow-md rounded-lg">
                    <div class="p-6 space-y-8">
                        <!-- Form Layanan Online -->
                        @include('admin.psychologists.partials.service-form', ['service' => $onlineService, 'type' => 'online'])
                        <!-- Form Layanan Offline -->
                        @include('admin.psychologists.partials.service-form', ['service' => $offlineService, 'type' => 'offline'])
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t text-right">
                        <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700">Simpan Perubahan Layanan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Konten Tab Jadwal -->
        <div x-show="activeTab === 'schedule'" x-cloak>
            @include('admin.psychologists.partials.schedule-tab')
        </div>
    </div>
@endsection
