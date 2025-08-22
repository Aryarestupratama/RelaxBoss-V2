@props([
    'navItems' => [],
])

<nav x-data="{ scrolled: false, mobileOpen: false }"
     x-init="scrolled = window.scrollY > 10"
     @scroll.window="scrolled = window.scrollY > 10"
     :class="scrolled ? 'bg-white/95 backdrop-blur-lg shadow-md' : 'bg-white'"
     class="fixed w-full z-50 top-0 transition-all duration-300">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            <!-- Logo -->
            <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                {{-- <img src="..." alt="Logo" class="h-10 w-auto"> --}}
                <span class="font-bold text-2xl text-[#007BFF]">RelaxBoss</span>
            </a>

            <!-- Navigasi Desktop -->
            <div class="hidden md:flex space-x-8">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}"
                       class="relative text-gray-600 hover:text-[#007BFF] transition-colors duration-300 font-medium group">
                        {{ $item['label'] }}
                        <span class="absolute left-0 -bottom-1 h-0.5 bg-[#007BFF] w-0 group-hover:w-full transition-all duration-300"></span>
                    </a>
                @endforeach
            </div>

            <!-- Tombol Auth Desktop -->
            <div class="hidden md:flex items-center space-x-3">
                @guest
                    <a href="{{ route('login') }}" class="font-medium text-gray-600 hover:text-[#007BFF] px-4 py-2 rounded-md transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-[#007BFF] hover:bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-md transition-colors shadow-sm">
                        Daftar Gratis
                    </a>
                @else
                    <!-- Dropdown Profil Pengguna -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'https://placehold.co/40x40/007BFF/FFFFFF?text=' . strtoupper(substr(Auth::user()->name, 0, 1)) }}" class="w-10 h-10 rounded-full object-cover border-2 border-transparent hover:border-blue-300 transition" alt="Foto Profil">
                            <svg class="w-4 h-4 text-gray-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 origin-top-right py-1" style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Tombol Menu Mobile -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden focus:outline-none">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path :class="{'hidden': mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path :class="{'hidden': !mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @foreach ($navItems as $item)
                <a href="{{ $item['href'] }}" @click="mobileOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">{{ $item['label'] }}</a>
            @endforeach
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200">
            @guest
                <div class="px-2 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Masuk</a>
                    <a href="{{ route('register') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-[#007BFF]">Daftar Gratis</a>
                </div>
            @else
                <div class="px-5">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Keluar</button>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>
