<aside class="w-[260px] bg-white border-r border-gray-200 flex flex-col justify-between hidden md:flex shrink-0">
    <div class="overflow-y-auto h-full pb-4 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#9ca3af_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
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
            <!-- Dashboard -->
            <a href="{{ route('superadmin.index') }}" class="flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs('superadmin.index') ? 'bg-[#f4f7fb] text-[#1a3668]' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3"><i class="fas fa-home w-5"></i> Dashboard</div>
                @if(request()->routeIs('superadmin.index'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

            <!-- Management Insiden -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Insiden</p>
            <a href="{{ route('serangan') }}" wire:navigate class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('serangan') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="far fa-eye w-5"></i> Serangan Keamanan
                </div>
                @if(request()->routeIs('serangan'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
                
            <a href="{{ route('gangguan') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('gangguan') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle w-5"></i> Gangguan Layanan
                </div>
                @if(request()->routeIs('gangguan'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

            <!-- Management Ticketing -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Ticketing</p>
            <a href="{{ route('kategori.ticket') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('kategori.ticket') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-tag w-5"></i> Kategori Tiket
                </div>
                @if(request()->routeIs('kategori.ticket'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
            <a href="{{ route('prioritas.ticket') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('prioritas.ticket') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="far fa-flag w-5"></i> Prioritas Tiket
                </div>
                @if(request()->routeIs('prioritas.ticket'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
            <a href="{{ route('daftar.ticket') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('daftar.ticket') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-list-ul w-5"></i> Daftar Tiket
                </div>
                @if(request()->routeIs('daftar.ticket'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>

            <!-- Management Pengguna -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Pengguna</p>
            <a href="{{ route('perangkat') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('perangkat') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="far fa-building w-5"></i> Perangkat Daerah
                </div>
                @if(request()->routeIs('perangkat'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
            <a href="{{ route('bidang') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('bidang') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-layer-group w-5"></i> Bidang
                </div>
                @if(request()->routeIs('bidang'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
            <a href="{{ route('notifikasi') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('notifikasi') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="far fa-bell w-5"></i> Notifikasi
                </div>
                @if(request()->routeIs('notifikasi'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
            <a href="{{ route('pengguna') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-sm {{ request()->routeIs('pengguna') ? 'bg-[#f4f7fb] text-[#1a3668] font-bold' : 'text-[#4b5563] hover:bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-users w-5"></i> Pengguna
                </div>
                @if(request()->routeIs('pengguna'))
                    <i class="fas fa-circle text-[6px]"></i>
                @endif
            </a>
        </nav>
    </div>

    <!-- AREA PROFIL ADMIN (DI LUAR SCROLL) -->
    <div class="border-t border-gray-200 p-4 space-y-3">
        <a href="#" class="w-full flex items-center justify-between group">
            <div class="flex items-center gap-3 px-2 pt-2">
                <div class="w-10 h-10 rounded-full bg-[#0d9488] flex items-center justify-center text-white font-bold text-sm uppercase shrink-0">
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 2) : '' }}
                </div>
                
                <div class="text-left overflow-hidden">
                    <p class="text-sm font-bold text-[#1e3a8a] leading-none mb-1 truncate">
                        {{ auth()->check() ? auth()->user()->name : '' }}
                    </p>
                    <p class="text-xs text-[#64748b] leading-none truncate capitalize">
                        {{ auth()->check() ? str_replace('_', ' ', auth()->user()->role ?? (auth()->user()->roles->first()->name ?? 'Super Admin')) : '' }}
                    </p>
                </div>
            </div>
        </a>

        <!-- Form Logout -->
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 border-t border-gray-100 pt-3">
            @csrf
            <button type="submit" class="w-full flex items-center gap-4 px-2 text-gray-500 hover:text-red-500 transition-colors group cursor-pointer bg-transparent border-0">
                <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-red-500 transition-colors"></i> 
                <span class="text-sm font-medium group-hover:text-red-500 transition-colors">Log out</span>
            </button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(request()->routeIs('superadmin.index'))
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('sidebar_memori_')) {
                localStorage.removeItem(key);
            }
        });
    @endif

    const dropdownBtns = document.querySelectorAll('aside button');
    
    dropdownBtns.forEach((btn, index) => {
        const menuDiv = btn.nextElementSibling;
        if (!menuDiv) return;
        
        const menuKey = 'sidebar_memori_' + index;
        const icon = btn.querySelector('.fa-chevron-down, .fa-chevron-up');
        
        btn.removeAttribute('onclick');
        
        const adaMenuAktif = menuDiv.querySelector('.font-semibold.text-blue-600') !== null;
        
        if (adaMenuAktif) {
            menuDiv.classList.remove('hidden');
            localStorage.setItem(menuKey, 'open');
            if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
        } else {
            const statusTersimpan = localStorage.getItem(menuKey);
            if (statusTersimpan === 'open') {
                menuDiv.classList.remove('hidden');
                if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
            } else if (statusTersimpan === 'closed') {
                menuDiv.classList.add('hidden');
                if(icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
            }
        }

        btn.addEventListener('click', function() {
            menuDiv.classList.toggle('hidden');
            
            if (menuDiv.classList.contains('hidden')) {
                localStorage.setItem(menuKey, 'closed');
                if(icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
            } else {
                localStorage.setItem(menuKey, 'open');
                if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
            }
        });
    });

    const sidebarScroll = document.querySelector('aside .overflow-y-auto');
    if (sidebarScroll) {
        const posisiScroll = localStorage.getItem('sidebar_scroll');
        if (posisiScroll !== null) {
            sidebarScroll.scrollTop = posisiScroll;
        }
        
        sidebarScroll.addEventListener('scroll', function() {
            localStorage.setItem('sidebar_scroll', sidebarScroll.scrollTop);
        });
    }
});
</script>