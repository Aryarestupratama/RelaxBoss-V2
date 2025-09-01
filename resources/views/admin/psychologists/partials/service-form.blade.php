<div class="border p-4 rounded-lg">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">Layanan {{ ucfirst($type) }}</h3>
        <div class="flex items-center">
            <input type="checkbox" name="services[{{ $type }}][is_active]" id="is_active_{{ $type }}" value="1" {{ old("services.{$type}.is_active", $service->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
            <label for="is_active_{{ $type }}" class="ml-2 block text-sm text-gray-900">Aktifkan Layanan Ini</label>
        </div>
    </div>
    <hr class="my-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="services[{{ $type }}][type]" value="{{ $type }}">
        <div>
            <label for="price_{{ $type }}" class="block text-sm font-medium text-gray-700">Harga per Sesi (Rp)</label>
            <input type="number" name="services[{{ $type }}][price_per_session]" id="price_{{ $type }}" value="{{ old("services.{$type}.price_per_session", $service->price_per_session) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        <div>
            <label for="duration_{{ $type }}" class="block text-sm font-medium text-gray-700">Durasi per Sesi (Menit)</label>
            <input type="number" name="services[{{ $type }}][duration_per_session_minutes]" id="duration_{{ $type }}" value="{{ old("services.{$type}.duration_per_session_minutes", $service->duration_per_session_minutes) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
    </div>
    <div class="mt-4 flex items-center">
        <input type="checkbox" name="services[{{ $type }}][is_free]" id="is_free_{{ $type }}" value="1" {{ old("services.{$type}.is_free", $service->is_free) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
        <label for="is_free_{{ $type }}" class="ml-2 block text-sm text-gray-900">Gratis (Harga akan diabaikan)</label>
    </div>
</div>
