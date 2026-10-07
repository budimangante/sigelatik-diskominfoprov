<header class="bg-[#f8f9fa] px-6 py-5 flex justify-between items-center">
    <div>
        <h1 class="text-[32px] font-bold text-[#1a3668] leading-tight">@yield('title', 'Dashboard')</h1>
        <p class="text-[12px] text-blue-500 font-medium">@yield('subtitle', 'Selamat datang di panel administrasi SIGELATIK')</p>
    </div>
    <div class="text-[12px] font-medium text-gray-400">
        <a href="{{ route('superadmin.index') }}" class="text-blue-500 hover:underline">Home</a> / @yield('title', 'Dashboard')
    </div>
</header>