<!-- File ini HANYA berisi konten navigasi sidebar. -->
<div class="flex flex-col flex-grow bg-slate-800 pt-5 overflow-y-auto">
    <!-- Logo -->
    <div class="flex items-center flex-shrink-0 px-4">
        <a href="{{ route('admin.dashboard') }}" class="font-bold text-2xl text-white">
            Relax<span class="text-[#007BFF]">Boss</span>
        </a>
    </div>

    <!-- Navigasi -->
    <nav class="mt-8 flex-1 px-2 space-y-2">
        <a href="{{ route('admin.dashboard') }}" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-1-3.72a4 4 0 00-4 0A6 6 0 003 20v1h12z" /></svg>
            Manajemen User
        </a>

        <a href="{{ route('admin.quizzes.index') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.quizzes.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.quizzes.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            Manajemen Kuis
        </a>

        <a href="{{ route('admin.programs.index') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.programs.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.programs.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Manajemen Komunitas
        </a>

        <a href="{{ route('admin.specializations.index') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.specializations.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.specializations.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
            Bidang Keahlian
        </a>

        {{-- [BARU] Link untuk Manajemen Profil Psikolog --}}
        <a href="{{ route('admin.psychologists.index') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md
                  {{ request()->routeIs('admin.psychologists.*') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 {{ request()->routeIs('admin.psychologists.*') ? 'text-white' : 'text-slate-400 group-hover:text-slate-300' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            Profil Psikolog
        </a>
    </nav>

    <!-- Tombol Logout -->
    <div class="px-2 pb-4 mt-8">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="group flex items-center w-full px-3 py-2 text-sm font-medium rounded-md text-slate-300 hover:bg-slate-700 hover:text-white">
                <svg class="mr-3 h-6 w-6 text-slate-400 group-hover:text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                Keluar
            </button>
        </form>
    </div>
</div>
