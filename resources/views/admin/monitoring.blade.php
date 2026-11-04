@extends('components.b-master')

@section('title', 'Monitoring Tiket Layanan')
@section('subtitle', 'Monitoring performa penanganan tiket dan pelaporan kinerja per layanan TIK')

@section('content')
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section + Tombol Reporting -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">SERVICE MONITORING</span>
                <span class="text-slate-300">•</span>
                <span>HELPDESK REPORTING</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Monitoring & Reporting Tiket Layanan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pemantauan terpadu beban kerja teknis, kepatuhan SLA, dan agregasi data aduan per jenis layanan.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs cursor-pointer">
                <i class="fas fa-print text-[#1a3668]"></i>
                <span>Cetak Reporting</span>
            </button>
            <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-50 border border-blue-200 rounded-xl text-xs font-bold text-[#1a3668] shadow-2xs">
                <i class="fas fa-cogs"></i>
                <span>{{ $monitoringData['total_services'] ?? 0 }} Layanan Aktif</span>
            </div>
        </div>
    </div>

    <!-- 4 Indikator Metrik Kinerja Layanan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Layanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Unit Layanan</p>
                <h3 class="text-2xl font-extrabold text-[#1a3668] mt-1">{{ $monitoringData['total_services'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-1">Katalog Aktif</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-toolbox"></i>
            </div>
        </div>

        <!-- Total Tiket Layanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Tiket Layanan</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $monitoringData['total_tickets'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-1">Akumulasi Permintaan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>

        <!-- Layanan Paling Banyak Diminta -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div class="max-w-[70%]">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Layanan Terpopuler</p>
                <h3 class="text-sm font-extrabold text-amber-600 mt-1 truncate" title="{{ $monitoringData['top_service'] }}">
                    {{ $monitoringData['top_service'] }}
                </h3>
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1">Paling Sering Masuk</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <!-- Rata-rata Penyelesaian Layanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rasio Selesai</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $monitoringData['global_solve_rate'] ?? 0 }}%</h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md inline-block mt-1">Tingkat Efektivitas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row: Bar Chart & Doughnut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Bar Chart Beban Kerja per Layanan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs lg:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-[#1a3668]"></i>
                    <span>Monitoring Status Tiket Tiap Layanan</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan jumlah tiket masuk, diproses, dan telah selesai per layanan.</p>
            </div>

            <div class="my-4 h-72">
                <canvas id="serviceBarChart"></canvas>
            </div>

            <div class="flex items-center justify-end gap-6 text-xs text-slate-500 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-slate-200 inline-block"></span>
                    <span>Masuk</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>
                    <span>Diproses</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span>
                    <span>Selesai</span>
                </div>
            </div>
        </div>

        <!-- Chart 2: Doughnut Proporsi Tiket per Layanan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-[#1a3668]"></i>
                    <span>Proporsi Permintaan Layanan</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Porsi volume tiket aduan berdasarkan jenis layanan.</p>
            </div>
            
            <div class="relative my-4 flex items-center justify-center">
                <div class="w-48 h-48">
                    <canvas id="serviceDoughnutChart"></canvas>
                </div>
            </div>

            <div class="space-y-1.5 text-xs pt-3 border-t border-slate-100 max-h-40 overflow-y-auto pr-1">
                @foreach($monitoringData['reportingTable'] as $item)
                    <div class="flex items-center justify-between py-0.5">
                        <div class="flex items-center gap-2 truncate max-w-[75%]">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                            <span class="text-slate-600 truncate text-[11px]">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-bold text-slate-800 text-[11px]">{{ $item['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reporting Section: Rekapitulasi Data Kinerja Layanan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-file-invoice text-[#1a3668]"></i>
                <span>Reporting Kinerja Tiket Layanan</span>
            </h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Laporan agregasi tiket masuk, penyelesaian, dan target SLA per layanan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap text-xs">
                <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">NAMA LAYANAN</th>
                        <th class="px-6 py-3.5">KATEGORI</th>
                        <th class="px-6 py-3.5 text-center">TARGET SLA</th>
                        <th class="px-6 py-3.5 text-center">MASUK</th>
                        <th class="px-6 py-3.5 text-center">DIPROSES</th>
                        <th class="px-6 py-3.5 text-center">SELESAI</th>
                        <th class="px-6 py-3.5 text-center">TINGKAT PENYELESAIAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monitoringData['reportingTable'] as $row)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-3.5 font-bold text-slate-800 flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $row['color'] }}"></span>
                                <span>{{ $row['name'] }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $row['category'] }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="bg-slate-100 text-slate-700 font-mono text-[10px] px-2 py-0.5 rounded font-bold">
                                    {{ $row['sla_target'] }} Jam
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold text-slate-800">{{ $row['total'] }}</td>
                            <td class="px-6 py-3.5 text-center text-blue-600 font-semibold">{{ $row['process'] }}</td>
                            <td class="px-6 py-3.5 text-center text-emerald-600 font-semibold">{{ $row['resolved'] }}</td>
                            <td class="px-6 py-3.5 text-center">
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
                            <td colspan="7" class="py-10 text-center text-slate-400 italic">
                                Belum ada data tiket layanan yang tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Inisialisasi Script Chart.js -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barData      = {!! json_encode($monitoringData['serviceBarChart']) !!};
        const doughnutData = {!! json_encode($monitoringData['serviceDoughnutChart']) !!};

        // 1. Bar Chart: Monitoring Status per Layanan
        new Chart(document.getElementById('serviceBarChart'), {
            type: 'bar',
            data: barData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

        // 2. Doughnut Chart: Proporsi Permintaan Layanan
        new Chart(document.getElementById('serviceDoughnutChart'), {
            type: 'doughnut',
            data: doughnutData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let value = context.parsed;
                                let percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${value} tiket (${percent}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection