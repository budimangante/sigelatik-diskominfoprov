@extends('components.b-master')

@section('title', 'Kinerja Dinas & Bidang')
@section('subtitle', 'Evaluasi Kinerja Pelayanan TIK per Dinas dan Bidang')

@section('content')
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- 1. Header & Quick Stat Pills -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
                <span class="text-[#1a3668]">MARKET ANALYTICS</span>
                <span class="text-slate-300">•</span>
                <span>KINERJA PERANGKAT DAERAH</span>
            </div>
            <h1 class="text-2xl font-black text-[#1a3668] tracking-tight">
                Pusat Analisis Kinerja Layanan & SLA
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pemantauan tren penyelesaian dan durasi layanan dengan indikator volatilitas waktu riil.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-center min-w-[100px]">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total Dinas</p>
                <p class="text-lg font-black text-[#1a3668]">{{ $kinerjaData['total_dinas'] ?? 0 }}</p>
            </div>
            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-xl text-center min-w-[100px]">
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wide">Total Aduan</p>
                <p class="text-lg font-black text-blue-600">{{ $kinerjaData['total_tickets'] ?? 0 }}</p>
            </div>
            <div class="px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-center min-w-[100px]">
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Rata-rata SLA</p>
                <p class="text-lg font-black text-emerald-600">{{ $kinerjaData['global_avg_hours'] ?? 0 }}<span class="text-xs font-semibold ml-0.5">Jam</span></p>
            </div>
        </div>
    </div>

    <!-- 2. Dua Chart Bergaya Bursa Efek (TradingView / Stock Style) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Stock Chart 1: Indeks Tren Efektivitas Bidang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between min-w-0">
            <!-- Ticker Header -->
            <div class="pb-3 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">

                        <h3 class="text-sm font-bold text-slate-800">Indeks Kepatuhan & Efektivitas</h3>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-slate-800 font-mono tracking-tight">96.8%</span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                            <i class="fas fa-arrow-trend-up"></i> +4.2% Bullish
                        </span>
                    </div>
                </div>

                <!-- Range Pills Ala Trading -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-[10px] font-bold text-slate-500">
                    <span class="px-2 py-0.5 bg-white text-[#1a3668] rounded shadow-2xs">6M</span>
                    <span class="px-2 py-0.5">YTD</span>
                    <span class="px-2 py-0.5">1Y</span>
                </div>
            </div>

            <!-- Area Canvas Terkunci -->
            <div class="relative w-full h-64 my-3">
                <canvas id="stockKinerjaChart"></canvas>
            </div>

            <!-- Stock Legend Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-4 font-semibold">
                    <span class="flex items-center gap-1.5 text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Keamanan Informasi
                    </span>
                    <span class="flex items-center gap-1.5 text-blue-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Infrastruktur Jaringan
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">Real-time Stream</span>
            </div>
        </div>

        <!-- Stock Chart 2: Volatilitas Kecepatan Resolusi vs Batas SLA -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between min-w-0">
            <!-- Ticker Header -->
            <div class="pb-3 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-800">Kecepatan Resolusi SLA</h3>
                    </div>
                    <div class="flex items-baseline gap-2 mt-2">
                        <span class="text-2xl font-black text-slate-800 font-mono tracking-tight">2.4 <span class="text-sm font-semibold text-slate-500">Jam</span></span>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                            <i class="fas fa-bolt"></i> 1.6 Jam Lebih Cepat dr Target
                        </span>
                    </div>
                </div>

                <!-- Range Pills Ala Trading -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-[10px] font-bold text-slate-500">
                    <span class="px-2 py-0.5 bg-white text-[#1a3668] rounded shadow-2xs">6M</span>
                    <span class="px-2 py-0.5">AVG</span>
                </div>
            </div>

            <!-- Area Canvas Terkunci -->
            <div class="relative w-full h-64 my-3">
                <canvas id="stockDurasiChart"></canvas>
            </div>

            <!-- Stock Legend Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-4 font-semibold">
                    <span class="flex items-center gap-1.5 text-[#1a3668]">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#1a3668]"></span> Durasi Aktual (Jam)
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-500">
                        <span class="w-3 h-0.5 border-t-2 border-dashed border-amber-500"></span> Batas 4.0 Jam
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 font-mono">Tolerance Band</span>
            </div>
        </div>

    </div>

    <!-- 3. Stacked Horizontal Bar: Kinerja Penanganan per Dinas -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs min-w-0">
        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bars-progress text-[#1a3668]"></i>
                    <span>Proporsi Penanganan Tiket per Dinas</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Komposisi tiket yang telah selesai dibanding yang masih diproses per dinas.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-emerald-600">
                    <span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Selesai
                </span>
                <span class="flex items-center gap-1.5 text-blue-600">
                    <span class="w-2.5 h-2.5 rounded bg-blue-600"></span> Diproses
                </span>
            </div>
        </div>

        <div class="relative w-full h-64 my-3">
            <canvas id="dinasStackedChart"></canvas>
        </div>
    </div>

    <!-- 4. Tabel Rincian Data Kinerja per Dinas -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden min-w-0">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-table text-[#1a3668]"></i>
                    <span>Tabel Kinerja Penanganan per Dinas</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Rincian status tiket yang diajukan masing-masing dinas/perangkat daerah.</p>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left whitespace-nowrap text-xs">
                <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">PERANGKAT DAERAH / DINAS</th>
                        <th class="px-5 py-3 text-center">TOTAL MASUK</th>
                        <th class="px-5 py-3 text-center">SEDANG DIPROSES</th>
                        <th class="px-5 py-3 text-center">TELAH SELESAI</th>
                        <th class="px-5 py-3 text-center">TINGKAT PENYELESAIAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kinerjaData['dinasTable'] as $row)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-3 font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#1a3668] shrink-0"></span>
                                <span>{{ $row['name'] }}</span>
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-slate-700 font-mono">{{ $row['total'] }}</td>
                            <td class="px-5 py-3 text-center text-blue-600 font-semibold font-mono">{{ $row['process'] }}</td>
                            <td class="px-5 py-3 text-center text-emerald-600 font-semibold font-mono">{{ $row['resolved'] }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <div class="w-20 bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $row['solve_rate'] }}%"></div>
                                    </div>
                                    <span class="font-bold text-[11px] font-mono text-slate-600">{{ $row['solve_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 italic">Belum ada data dinas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Inisialisasi Script Chart.js Bergaya Saham -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stockKinerjaData = {!! json_encode($kinerjaData['stockKinerja'] ?? ['labels' => [], 'datasets' => []]) !!};
        const stockDurasiData  = {!! json_encode($kinerjaData['stockDurasi'] ?? ['labels' => [], 'datasets' => []]) !!};
        const stackedData      = {!! json_encode($kinerjaData['dinasStackedChart'] ?? ['labels' => [], 'datasets' => []]) !!};

        // 1. Stock Chart: Kinerja Bidang
        const ctxKinerja = document.getElementById('stockKinerjaChart').getContext('2d');
        const gradGreen = ctxKinerja.createLinearGradient(0, 0, 0, 240);
        gradGreen.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
        gradGreen.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        const gradBlue = ctxKinerja.createLinearGradient(0, 0, 0, 240);
        gradBlue.addColorStop(0, 'rgba(37, 99, 235, 0.22)');
        gradBlue.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

        if (stockKinerjaData.datasets[0]) stockKinerjaData.datasets[0].backgroundColor = gradGreen;
        if (stockKinerjaData.datasets[1]) stockKinerjaData.datasets[1].backgroundColor = gradBlue;

        new Chart(ctxKinerja, {
            type: 'line',
            data: stockKinerjaData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#f8fafc',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        usePointStyle: true,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y}%`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9', borderDash: [4, 4], drawBorder: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        position: 'right', // Posisi kanan khas bursa efek
                        min: 60,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: (v) => v + '%',
                            font: { size: 10 },
                            color: '#94a3b8'
                        },
                        grid: { color: '#f1f5f9', borderDash: [4, 4], drawBorder: false }
                    }
                }
            }
        });

        // 2. Stock Chart: Durasi SLA
        const ctxDurasi = document.getElementById('stockDurasiChart').getContext('2d');
        const gradNavy = ctxDurasi.createLinearGradient(0, 0, 0, 240);
        gradNavy.addColorStop(0, 'rgba(26, 54, 104, 0.28)');
        gradNavy.addColorStop(1, 'rgba(26, 54, 104, 0.0)');

        if (stockDurasiData.datasets[1]) stockDurasiData.datasets[1].backgroundColor = gradNavy;

        new Chart(ctxDurasi, {
            type: 'line',
            data: stockDurasiData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#f8fafc',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        usePointStyle: true,
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y} Jam`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9', borderDash: [4, 4], drawBorder: false },
                        ticks: { font: { size: 10 }, color: '#94a3b8' }
                    },
                    y: {
                        position: 'right', // Posisi kanan khas bursa efek
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: (v) => v + 'j',
                            font: { size: 10 },
                            color: '#94a3b8'
                        },
                        grid: { color: '#f1f5f9', borderDash: [4, 4], drawBorder: false }
                    }
                }
            }
        });

        // 3. Stacked Horizontal Bar
        new Chart(document.getElementById('dinasStackedChart'), {
            type: 'bar',
            data: stackedData,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endsection