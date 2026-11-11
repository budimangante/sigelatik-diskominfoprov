<aside class="m-2 hidden h-[calc(100vh-1rem)] w-[280px] min-w-[280px] shrink-0 flex-col justify-between overflow-hidden rounded-[28px] border border-[#315773] bg-gradient-to-b from-[#1b405c] via-[#1a3d58] to-[#173952] text-white shadow-[0_12px_35px_rgba(15,45,65,0.16)] md:flex">
    <!-- Area navigasi yang dapat discroll: mengikuti sidebar Bidang -->
    <div class="h-full overflow-y-auto px-4 pb-4 pt-5 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#63849a_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-transparent hover:[&::-webkit-scrollbar-thumb]:bg-[#63849a]">

        <!-- Logo SIGELATIK -->
        <div class="mb-6 flex items-center gap-3 border-b border-white/10 px-2 pb-5">
            <img src="{{ asset('img/logofavicon.png') }}" alt="Logo SIGELATIK" class="h-10 w-10 shrink-0 object-contain">
            <div class="min-w-0">
                <h1 class="text-[16px] font-extrabold leading-tight tracking-wide text-white">SIGELATIK</h1>
                <p class="mt-0.5 text-[10px] leading-tight text-blue-100/75">Sistem Informasi Gangguan<br>Layanan TIK</p>
            </div>
        </div>

        <nav class="space-y-1">
            <!-- UTAMA -->
            <p class="px-3 pb-2 pt-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Utama</p>
            <a href="{{ route('superadmin.index') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('superadmin.index') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('superadmin.index') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-home text-sm"></i></span>
                    <span>Dashboard</span>
                </span>
                @if(request()->routeIs('superadmin.index'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- MANAGEMENT INSIDEN -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Management Insiden</p>
            <a href="{{ route('serangan') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('serangan') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('serangan') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-eye text-sm"></i></span>
                    <span>Serangan Keamanan</span>
                </span>
                @if(request()->routeIs('serangan'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('gangguan') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('gangguan') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('gangguan') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-signal text-sm"></i></span>
                    <span>Gangguan Layanan</span>
                </span>
                @if(request()->routeIs('gangguan'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- MANAGEMENT TICKETING -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Management Ticketing</p>
            <a href="{{ route('kategori.ticket') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('kategori.ticket') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('kategori.ticket') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-tag text-sm"></i></span>
                    <span>Kategori Tiket</span>
                </span>
                @if(request()->routeIs('kategori.ticket'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('prioritas.ticket') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('prioritas.ticket') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('prioritas.ticket') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-flag text-sm"></i></span>
                    <span>Prioritas Tiket</span>
                </span>
                @if(request()->routeIs('prioritas.ticket'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('daftar.ticket') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('daftar.ticket') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('daftar.ticket') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-list-ul text-sm"></i></span>
                    <span>Daftar Tiket</span>
                </span>
                @if(request()->routeIs('daftar.ticket'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- MANAGEMENT PENGGUNA -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Management Pengguna</p>
            <a href="{{ route('perangkat') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('perangkat') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('perangkat') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-building text-sm"></i></span>
                    <span>Perangkat Daerah</span>
                </span>
                @if(request()->routeIs('perangkat'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('bidang') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-layer-group text-sm"></i></span>
                    <span>Bidang</span>
                </span>
                @if(request()->routeIs('bidang'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('notifikasi') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('notifikasi') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('notifikasi') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-bell text-sm"></i></span>
                    <span>Notifikasi</span>
                </span>
                @if(request()->routeIs('notifikasi'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('pengguna') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('pengguna') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('pengguna') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-users text-sm"></i></span>
                    <span>Pengguna</span>
                </span>
                @if(request()->routeIs('pengguna'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
        </nav>
    </div>

    <!-- Profil dan logout: mengikuti komposisi sidebar Bidang -->
    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="mb-3 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/10 p-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#347d9f] text-xs font-extrabold uppercase text-white shadow-sm">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : '' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->check() ? auth()->user()->name : '' }}</p>
                <p class="mt-0.5 truncate text-[10px] capitalize text-blue-100/70">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role ?? (auth()->user()->roles->first()->name ?? 'Super Admin')) : '' }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0 border-t border-white/10 pt-3">
            @csrf
            <button type="submit" class="group flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-left text-[#ff2348] transition-colors duration-200 hover:bg-[#e58b9a]/10 hover:text-[#e4566b]">
                <i class="fas fa-sign-out-alt text-[15px] transition-transform duration-200 group-hover:-translate-x-0.5"></i>
                <span class="text-[13px] font-bold">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pertahankan perilaku reset memori menu saat dashboard superadmin dibuka.
    @if(request()->routeIs('superadmin.index'))
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('sidebar_memori_')) {
                localStorage.removeItem(key);
            }
        });
    @endif

    // Simpan dan pulihkan posisi scroll navigasi sidebar.
    const sidebarScroll = document.querySelector('aside .overflow-y-auto');

    if (sidebarScroll) {
        try {
            const posisiScroll = localStorage.getItem('sidebar_scroll');
            if (posisiScroll !== null) {
                sidebarScroll.scrollTop = Number(posisiScroll) || 0;
            }

            sidebarScroll.addEventListener('scroll', function () {
                localStorage.setItem('sidebar_scroll', sidebarScroll.scrollTop);
            }, { passive: true });
        } catch (error) {
            // Sidebar tetap berfungsi meskipun localStorage tidak tersedia.
        }
    }
});
</script>
