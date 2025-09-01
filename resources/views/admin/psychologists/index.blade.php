@extends('layouts.admin')

@section('content')
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Profil Psikolog</h1>
        </div>

        <!-- Notifikasi -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Tabel Psikolog -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Psikolog</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Profil</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($psychologists as $psychologist)
                        @php
                            // [PERBAIKAN] Logika baru untuk mengecek kelengkapan profil.
                            // Status dianggap lengkap hanya jika semua kolom wajib diisi.
                            $profile = $psychologist->psychologistProfile;
                            $isComplete = $profile &&
                                          !empty($profile->title) &&
                                          !empty($profile->str_number) &&
                                          !empty($profile->sipp_number) &&
                                          !empty($profile->domicile) &&
                                          !empty($profile->education) &&
                                          !empty($profile->practice_location) &&
                                          !is_null($profile->years_of_experience);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $psychologist->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $psychologist->email }}</td>
                            <td class="px-6 py-4">
                                @if($isComplete)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Lengkap
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Dilengkapi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.psychologists.edit', $psychologist) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $isComplete ? 'Edit Profil' : 'Lengkapi Profil' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna dengan peran Psikolog.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $psychologists->links() }}
        </div>
    </div>
@endsection