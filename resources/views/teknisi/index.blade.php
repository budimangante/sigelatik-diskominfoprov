@extends('components.b-master') 
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>HELPDESK</span>
            <span class="text-gray-300">•</span>
            <span>TEKNISI</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Task Ticket</h1>
        <p class="text-gray-500">Tiket yang ditugaskan kepada Anda beserta sisa waktu SLA.</p>
    </div>

    <!-- Filter & Pencarian -->
    <div class="mb-8 space-y-4">
        <!-- Kolom Pencarian -->
        <div class="flex gap-4">
            <input type="text" placeholder="Cari nomor tiket, mis. HD-2610-0142" class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-gray-700 shadow-sm">
            <button class="bg-[#1e3a8a] text-white px-8 py-3 rounded-lg text-sm font-semibold hover:bg-[#152a6a] transition-colors shadow-sm shrink-0">
                Cari
            </button>
        </div>
        
        <!-- Dropdown Filter -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <select class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none shadow-sm cursor-pointer">
                    <option>Semua Dinas</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
            <div class="relative">
                <select class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none shadow-sm cursor-pointer">
                    <option>Semua Bidang</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
            <div class="relative">
                <select class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none shadow-sm cursor-pointer">
                    <option>Semua Status</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- Daftar Tiket -->
    <div class="space-y-4">

        <!-- Tiket 1: Berjalan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-[11px] font-mono text-gray-500 mb-2">HD-2610-0142</div>
                    <h2 class="text-base font-bold text-[#1a3668] leading-tight">Akun email dinas tidak bisa login</h2>
                    <p class="text-xs text-gray-500 mt-1">Dinas Pendidikan · Ratna Wulandari</p>
                </div>
                
                <!-- Status Badge -->
                <div class="bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center gap-1.5 uppercase tracking-wider">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-600"></div> Process
                </div>
            </div>

            <!-- SLA Progress Bar (Berjalan) -->
            <div class="mt-8">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-xs font-bold text-[#1e3a8a]">Berjalan</span>
                    <span class="text-[10px] text-gray-400">target 4 jam</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <!-- Panjang progress bar diatur via style width (misal 65%) -->
                    <div class="h-full bg-[#1e3a8a] rounded-full" style="width: 65%;"></div>
                </div>
            </div>

            <!-- Floating Chat Icon (Ada Notif 1) -->
            <button class="absolute top-1/2 right-6 -translate-y-1/2 w-10 h-10 bg-[#1e3a8a] hover:bg-[#152a6a] text-white rounded-full shadow-md flex items-center justify-center transition-colors">
                <i class="far fa-comment-dots text-lg"></i>
                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">1</div>
            </button>
        </div>

        <!-- Tiket 2: Terlampaui -->
        <div class="bg-white rounded-xl shadow-sm border border-[#1e3a8a] p-6 relative">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-[11px] font-mono text-[#1e3a8a] mb-2">HD-2610-0120</div>
                    <h2 class="text-base font-bold text-[#1a3668] leading-tight">Aplikasi SIMPEG error 500 saat simpan</h2>
                    <p class="text-xs text-gray-500 mt-1">BKPSDM · Agus Salim</p>
                </div>
                
                <!-- Status Badge -->
                <div class="bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold flex items-center gap-1.5 uppercase tracking-wider">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-600"></div> Process
                </div>
            </div>

            <!-- SLA Progress Bar (Terlampaui - Merah Penuh) -->
            <div class="mt-8">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-xs font-bold text-red-600">Terlampaui</span>
                    <span class="text-[10px] text-gray-400">target 1 hari</span>
                </div>
                <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <!-- Lebar 100% dan warna diubah menjadi merah (bg-red-600) -->
                    <div class="h-full bg-red-600 rounded-full" style="width: 100%;"></div>
                </div>
            </div>

            <!-- Floating Chat Icon (Tanpa Notif) -->
            <button class="absolute top-1/2 right-6 -translate-y-1/2 w-10 h-10 bg-[#1e3a8a] hover:bg-[#152a6a] text-white rounded-full shadow-md flex items-center justify-center transition-colors">
                <i class="far fa-comment-dots text-lg"></i>
            </button>
        </div>

    </div>

</div>
</body>
</html>
@endsection