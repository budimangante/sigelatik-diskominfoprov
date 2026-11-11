<aside class="m-2 hidden h-[calc(100vh-1rem)] w-[280px] min-w-[280px] shrink-0 flex-col justify-between overflow-hidden rounded-[28px] border border-[#315773] bg-gradient-to-b from-[#1b405c] via-[#1a3d58] to-[#173952] text-white shadow-[0_12px_35px_rgba(15,45,65,0.16)] md:flex">
    <!-- Area navigasi yang dapat discroll -->
    <div class="min-h-0 flex-1 overflow-y-auto px-4 pb-4 pt-5 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#63849a_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-transparent hover:[&::-webkit-scrollbar-thumb]:bg-[#63849a]">
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
            <a href="{{ route('bidang.index') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.index') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.index') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-inbox text-sm"></i></span>Overview</span>
                @if(request()->routeIs('bidang.index'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('bidang.forward') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.forward') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.forward') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-shuffle text-sm"></i></span>Forward Ticket</span>
                @if(request()->routeIs('bidang.forward'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
            <a href="{{ route('bidang.history') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.history') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.history') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-clock-rotate-left text-sm"></i></span>History Ticket</span>
                @if(request()->routeIs('bidang.history'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- KATEGORI LAYANAN -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Kategori Layanan</p>
            <a href="{{ route('bidang.kategori') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.kategori') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.kategori') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-tag text-sm"></i></span>Kategori Layanan</span>
                @if(request()->routeIs('bidang.kategori'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- MANAGEMENT TICKETING -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Management Ticketing</p>
            <a href="{{ route('bidang.triage') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.triage') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.triage') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-shuffle text-sm"></i></span>Triage Ticket</span>
                @if(request()->routeIs('bidang.triage'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <!-- MANAGEMENT LAYANAN -->
            <p class="px-3 pb-2 pt-6 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Management Layanan</p>
            <a href="{{ route('bidang.sop') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('bidang.sop') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('bidang.sop') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-file-alt text-sm"></i></span>Manajemen SOP</span>
                @if(request()->routeIs('bidang.sop'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
        </nav>
    </div>

    <!-- Profil dan logout -->
    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="mb-3 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/10 p-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#347d9f] text-xs font-extrabold uppercase text-white shadow-sm">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : '' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->check() ? auth()->user()->name : '' }}</p>
                <p class="mt-0.5 truncate text-[10px] capitalize text-blue-100/70">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role) : '' }}</p>
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

@include('components.b-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarScroll = document.querySelector('aside .overflow-y-auto');
        if (!sidebarScroll) return;

        const savedScroll = localStorage.getItem('sidebar_scroll_bidang');
        if (savedScroll !== null) sidebarScroll.scrollTop = Number(savedScroll);

        sidebarScroll.addEventListener('scroll', function () {
            localStorage.setItem('sidebar_scroll_bidang', String(sidebarScroll.scrollTop));
        }, { passive: true });
    });
</script>
