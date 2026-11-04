@extends('components.b-master')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>SUPER ADMIN</span>
            <span class="text-gray-300">•</span>
            <span>DISKOMINFO</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Dashboard</h1>
        <p class="text-gray-500 text-xs">Selamat datang di panel administrasi SIGELATIK.</p>
    </div>

    <!-- Top Cards (Ringkasan Angka) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- 1. Jumlah Tiket -->
        <a href="{{ route('daftar.ticket') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-red-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Jumlah Tiket</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalTickets ?? 0) }}</h3>
                <p class="text-[11px] text-red-500 mt-1 font-bold">+{{ $thisMonthTickets ?? 0 }} bulan ini</p>
            </div>
            <div class="w-8 h-8 bg-red-50 rounded flex items-center justify-center text-red-500 text-sm"><i class="fas fa-ticket-alt"></i></div>
        </a>

        <!-- 2. Perangkat Daerah -->
        <a href="{{ route('perangkat') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-blue-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Perangkat Daerah</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($activeOpdCount ?? 0) }}</h3>
                <p class="text-[11px] text-blue-500 mt-1 font-bold">Aktif</p>
            </div>
            <div class="w-8 h-8 bg-blue-50 rounded flex items-center justify-center text-blue-500 text-sm"><i class="fas fa-server"></i></div>
        </a>

        <!-- 3. Jumlah Bidang -->
        <a href="{{ route('bidang') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-green-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Jumlah Bidang</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalBidang ?? 0) }}</h3>
                <p class="text-[11px] text-green-500 mt-1 font-bold">Terdaftar</p>
            </div>
            <div class="w-8 h-8 bg-green-50 rounded flex items-center justify-center text-green-500 text-sm"><i class="fas fa-list-ul"></i></div>
        </a>

        <!-- 4. Jumlah Pegawai -->
        <a href="{{ route('pengguna') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-purple-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Jumlah Pegawai</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalPegawai ?? 0) }}</h3>
                <p class="text-[11px] text-purple-500 mt-1 font-bold">SSO BKD</p>
            </div>
            <div class="w-8 h-8 bg-purple-50 rounded flex items-center justify-center text-purple-500 text-sm"><i class="fas fa-users"></i></div>
        </a>

        <!-- 5. Teknisi Helpdesk -->
        <a href="{{ route('pengguna') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-orange-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Teknisi Helpdesk</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($teknisiCount ?? 0) }}</h3>
                <p class="text-[11px] text-orange-500 mt-1 font-bold">Bertugas</p>
            </div>
            <div class="w-8 h-8 bg-orange-50 rounded flex items-center justify-center text-orange-500 text-sm"><i class="fas fa-cog"></i></div>
        </a>

        <!-- 6. Prioritas Tiket -->
        <a href="{{ route('prioritas.ticket') }}" class="block bg-white p-5 rounded-xl border border-gray-200 shadow-sm border-t-4 border-t-red-500 flex justify-between items-start transition-all duration-300 hover:-translate-y-1 hover:shadow-lg cursor-pointer">
            <div>
                <p class="text-[11px] text-gray-500 font-medium">Prioritas Tiket</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($criticalTickets ?? 0) }}</h3>
                <p class="text-[11px] text-red-500 mt-1 font-bold">Kritis</p>
            </div>
            <div class="w-8 h-8 bg-red-50 rounded flex items-center justify-center text-red-500 text-sm"><i class="fas fa-flag"></i></div>
        </a>
    </div>

    <!-- Middle Charts (Line Charts) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <!-- 1. Statistik Tiket Per Bulan -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">Statistik Tiket Per Bulan</h4>
                    <p class="text-[11px] text-blue-500">Jumlah tiket masuk setiap bulan</p>
                </div>
                <a href="{{ route('daftar.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
            </div>
            <div class="h-48 mt-4 relative"><canvas id="tiketBulanChart"></canvas></div>
        </div>

        <!-- 2. Statistik Rata-rata SLA (Respon vs Penanganan) -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm relative">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">Statistik Rata-rata SLA</h4>
                    <p class="text-[11px] text-blue-500">Durasi respon triage vs penanganan teknisi (Menit)</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-semibold">
                    <span class="flex items-center gap-1.5 text-blue-600">
                        <span class="w-2.5 h-1 rounded-full bg-blue-600"></span> Respon (SLA 1)
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-600">
                        <span class="w-2.5 h-1 rounded-full bg-amber-500"></span> Penanganan (SLA 2)
                    </span>
                </div>
            </div>
            <div class="h-48 mt-4 relative">
                <canvas id="averageSlaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Charts (4 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Tiket per Kategori -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">Tiket per Kategori</h4>
                    <p class="text-[11px] text-blue-500">Distribusi kategori</p>
                </div>
                <a href="{{ route('kategori.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
            </div>
            <div class="h-40 relative"><canvas id="kategoriChart"></canvas></div>
        </div>

        <!-- 2. Tiket per Status -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">Tiket per Status</h4>
                    <p class="text-[11px] text-blue-500">Status penanganan</p>
                </div>
                <a href="{{ route('daftar.ticket') }}" class="text-[11px] text-blue-500 font-medium hover:underline">Lihat ></a>
            </div>
            <div class="h-40 relative"><canvas id="statusChart"></canvas></div>
        </div>

        <!-- 3. User Belum Terverifikasi -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">User Blm Terverifikasi</h4>
                    <p class="text-[11px] text-rose-500">Belum aktivasi email/SSO</p>
                </div>
                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                    {{ $chartData['userVerification']['unverified']['count'] ?? 0 }} User
                </span>
            </div>
            <div class="h-32 mt-2 relative flex items-center justify-center">
                <canvas id="unverifiedChart"></canvas>
            </div>
            <div class="flex justify-center items-center gap-2 mt-4 text-[10px] text-gray-500 font-semibold">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>{{ $chartData['userVerification']['unverified']['percentage'] ?? 0 }}% Belum Terverifikasi</span>
            </div>
        </div>

        <!-- 4. User Terverifikasi -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h4 class="font-bold text-[13px] text-gray-800">User Terverifikasi</h4>
                    <p class="text-[11px] text-emerald-500">Terdaftar aktif di sistem</p>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                    {{ $chartData['userVerification']['verified']['count'] ?? 0 }} User
                </span>
            </div>
            <div class="h-32 mt-2 relative flex items-center justify-center">
                <canvas id="verifiedChart"></canvas>
            </div>
            <div class="flex justify-center items-center gap-2 mt-4 text-[10px] text-gray-500 font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ $chartData['userVerification']['verified']['percentage'] ?? 0 }}% Terverifikasi SSO</span>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const charts = @json($chartData);

    // 1. Line: Statistik Tiket Per Bulan
    new Chart(document.getElementById('tiketBulanChart'), {
        type: 'line',
        data: charts.ticketMonthly,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // 2. Multi-Line: Rata-rata SLA (Respon & Penanganan)
    new Chart(document.getElementById('averageSlaChart'), {
        type: 'line',
        data: charts.averageSla,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' Menit';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 10 },
                        callback: function(value) { return value + ' mnt'; }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });

    // 3. Vertical Bar: Tiket per Kategori
    new Chart(document.getElementById('kategoriChart'), {
        type: 'bar',
        data: charts.ticketCategory,
        options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 9 }, stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { ticks: { font: { size: 9 } }, grid: { display: false } }
            }
        }
    });

    // 4. Horizontal Bar: Tiket per Status
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: charts.ticketStatus,
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { font: { size: 9 } }, grid: { color: '#f1f5f9' } },
                y: { ticks: { font: { size: 9, weight: 'bold' } }, grid: { display: false } }
            }
        }
    });

    // 5. Donut: User Belum Terverifikasi
    new Chart(document.getElementById('unverifiedChart'), {
        type: 'doughnut',
        data: charts.userVerification.unverified.chart,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: { legend: { display: false } }
        }
    });

    // 6. Donut: User Terverifikasi
    new Chart(document.getElementById('verifiedChart'), {
        type: 'doughnut',
        data: charts.userVerification.verified.chart,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection