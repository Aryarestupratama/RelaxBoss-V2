<!-- Wrapper untuk sidebar, menangani tampilan mobile dan desktop -->
<div x-show="sidebarOpen" 
     class="fixed inset-0 flex z-40 md:hidden" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
    <!-- Overlay gelap untuk mobile -->
    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    <!-- Konten sidebar mobile -->
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-800">
        @include('components.sidebar.psychologist-content')
    </div>
</div>

<!-- Sidebar untuk Desktop -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64">
        @include('components.sidebar.psychologist-content')
    </div>
</div>
