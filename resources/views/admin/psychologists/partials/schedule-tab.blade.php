<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Kolom Kiri: Form Tambah Jadwal -->
    <div class="md:col-span-1">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Jadwal Baru</h3>
            <form action="{{ route('admin.psychologists.schedules.store', $psychologist) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="day_of_week" class="block text-sm font-medium text-gray-700">Hari</label>
                    {{-- [PERBAIKAN] Mengubah opsi dropdown ke Bahasa Indonesia --}}
                    <select name="day_of_week" id="day_of_week" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Jam Mulai</label>
                        <input type="time" name="start_time" id="start_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">Jam Selesai</label>
                        <input type="time" name="end_time" id="end_time" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe Sesi</label>
                    <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700">Tambah Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Daftar Jadwal yang Ada -->
    <div class="md:col-span-2">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td class="px-6 py-4 font-medium">{{ $schedule->day_of_week }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->type === 'online' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($schedule->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada jadwal yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
