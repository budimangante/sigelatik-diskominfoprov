@extends('superadmin.b-master')

@section('content')
<!-- Top Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- div ini diganti jadi a href dan ditambahin class block sama animasi di ujungnya -->
    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-red-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Jumlah Tiket</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-red-500 mt-1 font-bold">+0 bulan ini</p>
        </div>
        <div class="w-8 h-8 bg-red-50 rounded flex items-center justify-center text-red-500 text-sm"><i class="fas fa-ticket-alt"></i></div>
    </a>

    <!-- Kotak Perangkat Daerah (Biru) -->
    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-blue-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Perangkat Daerah</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-blue-500 mt-1 font-bold">Aktif</p>
        </div>
        <div class="w-8 h-8 bg-blue-50 rounded flex items-center justify-center text-blue-500 text-sm"><i class="fas fa-server"></i></div>
    </a>

    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-green-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Jumlah Bidang</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-green-500 mt-1 font-bold">Terdaftar</p>
        </div>
        <div class="w-8 h-8 bg-green-50 rounded flex items-center justify-center text-green-500 text-sm"><i class="fas fa-list-ul"></i></div>
    </a>

    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-purple-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Jumlah Pegawai</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-purple-500 mt-1 font-bold"> SSO BKD</p>
        </div>
        <div class="w-8 h-8 bg-purple-50 rounded flex items-center justify-center text-purple-500 text-sm"><i class="fas fa-users"></i></div>
    </a>

    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-orange-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Teknisi Helpdesk</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-orange-500 mt-1 font-bold">Bertugas</p>
        </div>
        <div class="w-8 h-8 bg-orange-50 rounded flex items-center justify-center text-orange-500 text-sm"><i class="fas fa-cog"></i></div>
    </a>

    <a href="#" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-red-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
        <div>
            <p class="text-[11px] text-gray-500 font-medium">Prioritas Tiket</p>
            <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
            <p class="text-[11px] text-red-500 mt-1 font-bold">Kritis</p>
        </div>
        <div class="w-8 h-8 bg-red-50 rounded flex items-center justify-center text-red-500 text-sm"><i class="fas fa-flag"></i></div>
    </a>
</div>

<!-- Middle Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
        <div class="flex justify-between items-start mb-2">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">Statistik Tiket Per Bulan</h4>
                <p class="text-[11px] text-blue-500">Jumlah tiket masuk setiap bulan</p>
            </div>
            <a href="#" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-48 mt-4"><canvas id="tiketBulanChart"></canvas></div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
        <div class="flex justify-between items-start mb-2">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">Statistik Peningkatan Pengguna</h4>
                <p class="text-[11px] text-blue-500">Pertumbuhan pengguna baru per bulan</p>
            </div>
            <a href="#" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-48 mt-4"><canvas id="penggunaChart"></canvas></div>
    </div>
</div>

<!-- Bottom Charts -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">Tiket per Kategori</h4>
                <p class="text-[11px] text-blue-500">Distribusi kategori</p>
            </div>
            <a href="{{ route('kategori.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-40"><canvas id="kategoriChart"></canvas></div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">Tiket per Prioritas</h4>
                <p class="text-[11px] text-blue-500">Tingkat prioritas</p>
            </div>
            <a href="{{ route('prioritas.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-40"><canvas id="prioritasChart"></canvas></div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">Tiket per Status</h4>
                <p class="text-[11px] text-blue-500">Status penanganan</p>
            </div>
            <a href="{{ route('daftar.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-40"><canvas id="statusChart"></canvas></div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h4 class="font-bold text-[13px] text-gray-800">User Verifikasi</h4>
                <p class="text-[11px] text-blue-500">Status verifikasi pengguna</p>
            </div>
            <a href="#" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
        </div>
        <div class="h-32 mt-2"><canvas id="verifikasiChart"></canvas></div>
        <div class="flex justify-center gap-4 mt-4 text-[10px] text-gray-500">
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Belum terverifikasi</div>
            <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Terverifikasi SSO</div>
        </div>
    </div>
</div>
@endsection