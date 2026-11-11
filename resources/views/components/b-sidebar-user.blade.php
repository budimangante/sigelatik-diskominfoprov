<aside class="m-2 hidden h-[calc(100vh-1rem)] w-[280px] min-w-[280px] shrink-0 flex-col justify-between overflow-hidden rounded-[28px] border border-[#315773] bg-gradient-to-b from-[#1b405c] via-[#1a3d58] to-[#173952] text-white shadow-[0_12px_35px_rgba(15,45,65,0.16)] md:flex">
    <div class="min-h-0 flex-1 overflow-y-auto px-4 pb-4 pt-5 [scrollbar-width:thin] [scrollbar-color:transparent_transparent] hover:[scrollbar-color:#63849a_transparent] [&::-webkit-scrollbar]:w-[5px] [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-transparent hover:[&::-webkit-scrollbar-thumb]:bg-[#63849a]">
        <!-- Logo dan identitas SIGELATIK -->
        <div class="mb-6 flex items-center gap-3 border-b border-white/10 px-2 pb-5">
            <img src="{{ asset('img/logofavicon.png') }}" alt="Logo SIGELATIK" class="h-10 w-10 shrink-0 object-contain">
            <div class="min-w-0">
                <h1 class="text-[16px] font-extrabold leading-tight tracking-wide text-white">SIGELATIK</h1>
                <p class="mt-0.5 text-[10px] leading-tight text-blue-100/75">Sistem Informasi Gangguan<br>Layanan TIK</p>
            </div>
        </div>

        <nav class="space-y-1">
            <p class="px-3 pb-2 pt-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#91b4ca]">Permohonan</p>

            <a href="{{ route('user.index') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('user.index') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('user.index') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-ticket-alt text-sm"></i></span>Buat Tiket</span>
                @if(request()->routeIs('user.index'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <a href="{{ route('user.tiket') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('user.tiket') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('user.tiket') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="fas fa-list-ul text-sm"></i></span>Tiket Saya</span>
                @if(request()->routeIs('user.tiket'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>

            <a href="{{ route('user.chat') }}" class="group flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-semibold transition duration-200 {{ request()->routeIs('user.chat') ? 'bg-[#3286b077] text-white shadow-[0_5px_14px_rgba(5,25,40,0.16)]' : 'text-blue-50/85 hover:bg-white/10 hover:text-white' }}">
                <span class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ request()->routeIs('user.chat') ? 'bg-white/15 text-white' : 'bg-white/10 text-blue-100 group-hover:bg-white/15' }}"><i class="far fa-comments text-sm"></i></span>Chat Teknisi</span>
                @if(request()->routeIs('user.chat'))<i class="fas fa-circle text-[6px] text-white"></i>@endif
            </a>
        </nav>
    </div>

    <!-- Profil pelapor dan logout -->
    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="mb-3 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/10 p-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#347d9f] text-xs font-extrabold uppercase text-white shadow-sm">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : '' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-white">{{ auth()->check() ? auth()->user()->name : '' }}</p>
                <p class="mt-0.5 truncate text-[10px] capitalize text-blue-100/70">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role ?? 'Pelapor') : '' }}</p>
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
        const sidebarScroll = document.querySelector('aside .overflow-y-auto');
        if (!sidebarScroll) return;
        try {
            const savedScroll = localStorage.getItem('sidebar_scroll_user');
            if (savedScroll !== null) sidebarScroll.scrollTop = Number(savedScroll) || 0;
            sidebarScroll.addEventListener('scroll', function () {
                localStorage.setItem('sidebar_scroll_user', String(sidebarScroll.scrollTop));
            }, { passive: true });
        } catch (error) {
            // Sidebar tetap dapat digunakan meskipun penyimpanan browser tidak tersedia.
        }
    });
</script>
