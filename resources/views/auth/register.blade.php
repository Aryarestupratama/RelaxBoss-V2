<x-guest-layout>
    @php
        // [FIX] Mendefinisikan variabel $title untuk digunakan di dalam view ini.
        $title = 'Buat Akun Baru';
    @endphp

    {{-- Mengatur judul tab browser dan judul form secara dinamis --}}
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{
        @php
            // Logika untuk menentukan step awal jika ada error validasi dari server
            $initialStep = 1;
            if ($errors->hasAny(['full_name', 'job_title', 'gender', 'birth_date'])) {
                $initialStep = 2;
            } elseif ($errors->has('profile_picture')) {
                $initialStep = 3;
            }
        @endphp
        step: {{ $initialStep }},
        formData: {
            name: '{{ old('name') }}',
            email: '{{ old('email') }}',
            password: '',
            password_confirmation: '',
            full_name: '{{ old('full_name') }}',
            job_title: '{{ old('job_title') }}',
            gender: '{{ old('gender', 'male') }}',
            birth_date: '{{ old('birth_date') }}',
        },
        profilePicturePreview: null,
        isStepValid() {
            if (this.step === 1) {
                return this.formData.name &&
                       this.formData.email &&
                       this.formData.password &&
                       this.formData.password.length >= 8 &&
                       this.formData.password_confirmation &&
                       (this.formData.password === this.formData.password_confirmation);
            }
            if (this.step === 2) {
                return this.formData.full_name &&
                       this.formData.job_title &&
                       this.formData.gender &&
                       this.formData.birth_date;
            }
            return true; // Step 3 has no mandatory validation
        },
        updatePreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.profilePicturePreview = URL.createObjectURL(file);
            }
        }
    }">

        <!-- Form Title -->
        <div class="mb-8 text-center">
            {{-- [FIX] Menggunakan variabel $title yang sudah didefinisikan --}}
            <h1 class="text-3xl font-bold text-gray-800">{{ $title }}</h1>
            <p class="text-gray-500 mt-2">Hanya butuh beberapa langkah untuk memulai.</p>
        </div>

        <!-- Progress Bar -->
        <div class="relative mb-8">
            <!-- Wrapper untuk garis agar posisi dan lebarnya akurat -->
            <div class="absolute top-5 left-0 right-0 mx-auto w-[calc(100%-2.5rem)]">
                <!-- Garis Latar Belakang -->
                <div class="w-full h-0.5 bg-gray-200 rounded-full"></div>
                <!-- Garis Progres Aktif -->
                <div class="absolute top-0 left-0 h-0.5 bg-[#007BFF] rounded-full transition-all duration-500" :style="`width: ${((step - 1) / 2) * 100}%`"></div>
            </div>

            <!-- Kontainer Lingkaran Step -->
            <div class="relative flex justify-between z-10">
                <!-- Step 1 -->
                <div class="text-center">
                    <div :class="step >= 1 ? 'bg-[#007BFF] text-white border-[#007BFF]' : 'bg-white border-gray-300'" class="w-10 h-10 mx-auto rounded-full flex items-center justify-center border-2 transition-all duration-300">
                        <span x-show="step <= 1" x-transition>1</span>
                        {{-- [DITAMBAHKAN] Ikon centang untuk step yang selesai --}}
                        <svg x-show="step > 1" x-transition class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <p class="mt-2 text-xs font-semibold" :class="step >= 1 ? 'text-[#007BFF]' : 'text-gray-500'">Akun</p>
                </div>
                <!-- Step 2 -->
                <div class="text-center">
                    <div :class="step >= 2 ? 'bg-[#007BFF] text-white border-[#007BFF]' : 'bg-white border-gray-300'" class="w-10 h-10 mx-auto rounded-full flex items-center justify-center border-2 transition-all duration-300">
                        <span x-show="step <= 2" x-transition>2</span>
                         {{-- [DITAMBAHKAN] Ikon centang untuk step yang selesai --}}
                        <svg x-show="step > 2" x-transition class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <p class="mt-2 text-xs font-semibold" :class="step >= 2 ? 'text-[#007BFF]' : 'text-gray-500'">Profil</p>
                </div>
                <!-- Step 3 -->
                <div class="text-center">
                    <div :class="step >= 3 ? 'bg-[#007BFF] text-white border-[#007BFF]' : 'bg-white border-gray-300'" class="w-10 h-10 mx-auto rounded-full flex items-center justify-center border-2 transition-all duration-300">
                        <span>3</span>
                    </div>
                    <p class="mt-2 text-xs font-semibold" :class="step >= 3 ? 'text-[#007BFF]' : 'text-gray-500'">Foto</p>
                </div>
            </div>
        </div>


        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: Account Info -->
            <div x-show="step === 1" class="space-y-4">
                <div>
                    <x-input-label for="name" value="Nama Panggilan" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" x-model="formData.name" required autofocus autocomplete="name" placeholder="Bagaimana kami harus memanggil Anda?"/>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" x-model="formData.email" required autocomplete="username" placeholder="contoh@email.com"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="password" value="Kata Sandi" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" x-model="formData.password" required autocomplete="new-password" />
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" x-model="formData.password_confirmation" required autocomplete="new-password" />
                        <p x-show="formData.password && formData.password_confirmation && formData.password !== formData.password_confirmation" class="text-sm text-red-600 mt-1">Kata sandi tidak cocok.</p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Profile Details -->
            <div x-show="step === 2" class="space-y-4" style="display: none;">
                <div>
                    <x-input-label for="full_name" value="Nama Lengkap" />
                    <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" x-model="formData.full_name" required />
                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="job_title" value="Pekerjaan" />
                    <x-text-input id="job_title" class="block mt-1 w-full" type="text" name="job_title" x-model="formData.job_title" required />
                    <x-input-error :messages="$errors->get('job_title')" class="mt-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="gender" value="Jenis Kelamin" />
                        <select id="gender" name="gender" x-model="formData.gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                            <option value="other">Lainnya</option>
                            <option value="private">Tidak ingin menyebutkan</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="birth_date" value="Tanggal Lahir" />
                        <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" x-model="formData.birth_date" required />
                        <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Step 3: Personalization -->
            <div x-show="step === 3" class="space-y-4" style="display: none;">
                <div>
                    <x-input-label for="profile_picture" value="Unggah Foto Profil (Opsional)" />
                    <label for="profile_picture" class="mt-1 flex justify-center w-full h-32 px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md cursor-pointer hover:border-[#007BFF]">
                        <div class="space-y-1 text-center">
                            <svg x-show="!profilePicturePreview" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <img x-show="profilePicturePreview" :src="profilePicturePreview" class="mx-auto h-28 w-auto object-contain" alt="Pratinjau Foto Profil">
                            <div x-show="!profilePicturePreview" class="flex text-sm text-gray-600">
                                <p class="pl-1">Klik untuk mengunggah</p>
                            </div>
                        </div>
                        <input id="profile_picture" name="profile_picture" type="file" class="sr-only" @change="updatePreview" accept="image/*">
                    </label>
                </div>
            </div>


            <!-- Form Navigation Buttons -->
            <div class="flex items-center mt-8" :class="step > 1 ? 'justify-between' : 'justify-end'">
                <button type="button" x-show="step > 1" @click="step--" class="text-sm font-semibold text-gray-600 hover:text-[#007BFF] transition-colors">
                    &larr; Kembali
                </button>

                <button type="button" x-show="step < 3" @click="step++" :disabled="!isStepValid()"
                        class="inline-flex items-center px-6 py-2 bg-[#007BFF] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                    Lanjutkan
                </button>

                <button type="submit" x-show="step === 3" style="display: none;"
                        class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    Selesai & Daftar
                </button>
            </div>

            <div class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a class="font-semibold text-[#007BFF] hover:underline" href="{{ route('login') }}">
                    Masuk di sini
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>

