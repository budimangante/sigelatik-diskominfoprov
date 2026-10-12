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
            <span>Helpdesk Utama</span>
            <span class="text-gray-300">•</span>
            <span>Dinas Kominfo</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Overview lintas bidang</h1>
        <p class="text-gray-500">Seluruh permohonan perangkat daerah dalam satu pandangan.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-t-4 border-t-red-500">
            <h3 class="text-gray-500 text-sm mb-1">Total permohonan</h3>
            <p class="text-4xl font-bold text-[#1a3668] mb-1">0</p>
            <p class="text-xs text-red-500 font-medium">bulan ini</p>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-t-4 border-t-blue-500">
            <h3 class="text-gray-500 text-sm mb-1">Menunggu triage</h3>
            <p class="text-4xl font-bold text-[#1a3668] mb-1">0</p>
            <p class="text-xs text-blue-500 font-medium">bulan ini</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-t-4 border-t-green-500">
            <h3 class="text-gray-500 text-sm mb-1">Dalam proses</h3>
            <p class="text-4xl font-bold text-[#1a3668] mb-1">0</p>
            <p class="text-xs text-green-500 font-medium">bulan ini</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 border-t-4 border-t-orange-500">
            <h3 class="text-gray-500 text-sm mb-1">SLA terlampaui</h3>
            <p class="text-4xl font-bold text-[#1a3668] mb-1">0</p>
            <p class="text-xs text-orange-500 font-medium">bulan ini</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Panel: Tiket per bidang -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-[#1a3668] mb-6">Tiket per bidang</h2>
            
            <div class="space-y-5">
                <!-- Progress Item 1 -->
                <div>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Layanan Aplikasi</span>
                        <span class="font-medium">0</span>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5">
                        <div class="bg-[#1a3668] h-2.5 rounded-full" style="width: 2%"></div>
                    </div>
                </div>

                <!-- Progress Item 2 -->
                <div>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Infrastruktur Jaringan</span>
                        <span class="font-medium">0</span>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5">
                        <div class="bg-[#1a3668] h-2.5 rounded-full" style="width: 2%"></div>
                    </div>
                </div>

                <!-- Progress Item 3 -->
                <div>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Keamanan Informasi</span>
                        <span class="font-medium">0</span>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5">
                        <div class="bg-[#1a3668] h-2.5 rounded-full" style="width: 2%"></div>
                    </div>
                </div>

                <!-- Progress Item 4 -->
                <div>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Statistik & Data</span>
                        <span class="font-medium">0</span>
                    </div>
                    <div class="w-full bg-gray-50 rounded-full h-2.5">
                        <div class="bg-[#1a3668] h-2.5 rounded-full" style="width: 2%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Tiket terbaru -->
        <div>
            <h2 class="text-lg font-bold text-[#1a3668] mb-4">Tiket terbaru</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-medium tracking-wider">Tiket</th>
                            <th class="px-6 py-4 font-medium tracking-wider">Bidang</th>
                            <th class="px-6 py-4 font-medium tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600">
                        <!-- Row 1 -->
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-blue-600 font-medium">HD-2610-0142</td>
                            <td class="px-6 py-4">Contoh Tiket</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-green-200 text-green-700 bg-green-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> NEW
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
</body>
</html>
@endsection