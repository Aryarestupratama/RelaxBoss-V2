<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Daftar Aturan Skor</h2>
        <button @click="showCreateRuleModal = true" class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-md hover:bg-blue-700">Tambah Aturan</button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub-Skala</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rentang Skor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interpretasi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($quiz->scoringRules->sortBy('sub_scale')->sortBy('min_score') as $rule)
                    <tr>
                        <td class="px-6 py-4">{{ $rule->sub_scale }}</td>
                        <td class="px-6 py-4 font-mono">{{ $rule->min_score }} - {{ $rule->max_score }}</td>
                        <td class="px-6 py-4">{{ $rule->interpretation }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openEditRuleModal({{ $rule }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                            <button @click="openDeleteRuleModal({{ $rule }})" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada aturan skor untuk kuis ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
