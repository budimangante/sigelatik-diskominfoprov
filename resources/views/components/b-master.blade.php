<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SIGELATIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        @font-face {
        font-family: 'StretchPro';
        src: url('/fonts/StretchPro.otf') format('opentype');
        font-weight: normal;
        font-style: normal;
        }
    </style>
    @vite(['resources/js/app.ts'])
</head>
<body class="bg-[#f8f9fa] flex h-screen overflow-hidden text-gray-700">

    {{-- Penentuan sidebar dinamis berdasarkan relasi role --}}
    @php
        $sidebarFile = 'components.b-sidebar-user'; // Default pelapor / umum

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->hasRole('super_admin')) {
                $sidebarFile = 'components.b-sidebar-superadmin';
            } elseif ($user->hasRole('admin_pemprov')) {
                $sidebarFile = 'components.b-sidebar-admin';
            } elseif ($user->hasRole('helpdesk_utama')) {
                $sidebarFile = 'components.b-sidebar-dinas';
            } elseif ($user->hasRole('helpdesk_bidang')) {
                $sidebarFile = 'components.b-sidebar-bidang';
            } elseif ($user->hasRole('teknisi')) {
                $sidebarFile = 'components.b-sidebar-teknisi';
            }
        }
    @endphp

    {{-- Render 1 sidebar saja sesuai role --}}
    @include($sidebarFile)

    <!-- AREA KONTEN UTAMA -->
    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        
        @include('components.b-nav')
        
        <main class="p-6 flex-1">
            @yield('content')
        </main>

        @include('components.b-footer')
        
    </div>

    @include('components.b-js')

    <script>
        @if (session('welcome') || session('success') || session('error'))
        (function showDashboardToast() {
            if (typeof window.showModernToast === 'function') {
                @if (session('welcome'))
                    window.showModernToast("{{ session('welcome') }}", 'success');
                @elseif (session('success'))
                    window.showModernToast("{{ session('success') }}", 'success');
                @elseif (session('error'))
                    window.showModernToast("{{ session('error') }}", 'error');
                @endif
            } else {
                setTimeout(showDashboardToast, 100);
            }
        })();
        @endif

        (function initReverbListener() {
            if (typeof window.Echo !== 'undefined' && typeof window.showModernToast === 'function') {
                window.Echo.channel('services-channel')
                    .listen('.service.event', (e) => {
                        window.showModernToast(e.message, e.type || 'success');
                    });
            } else {
                setTimeout(initReverbListener, 150);
            }
        })();
    </script>
</body>
</html>