@extends('layouts.psychologist')

@section('content')
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Program Bimbingan Saya</h1>
        </div>

        <!-- Tabel Program -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($programs as $program)
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $program->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $program->enrolled_users_count }} Peserta</td>
                            <td class="px-6 py-4 text-gray-500">{{ $program->duration_days }} Hari</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('psikolog.programs.materials.index', $program) }}" class="text-indigo-600 hover:text-indigo-900">Kelola Materi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Anda belum ditugaskan ke program mana pun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $programs->links() }}
        </div>
    </div>
@endsection
