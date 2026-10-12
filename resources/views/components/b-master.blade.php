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
    </style>
</head>
<body class="bg-[#f8f9fa] flex h-screen overflow-hidden text-gray-700">

    {{-- LOGIKA PEMANGGILAN SIDEBAR DINAMIS YANG AMAN --}}
    @php
        // 1. Tentukan file default jika belum login atau role tidak dikenali
        $sidebarFile = 'components.b-sidebar-user'; 
        
        // 2. Cek apakah ada yang login
        if (auth()->check()) {
            $role = auth()->user()->role; // Ambil role dari database
            
            // 3. Cocokkan role dengan nama file persis seperti di gambar Anda
            if ($role === 'super_admin' || $role === 'superadmin') {
                $sidebarFile = 'components.b-sidebar-superadmin';
            } elseif ($role === 'helpdesk_utama' || $role === 'dinas') {
                $sidebarFile = 'components.b-sidebar-dinas';
            } elseif ($role === 'helpdesk_bidang' || $role === 'bidang') {
                $sidebarFile = 'components.b-sidebar-bidang';
            } elseif ($role === 'admin_default' || $role === 'admin_pemprov' || $role === 'admin') {
                $sidebarFile = 'components.b-sidebar-admin';
            } elseif ($role === 'helpdesk_teknisi' || $role === 'teknisi') {
                $sidebarFile = 'components.b-sidebar-teknisi';
            } else {
                // Jika rolenya user atau lainnya
                $sidebarFile = 'components.b-sidebar-user';
            }
        }
    @endphp

    {{-- 4. Panggil file yang sudah dipastikan aman di atas --}}
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
</body>
</html>