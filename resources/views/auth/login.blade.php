<x-guest-layout>
    <x-slot name="title">
        Masuk ke Akun Anda
    </x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Judul Utama Form -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800">Selamat Datang Kembali</h1>
        <p class="text-gray-500 mt-2">Masuk untuk melanjutkan perjalanan Anda.</p>
    </div>

    <!-- Tombol Social Login -->
    <div class="space-y-3">
        <button type="button" onclick="Swal.fire({icon: 'info', title: 'Fitur Segera Hadir!', text: 'Login dengan media sosial sedang dalam tahap pengembangan.'})" class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/><path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C16.318 4 9.656 8.337 6.306 14.691z"/><path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.222 0-9.657-3.356-11.303-7.962l-6.571 4.819C9.656 40.663 16.318 44 24 44z"/><path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C42.012 36.49 44 30.861 44 24c0-1.341-.138-2.65-.389-3.917z"/></svg>
            Masuk dengan Google
        </button>
    </div>

    <!-- Pemisah -->
    <div class="flex items-center my-6">
        <hr class="flex-grow border-gray-200">
        <span class="mx-4 text-xs font-semibold text-gray-400">ATAU</span>
        <hr class="flex-grow border-gray-200">
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- [REVISI] Password -->
        <div class="mt-4 space-y-2" x-data="{ showPassword: false }">
            <x-input-label for="password" value="Kata Sandi" />
            <div class="relative">
                <x-text-input id="password"
                              class="block mt-1 w-full pr-10"
                              x-bind:type="showPassword ? 'text' : 'password'"
                              name="password"
                              required
                              autocomplete="current-password" />
                
                {{-- [DIUBAH] Tombol untuk melihat/menyembunyikan kata sandi dengan warna yang lebih terlihat --}}
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-sm text-gray-600 hover:text-[#007BFF] focus:outline-none focus:text-[#007BFF] transition-colors">
                    <i class="fa-solid fa-eye" x-show="!showPassword"></i>
                    <i class="fa-solid fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#007BFF] shadow-sm focus:ring-[#007BFF]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="flex flex-col items-center justify-end mt-8 space-y-4">
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#007BFF] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                Masuk
            </button>

            <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                Belum punya akun? <span class="font-semibold text-[#007BFF]">Daftar di sini</span>
            </a>
        </div>
    </form>
</x-guest-layout>

