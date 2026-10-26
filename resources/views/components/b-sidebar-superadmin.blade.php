<aside class="w-[260px] bg-white border-r border-gray-200 flex flex-col justify-between hidden md:flex shrink-0">
        <div class="overflow-y-auto h-full pb-4 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#9ca3af_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <!-- logonya udah disesuaikan yang pakai gambar ya ma -->
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

           <!-- Management Profil -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Profil</p>
            <div>
                @php $profilAktif = request()->routeIs('profil.*'); @endphp
                
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-lg text-sm {{ $profilAktif ? 'text-[#1a3668] font-bold' : 'text-[#4b5563]' }}">
                    <div class="flex items-center gap-3"><i class="far fa-id-badge w-5"></i> Profil</div>
                    <i class="fas {{ $profilAktif ? 'fa-chevron-down' : 'fa-chevron-up' }} text-xs text-gray-400"></i>
                </button>
                
                <!-- class hidden otomatis hilang kalau menu profil lagi aktif -->
                <div class="pl-12 py-1 space-y-3 text-sm text-[#64748b] {{ $profilAktif ? '' : 'hidden' }}">
                    <a href="{{ route('profil.tentang') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('profil.tentang') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('profil.tentang') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Tentang Kami</a>
                    <a href="{{ route('profil.sub') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('profil.sub') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('profil.sub') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Sub Layanan</a>
                    <a href="{{ route('profil.team') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('profil.team') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('profil.team') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Team Infrastruktur</a>
                    <a href="{{ route('profil.link') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('profil.link') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('profil.link') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Link Layanan</a>
                </div>
            </div>
            
            <!-- Management Network -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Network</p>
            <div>
                <!-- Bikin variabel untuk ngecek apakah rute berawalan 'data.' sedang aktif -->
                @php $dataAktif = request()->routeIs('data.*'); @endphp
                
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-lg text-sm {{ $dataAktif ? 'text-[#1a3668] font-bold' : 'text-[#4b5563]' }}">
                    <div class="flex items-center gap-3"><i class="fas fa-server w-5"></i> Data Center</div>
                    <i class="fas {{ $dataAktif ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-gray-400"></i>
                </button>
                
                <!-- Class hidden diganti pakai logika biar gak ketutup pas direfresh -->
                <div class="pl-12 py-1 space-y-3 text-sm text-[#64748b] {{ $dataAktif ? '' : 'hidden' }}">
                    <a href="{{ route('data.vps') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('data.vps') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('data.vps') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Virtual Private Server</a>
                    <a href="{{ route('data.whm') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('data.whm') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('data.whm') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> WHM / C-Panel</a>
                </div>
            </div>

            <!-- Menu Metro -->
            <div>
                @php $metroAktif = request()->routeIs('metro.*'); @endphp
                
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-lg text-sm {{ $metroAktif ? 'text-[#1a3668] font-bold' : 'text-[#4b5563]' }}">
                    <div class="flex items-center gap-3"><i class="fas fa-chart-bar w-5"></i> Metro</div>
                    <i class="fas {{ $metroAktif ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-gray-400"></i>
                </button>
                
                <div class="pl-12 py-1 space-y-3 text-sm text-[#64748b] {{ $metroAktif ? '' : 'hidden' }}">
                    <a href="{{ route('metro.jaringan') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('metro.jaringan') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('metro.jaringan') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Jaringan Internet
                    </a>
                </div>
            </div>

            <!-- Management Device -->
            <p class="pt-5 pb-2 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Management Device</p>
            <div>
                @php $deviceAktif = request()->routeIs('device.*'); @endphp
                
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
                class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-50 rounded-lg text-sm {{ $deviceAktif ? 'text-[#1a3668] font-bold' : 'text-[#4b5563]' }}">
                    <div class="flex items-center gap-3"><i class="fas fa-desktop w-5"></i> List Device</div>
                    <i class="fas {{ $deviceAktif ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-gray-400"></i>
                </button>
                
                <div class="pl-12 py-1 space-y-3 text-sm text-[#64748b] {{ $deviceAktif ? '' : 'hidden' }}">
                    <a href="{{ route('device.software') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('device.software') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('device.software') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Software</a>
                    <a href="{{ route('device.hardware') }}" class="flex items-center gap-3 hover:text-[#1a3668] {{ request()->routeIs('device.hardware') ? 'text-[#1a3668] font-semibold' : '' }}">
                        <span class="w-1.5 h-1.5 border-2 rounded-full {{ request()->routeIs('device.hardware') ? 'border-[#1a3668] bg-[#1a3668]' : 'border-gray-300' }}"></span> Hardware</a>
                </div>
            </div>

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
    <!-- Bungkus icon dan teks dalam satu div agar tetap berdekatan di kiri -->
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle w-5"></i> Gangguan Layanan
                </div>
                
                <!-- Titik biru akan otomatis didorong ke kanan karena justify-between -->
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
    <div class="border-t border-gray-200 p-4">
        <!-- Ganti isian href dengan link tujuan kamu -->
        <a href="{{ route('profil.tentang') }}" class="w-full flex items-center justify-between group">
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
            
            <!-- Icon Logout (Muncul saat di-hover) -->
            <!-- Bagian Logout (Posisikan di bawah div profil Anda) -->
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0 border-t border-gray-200 pt-3">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 text-gray-500 hover:text-red-500 transition-colors group cursor-pointer bg-transparent border-0 p-0">
                    <i class="fas fa-sign-out-alt text-gray-400 group-hover:text-red-500 transition-colors"></i> 
                    <span class="text-sm font-medium group-hover:text-red-500 transition-colors">Log out</span>
                </button>
            </form>
        </a>
    </div>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // TAMBAHAN: Hapus ingatan browser khusus kalau lagi masuk ke halaman Dashboard
    @if(request()->routeIs('superadmin.index'))
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('sidebar_memori_')) {
                localStorage.removeItem(key);
            }
        });
    @endif

    // Mengambil semua tombol dropdown di sidebar
    const dropdownBtns = document.querySelectorAll('aside button');
    
    dropdownBtns.forEach((btn, index) => {
        const menuDiv = btn.nextElementSibling;
        if (!menuDiv) return;
        
        // Membuat kunci memori unik untuk setiap menu
        const menuKey = 'sidebar_memori_' + index;
        const icon = btn.querySelector('.fa-chevron-down, .fa-chevron-up');
        
        // Menghapus atribut onclick bawaan HTML secara otomatis agar tidak bentrok
        btn.removeAttribute('onclick');
        
        // Mengecek apakah di dalam dropdown ini ada menu yang sedang aktif diklik
        const adaMenuAktif = menuDiv.querySelector('.font-semibold.text-blue-600') !== null;
        
        if (adaMenuAktif) {
            // Jika ada menu yang aktif, pastikan dropdown selalu terbuka
            menuDiv.classList.remove('hidden');
            localStorage.setItem(menuKey, 'open');
            if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
        } else {
            // Jika tidak, cek ingatan browser apakah sebelumnya menu ini sengaja dibuka
            const statusTersimpan = localStorage.getItem(menuKey);
            if (statusTersimpan === 'open') {
                menuDiv.classList.remove('hidden');
                if(icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
            } else if (statusTersimpan === 'closed') {
                menuDiv.classList.add('hidden');
                if(icon) { icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
            }
        }

        // Fungsi klik baru untuk membuka/menutup dan menyimpan ingatan ke browser
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

    // KODE BARU UNTUK NAHAN SCROLL SIDEBAR
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