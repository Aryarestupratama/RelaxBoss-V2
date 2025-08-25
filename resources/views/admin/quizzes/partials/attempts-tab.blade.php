<div class="bg-white rounded-lg shadow-md">
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Hasil Pengerjaan</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pengguna</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pengerjaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ringkasan AI</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($quiz->attempts as $attempt)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $attempt->user->name ?? 'Pengguna Dihapus' }}</td>
                        <td class="px-6 py-4">{{ $attempt->created_at->format('d F Y, H:i') }}</td>
                        <td class="px-6 py-4 text-gray-500 italic">{{ Str::limit($attempt->ai_summary, 70) ?? 'Belum ada' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.attempts.show', $attempt) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada pengguna yang mengerjakan kuis ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>