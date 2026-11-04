@extends('components.b-master')

@section('title', 'Serangan Keamanan')
@section('subtitle', 'Monitoring insiden siber, kerentanan OPD, dan jenis ancaman keamanan')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-rose-700">CSIRT MONITORING</span>
                <span class="text-slate-300">•</span>
                <span>CYBER THREAT INTELLIGENCE</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Pusat Tanggap Insiden Keamanan Siber
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Visualisasi frekuensi serangan, ranking kerentanan OPD, serta distribusi vektor ancaman web dan jaringan.
            </p>
        </div>
    </div>

    <!-- 4 Indikator Metrik Siber -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Insiden -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Ancaman Tercatat</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $securityData['total_incidents'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md inline-block mt-1">Laporan Masuk</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-shield-virus"></i>
            </div>
        </div>

        <!-- OPD Paling Rentan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div class="max-w-[70%]">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">OPD Paling Sering Ditarget</p>
                <h3 class="text-sm font-extrabold text-slate-800 mt-1 truncate" title="{{ $securityData['most_vulnerable'] }}">
                    {{ $securityData['most_vulnerable'] }}
                </h3>
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1">Target Terbanyak</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-bullseye"></i>
            </div>
        </div>

        <!-- Vektor Serangan Dominan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div class="max-w-[70%]">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ancaman Terbanyak</p>
                <h3 class="text-sm font-extrabold text-[#1a3668] mt-1 truncate" title="{{ $securityData['dominant_attack'] }}">
                    {{ $securityData['dominant_attack'] }}
                </h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-1">Vektor Utama</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-biohazard"></i>
            </div>
        </div>

        <!-- Tingkat Penanganan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Mitigasi & Pemulihan</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">100%</h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md inline-block mt-1">SLA Tanggap Cepat</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Top 10 OPD Paling Rentan + Proporsi Jenis Serangan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Top 10 OPD (Horizontal Bar) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-building text-rose-600"></i>
                            <span>Top 10 OPD Paling Sering Terkena Serangan</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Rangking perangkat daerah berdasarkan akumulasi insiden serangan siber.</p>
                    </div>
                    <span class="text-[10px] bg-rose-50 text-rose-700 px-2 py-1 rounded-md font-bold uppercase">Prioritas Hardening</span>
                </div>
            </div>

            <div class="my-4 h-80">
                <canvas id="topOpdChart"></canvas>
            </div>

            <div class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span>Sumber: Log Laporan Insiden Helpdesk & CSIRT Provinsi</span>
                <span class="font-semibold text-slate-600">Data otomatis teragregasi</span>
            </div>
        </div>

        <!-- Chart 2: Komposisi Jenis Serangan (Doughnut) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-bug text-[#1a3668]"></i>
                    <span>Sebaran Jenis Serangan</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Klasifikasi tipe serangan keamanan siber.</p>
            </div>
            
            <div class="relative my-4 flex items-center justify-center">
                <div class="w-48 h-48">
                    <canvas id="attackTypeChart"></canvas>
                </div>
            </div>

            <div class="space-y-1.5 text-xs pt-3 border-t border-slate-100 max-h-44 overflow-y-auto pr-1">
                @php
                    $badgeColors = ['#e11d48', '#f59e0b', '#2563eb', '#7c3aed', '#059669', '#ea580c', '#64748b'];
                    $cIdx = 0;
                @endphp
                @foreach($securityData['attack_types'] as $typeName => $count)
                    <div class="flex items-center justify-between py-0.5">
                        <div class="flex items-center gap-2 truncate max-w-[75%]">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $badgeColors[$cIdx % count($badgeColors)] }}"></span>
                            <span class="text-slate-600 truncate text-[11px]">{{ $typeName }}</span>
                        </div>
                        <span class="font-bold text-slate-800 text-[11px]">{{ $count }}</span>
                    </div>
                    @php $cIdx++; @endphp
                @endforeach
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Tren Insiden Bulanan + Tabel Rincian Top 10 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 3: Tren Serangan Bulanan (Line Chart) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-[#1a3668]"></i>
                    <span>Tren Serangan 6 Bulan Terakhir</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Fluktuasi frekuensi serangan siber dari waktu ke waktu.</p>
            </div>

            <div class="my-4 h-64">
                <canvas id="trendChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-500 pt-3 border-t border-slate-100">
                Peningkatan sering terjadi menjelang periode update sistem atau libur panjang kedinasan.
            </p>
        </div>

        <!-- Tabel Leaderboard Top 10 Kerentanan OPD -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden lg:col-span-2 flex flex-col justify-between">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-list-ol text-[#1a3668]"></i>
                        <span>Peringkat 10 OPD Paling Rentan</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian status mitigasi dan urgensi per perangkat daerah.</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left whitespace-nowrap text-xs">
                    <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3 w-12 text-center">RANK</th>
                            <th class="px-5 py-3">PERANGKAT DAERAH / DINAS</th>
                            <th class="px-5 py-3 text-center">TOTAL SERANGAN</th>
                            <th class="px-5 py-3 text-center">URGENSI KRITIS</th>
                            <th class="px-5 py-3 text-center">STATUS PEMULIHAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($securityData['top10_opd'] as $idx => $opd)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-3 text-center font-bold">
                                    @if($idx == 0)
                                        <span class="w-5 h-5 rounded-full bg-rose-600 text-white inline-flex items-center justify-center text-[10px]">1</span>
                                    @elseif($idx == 1)
                                        <span class="w-5 h-5 rounded-full bg-amber-500 text-white inline-flex items-center justify-center text-[10px]">2</span>
                                    @elseif($idx == 2)
                                        <span class="w-5 h-5 rounded-full bg-blue-600 text-white inline-flex items-center justify-center text-[10px]">3</span>
                                    @else
                                        <span class="text-slate-400 font-mono">{{ $idx + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 font-bold text-slate-800">{{ $opd['name'] }}</td>
                                <td class="px-5 py-3 text-center font-bold text-rose-600 font-mono">{{ $opd['total'] }} kali</td>
                                <td class="px-5 py-3 text-center">
                                    @if($opd['critical'] > 0)
                                        <span class="bg-rose-50 text-rose-700 border border-rose-200 px-2 py-0.5 rounded text-[10px] font-bold">
                                            {{ $opd['critical'] }} Kritis
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[10px]">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-lg text-[10px] font-bold">
                                        {{ $opd['resolved'] }}/{{ $opd['total'] }} Pulih
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 italic">Belum ada data insiden serangan keamanan tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const topOpdData    = {!! json_encode($securityData['topOpdChart']) !!};
        const attackTypeData = {!! json_encode($securityData['attackTypeChart']) !!};
        const trendData     = {!! json_encode($securityData['trendChart']) !!};

        // 1. Horizontal Bar: Top 10 OPD Paling Rentan
        new Chart(document.getElementById('topOpdChart'), {
            type: 'bar',
            data: topOpdData,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.x} insiden serangan tercatat`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Doughnut: Sebaran Jenis Serangan
        new Chart(document.getElementById('attackTypeChart'), {
            type: 'doughnut',
            data: attackTypeData,
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
                                return ` ${context.label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // 3. Line Chart: Tren Insiden Bulanan
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: trendData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    });
</script>
@endsection