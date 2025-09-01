<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Jadwalkan Sesi dengan {{ $psychologist->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- TAMBAHKAN BLOK INI UNTUK MENAMPILKAN ERROR VALIDASI -->
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan validasi.</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- AKHIR BLOK ERROR -->

            <form action="{{ route('booking.store', $psychologist->id) }}" method="POST">
                @csrf
                <div x-data="bookingCalendar()">

                    <input type="hidden" name="service_id" :value="selectedService ? selectedService.id : ''">
                    <input type="hidden" name="session_start_time" :value="getSelectedDateTime()">

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Kolom Kiri: Kalender & Pilihan Layanan -->
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow-lg rounded-2xl p-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4">1. Pilih Layanan & Jadwal</h2>

                                <!-- Pilihan Tipe Layanan -->
                                <div class="mb-6">
                                    <h3 class="font-semibold text-gray-700 mb-3">Pilih Tipe Layanan</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <button type="button" @click="selectService(onlineService)"
                                                :disabled="!onlineService"
                                                :class="{
                                                    'bg-blue-600 text-white ring-2 ring-blue-300': selectedService && selectedService.type === 'online',
                                                    'bg-white text-gray-700 border hover:bg-gray-50': !selectedService || selectedService.type !== 'online',
                                                    'opacity-50 cursor-not-allowed': !onlineService
                                                }"
                                                class="text-left p-3 rounded-lg font-semibold transition">
                                            <p>Online</p>
                                            <p class="text-xs font-normal" :class="selectedService && selectedService.type === 'online' ? 'text-blue-200' : 'text-gray-500'" x-text="onlineService ? (onlineService.is_free ? 'Gratis' : `Rp ${Number(onlineService.price_per_session).toLocaleString('id-ID')}`) : 'Tidak Tersedia'"></p>
                                        </button>
                                        <button type="button" @click="selectService(offlineService)"
                                                :disabled="!offlineService"
                                                :class="{
                                                    'bg-blue-600 text-white ring-2 ring-blue-300': selectedService && selectedService.type === 'offline',
                                                    'bg-white text-gray-700 border hover:bg-gray-50': !selectedService || selectedService.type !== 'offline',
                                                    'opacity-50 cursor-not-allowed': !offlineService
                                                }"
                                                class="text-left p-3 rounded-lg font-semibold transition">
                                            <p>Offline</p>
                                            <p class="text-xs font-normal" :class="selectedService && selectedService.type === 'offline' ? 'text-blue-200' : 'text-gray-500'" x-text="offlineService ? (offlineService.is_free ? 'Gratis' : `Rp ${Number(offlineService.price_per_session).toLocaleString('id-ID')}`) : 'Tidak Tersedia'"></p>
                                        </button>
                                    </div>
                                </div>

                                <!-- Pilihan Tanggal (7 Hari ke Depan) -->
                                <div class="flex space-x-2 overflow-x-auto pb-4">
                                    <template x-for="day in nextSevenDays" :key="day.dateString">
                                        <button type="button" @click="selectDate(day)"
                                                :class="{
                                                    'bg-blue-600 text-white': selectedDate && selectedDate.dateString === day.dateString,
                                                    'bg-gray-100 text-gray-700 hover:bg-blue-100': !selectedDate || selectedDate.dateString !== day.dateString,
                                                    'opacity-50 cursor-not-allowed': !day.isAvailable
                                                }"
                                                :disabled="!day.isAvailable"
                                                class="flex-shrink-0 text-center px-4 py-2 rounded-lg transition">
                                            <p class="text-xs" x-text="day.dayName"></p>
                                            <p class="font-bold text-lg" x-text="day.date"></p>
                                        </button>
                                    </template>
                                </div>

                                <hr class="my-6">

                                <!-- Pilihan Waktu -->
                                <div>
                                    <h3 class="font-semibold text-gray-700 mb-3">Pilih Waktu Tersedia (<span x-text="selectedDate ? selectedDate.fullDate : 'Pilih tanggal dulu'"></span>)</h3>
                                    <div x-show="!selectedDate" class="text-center text-gray-400 py-8">
                                        <p>Silakan pilih tanggal di atas untuk melihat waktu yang tersedia.</p>
                                    </div>
                                    <div x-show="selectedDate && availableSlots.length === 0" class="text-center text-gray-400 py-8">
                                        <p>Tidak ada jadwal tersedia untuk tanggal & tipe layanan ini.</p>
                                    </div>
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                        <template x-for="slot in availableSlots" :key="slot">
                                            <button type="button" @click="selectedSlot = slot"
                                                    :class="{ 'bg-blue-600 text-white ring-2 ring-blue-300': selectedSlot === slot, 'bg-white text-blue-600 border border-blue-200 hover:bg-blue-50': selectedSlot !== slot }"
                                                    class="px-4 py-2 rounded-lg font-semibold transition">
                                                <span x-text="slot"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Ringkasan & Konfirmasi -->
                        <div class="lg:col-span-1">
                            <div class="bg-white shadow-lg rounded-2xl p-6 sticky top-24">
                                <h2 class="text-xl font-bold text-gray-800 mb-4">2. Ringkasan Sesi</h2>
                                <div class="space-y-4 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Psikolog</span>
                                        <span class="font-semibold text-gray-800">{{ $psychologist->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tanggal</span>
                                        <span class="font-semibold text-gray-800" x-text="selectedDate ? selectedDate.fullDate : '-'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Waktu</span>
                                        <span class="font-semibold text-gray-800" x-text="selectedSlot || '-'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Tipe Sesi</span>
                                        <span class="font-semibold text-gray-800" x-text="selectedService ? selectedService.type.charAt(0).toUpperCase() + selectedService.type.slice(1) : '-'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Durasi</span>
                                        <span class="font-semibold text-gray-800" x-text="selectedService ? `${selectedService.duration_per_session_minutes} Menit` : '-'"></span>
                                    </div>
                                    <hr>
                                    <div class="flex justify-between text-lg">
                                        <span class="font-semibold text-gray-800">Total Biaya</span>
                                        <span class="font-bold text-blue-600" x-text="selectedService ? (selectedService.is_free ? 'Gratis' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(selectedService.price_per_session)) : 'Rp 0'"></span>
                                    </div>
                                </div>
                                <button type="submit" :disabled="!selectedDate || !selectedSlot || !selectedService" class="mt-6 w-full text-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                    Konfirmasi & Lanjutkan
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function bookingCalendar() {
            return {
                schedules: @json($schedulesByDay ?? []),
                onlineService: @json($onlineService ?? null),
                offlineService: @json($offlineService ?? null),

                selectedDate: null,
                selectedSlot: null,
                selectedService: null,
                nextSevenDays: [],
                availableSlots: [],

                init() {
                    this.selectedService = this.onlineService ?? this.offlineService;
                    this.generateNextSevenDays();
                },

                selectService(service) {
                    if (!service) return;
                    this.selectedService = service;
                    this.selectedDate = null;
                    this.selectedSlot = null;
                    this.availableSlots = [];
                    this.generateNextSevenDays();
                },

                generateNextSevenDays() {
                    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    let dates = [];
                    for (let i = 0; i < 7; i++) {
                        const date = new Date();
                        date.setDate(date.getDate() + i);
                        const dayName = dayNames[date.getDay()];

                        const year = date.getFullYear();
                        const month = (date.getMonth() + 1).toString().padStart(2, '0');
                        const day = date.getDate().toString().padStart(2, '0');
                        const localDateString = `${year}-${month}-${day}`;

                        const hasScheduleForType = this.selectedService && this.schedules[dayName]?.some(s => s.type === this.selectedService.type);

                        dates.push({
                            dayName: i === 0 ? 'Hari Ini' : dayName,
                            date: date.getDate(),
                            dateString: localDateString,
                            fullDate: date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric'}),
                            isAvailable: hasScheduleForType
                        });
                    }
                    this.nextSevenDays = dates;
                },

                selectDate(day) {
                    this.selectedDate = day;
                    this.selectedSlot = null;
                    this.generateAvailableSlots();
                },

                generateAvailableSlots() {
                    if (!this.selectedDate || !this.selectedService) {
                        this.availableSlots = [];
                        return;
                    }

                    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    
                    const dateParts = this.selectedDate.dateString.split('-');
                    const year = parseInt(dateParts[0], 10);
                    const month = parseInt(dateParts[1], 10) - 1;
                    const day = parseInt(dateParts[2], 10);
                    const localDate = new Date(year, month, day);
                    const selectedDayName = dayNames[localDate.getDay()];

                    const daySchedules = this.schedules[selectedDayName]?.filter(s => s.type === this.selectedService.type);
                    
                    if (!daySchedules || daySchedules.length === 0) {
                        this.availableSlots = [];
                        return;
                    }

                    let slots = [];
                    const duration = this.selectedService.duration_per_session_minutes;

                    daySchedules.forEach(schedule => {
                        const now = new Date();
                        let start = new Date(`${this.selectedDate.dateString}T${schedule.start_time}`);
                        const end = new Date(`${this.selectedDate.dateString}T${schedule.end_time}`);

                        while (start < end) {
                            if (start > now) {
                                // --- PERBAIKAN DI SINI ---
                                // Format waktu secara manual untuk memastikan format HH:MM
                                const hours = start.getHours().toString().padStart(2, '0');
                                const minutes = start.getMinutes().toString().padStart(2, '0');
                                slots.push(`${hours}:${minutes}`);
                                // --- AKHIR PERBAIKAN ---
                            }
                            start.setMinutes(start.getMinutes() + duration);
                        }
                    });

                    this.availableSlots = slots;
                },

                getSelectedDateTime() {
                    if (!this.selectedDate || !this.selectedSlot) {
                        return '';
                    }
                    // Sekarang this.selectedSlot sudah pasti dalam format HH:MM
                    return `${this.selectedDate.dateString} ${this.selectedSlot}:00`;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
