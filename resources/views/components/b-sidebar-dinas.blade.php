<aside class="w-[260px] bg-white border-r border-gray-200 flex flex-col justify-between hidden md:flex shrink-0">
    <div class="overflow-y-auto h-full pb-4 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#9ca3af_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
    
        <!-- Bagian Logo -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <img src="{{ asset('img/logofavicon.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <h1 class="text-[16px] font-bold text-[#1e3a8a] leading-tight">SIGELATIK</h1>
                    <p class="text-[10px] text-gray-500 leading-tight">Sistem Informasi Gangguan<br>Layanan TIK</p>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-1">
            
            <!-- Kategori UTAMA -->
            <p class="pt-2 pb-2 px-4 text-[10px] font-bold text-[#9ca3af] uppercase tracking-wider">Utama</p>
            <a href="{{ route('dinas.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('dinas.index') ? 'bg-[#f4f7fb] text-[#1a3668]' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-home w-5"></i> Overview
                </div>
                @if(request()->routeIs('dinas.index'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

            <!-- Kategori MANAGEMENT TICKETING -->
            <p class="pt-2 pb-2 px-4 text-[10px] font-bold text-[#9ca3af] uppercase tracking-wider">Management Ticketing</p>
            <a href="{{ route('dinas.triage') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('dinas.triage') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exchange-alt w-5"></i> Triage & Reassign
                </div>
                @if(request()->routeIs('dinas.triage'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

            <!-- Kategori MANAGEMENT LAYANAN -->
            <p class="pt-2 pb-2 px-4 text-[10px] font-bold text-[#9ca3af] uppercase tracking-wider">Management Layanan</p>
            <a href="{{ route('dinas.sop') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('dinas.sop') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="far fa-file-alt w-5"></i> Manajemen SOP
                </div>
                @if(request()->routeIs('dinas.sop'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

        </nav>
    </div>

    <!-- AREA PROFIL ADMIN (DI LUAR SCROLL) -->
    <div class="border-t border-gray-100 p-4">
        <div class="w-full flex flex-col gap-4">
            
            <!-- Info Profil Aktif -->
            <div class="flex items-center gap-3 px-2 pt-2">
                <!-- Inisial Profil Dinamis -->
                <div class="w-10 h-10 rounded-full bg-[#0d9488] flex items-center justify-center text-white font-bold text-sm uppercase shrink-0">
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : '' }}
                </div>
                
                <div class="text-left overflow-hidden">
                    <!-- Nama Admin Dinamis -->
                    <p class="text-sm font-bold text-[#1e3a8a] leading-none mb-1 truncate">
                        {{ auth()->check() ? auth()->user()->name : '' }}
                    </p>
                    <!-- Role Dinamis -->
                    <p class="text-xs text-[#64748b] leading-none truncate capitalize">
                        {{ auth()->check() ? str_replace('_', ' ', auth()->user()->role) : '' }}
                    </p>
                </div>
            </div>
            
            <!-- Tombol Keluar (Logout) -->
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 border-t border-gray-200 pt-3">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 text-gray-500 hover:text-red-500 transition-colors group cursor-pointer bg-transparent border-0 p-0">
                    <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-red-500 transition-colors"></i> 
                    <span class="text-sm font-medium group-hover:text-red-500 transition-colors">Log out</span>
                </button>
            </form>
            
        </div>
    </div>
</aside>

@include('components.b-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // KODE UNTUK NAHAN SCROLL SIDEBAR
    const sidebarScroll = document.querySelector('aside .overflow-y-auto')
    
    if (sidebarScroll) {
        // pas halaman beres ngeload balikin posisi scroll dari memori
        const posisiScroll = localStorage.getItem('sidebar_scroll')
        if (posisiScroll !== null) {
            sidebarScroll.scrollTop = posisiScroll
        }
        
        // tiap kali discroll simpan posisi angkanya ke memori browser
        sidebarScroll.addEventListener('scroll', function() {
            localStorage.setItem('sidebar_scroll', sidebarScroll.scrollTop)
        })
    }
});
</script>