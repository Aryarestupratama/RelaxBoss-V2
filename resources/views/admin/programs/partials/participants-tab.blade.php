<div class="bg-white rounded-lg shadow-md">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Peserta Terdaftar</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bergabung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progres</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($program->enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $enrollment->user->name ?? 'Pengguna Dihapus' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $enrollment->user->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $enrollment->created_at->format('d F Y') }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            <span class="font-semibold">Hari ke-{{ $enrollment->current_day }}</span> / {{ $program->duration_days }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada peserta yang terdaftar di program ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
