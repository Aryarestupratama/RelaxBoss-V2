@extends('layouts.psychologist')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pengaturan</h1>
            <p class="text-gray-500 mb-8">Kelola informasi profil, layanan, dan jadwal praktik Anda di sini.</p>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
                    <p class="font-bold">Berhasil</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('psikolog.profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="space-y-8">
                    <!-- Bagian 1: Profil Profesional -->
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <div class="flex justify-between items-center pb-4 border-b">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Profil Profesional</h2>
                                <p class="text-sm text-gray-500">Informasi ini akan ditampilkan di halaman profil publik Anda.</p>
                            </div>
                            <label for="is_available" class="flex items-center cursor-pointer">
                                <span class="mr-3 text-sm font-medium text-gray-900">Siap Menerima Pasien</span>
                                <div class="relative">
                                    <input type="checkbox" id="is_available" name="profile[is_available]" class="sr-only" value="1" {{ old('profile.is_available', $profile->is_available) ? 'checked' : '' }}>
                                    <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                                </div>
                            </label>
                        </div>
                        
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Input lainnya -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Gelar Profesional</label>
                                <input type="text" name="profile[title]" id="title" value="{{ old('profile.title', $profile->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="years_of_experience" class="block text-sm font-medium text-gray-700">Pengalaman (Tahun)</label>
                                <input type="number" name="profile[years_of_experience]" id="years_of_experience" value="{{ old('profile.years_of_experience', $profile->years_of_experience) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label for="education" class="block text-sm font-medium text-gray-700">Pendidikan Terakhir</label>
                                <input type="text" name="profile[education]" id="education" value="{{ old('profile.education', $profile->education) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="practice_location" class="block text-sm font-medium text-gray-700">Lokasi Praktik Utama</label>
                                <input type="text" name="profile[practice_location]" id="practice_location" value="{{ old('profile.practice_location', $profile->practice_location) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label for="str_number" class="block text-sm font-medium text-gray-700">Nomor STR</label>
                                <input type="text" name="profile[str_number]" id="str_number" value="{{ old('profile.str_number', $profile->str_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="sipp_number" class="block text-sm font-medium text-gray-700">Nomor SIPP</label>
                                <input type="text" name="profile[sipp_number]" id="sipp_number" value="{{ old('profile.sipp_number', $profile->sipp_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio Singkat</label>
                                <textarea name="profile[bio]" id="bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('profile.bio', $profile->bio) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 2: Layanan & Harga -->
                    <div class="bg-white p-8 rounded-lg shadow-md">
                         <h2 class="text-xl font-bold text-gray-900 pb-4 border-b">Layanan & Harga</h2>
                         <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Layanan Online -->
                            <div class="border p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-semibold text-lg">Sesi Online</h3>
                                    <input type="checkbox" name="services[online][is_active]" value="1" class="rounded" {{ old('services.online.is_active', $onlineService->is_active ?? false) ? 'checked' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga per Sesi (Rp)</label>
                                    <input type="number" name="services[online][price_per_session]" value="{{ old('services.online.price_per_session', $onlineService->price_per_session ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Durasi per Sesi (Menit)</label>
                                    <input type="number" name="services[online][duration_per_session_minutes]" value="{{ old('services.online.duration_per_session_minutes', $onlineService->duration_per_session_minutes ?? '50') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                             <!-- Layanan Offline -->
                            <div class="border p-4 rounded-lg">
                               <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-semibold text-lg">Sesi Offline</h3>
                                    <input type="checkbox" name="services[offline][is_active]" value="1" class="rounded" {{ old('services.offline.is_active', $offlineService->is_active ?? false) ? 'checked' : '' }}>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga per Sesi (Rp)</label>
                                    <input type="number" name="services[offline][price_per_session]" value="{{ old('services.offline.price_per_session', $offlineService->price_per_session ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Durasi per Sesi (Menit)</label>
                                    <input type="number" name="services[offline][duration_per_session_minutes]" value="{{ old('services.offline.duration_per_session_minutes', $offlineService->duration_per_session_minutes ?? '50') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Bagian 3: Jadwal Praktik Mingguan -->
                     <div class="bg-white p-8 rounded-lg shadow-md">
                        <h2 class="text-xl font-bold text-gray-900 pb-4 border-b">Jadwal Praktik Mingguan</h2>
                        <div class="mt-6 space-y-6">
                            @foreach ($days as $day)
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center border-t pt-4">
                                    <div class="md:col-span-2 font-semibold">{{ $day }}</div>
                                    
                                    <!-- Jadwal Online -->
                                    <div class="md:col-span-5">
                                        <label class="flex items-center gap-2 mb-2">
                                            <input type="checkbox" name="schedules[{{ $day }}][online][is_active]" class="rounded" {{ old("schedules.$day.online.is_active", $schedules[$day]['online'] ?? false) ? 'checked' : '' }}>
                                            <span>Online</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="time" name="schedules[{{ $day }}][online][start_time]" value="{{ old("schedules.$day.online.start_time", $schedules[$day]['online']->start_time ?? '') }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                            <input type="time" name="schedules[{{ $day }}][online][end_time]" value="{{ old("schedules.$day.online.end_time", $schedules[$day]['online']->end_time ?? '') }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                    </div>

                                    <!-- Jadwal Offline -->
                                    <div class="md:col-span-5">
                                        <label class="flex items-center gap-2 mb-2">
                                            <input type="checkbox" name="schedules[{{ $day }}][offline][is_active]" class="rounded" {{ old("schedules.$day.offline.is_active", $schedules[$day]['offline'] ?? false) ? 'checked' : '' }}>
                                            <span>Offline</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <input type="time" name="schedules[{{ $day }}][offline][start_time]" value="{{ old("schedules.$day.offline.start_time", $schedules[$day]['offline']->start_time ?? '') }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                            <input type="time" name="schedules[{{ $day }}][offline][end_time]" value="{{ old("schedules.$day.offline.end_time", $schedules[$day]['offline']->end_time ?? '') }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                     </div>

                    <!-- Tombol Simpan -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <style>
        /* Simple toggle switch style */
        input:checked ~ .dot {
            transform: translateX(100%);
            background-color: #fff;
        }
        input:checked ~ .block {
            background-color: #2563eb; /* blue-600 */
        }
    </style>
@endsection
