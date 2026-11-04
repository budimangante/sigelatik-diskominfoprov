@extends('components.b-master')

@section('title', 'Prioritas Tiket')
@section('subtitle', 'Daftar dan Analisis Prioritas Tiket')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">HELPDESK ANALYTICS</span>
                <span class="text-slate-300">•</span>
                <span>URGENCY BREAKDOWN</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Statistik Prioritas Tiket
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Visualisasi beban kerja tiket berdasarkan urgensi penanganan dan standar durasi SLA.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
            <i class="fas fa-ticket-alt text-[#1a3668]"></i>
            <span>Total: {{ $prioritasData['total'] ?? 0 }} Tiket</span>
        </div>
    </div>

    <!-- 4 Indikator Angka -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kritis -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kritis (Critical)</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $prioritasData['counts']['critical'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-md inline-block mt-1">SLA: 4 Jam</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-bolt"></i>
            </div>
        </div>

        <!-- Tinggi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tinggi (High)</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $prioritasData['counts']['high'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1">SLA: 8 Jam</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Sedang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sedang (Medium)</p>
                <h3 class="text-2xl font-extrabold text-[#1a3668] mt-1">{{ $prioritasData['counts']['medium'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-1">SLA: 24 Jam</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <!-- Rendah -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rendah (Low)</p>
                <h3 class="text-2xl font-extrabold text-slate-600 mt-1">{{ $prioritasData['counts']['low'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-1">SLA: 48 Jam</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-clipboard-check"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Doughnut + Bar Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Doughnut Chart: Komposisi -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-[#1a3668]"></i>
                    <span>Komposisi Urgensi</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Persentase tiket masuk berdasarkan prioritas.</p>
            </div>
            
            <div class="relative my-6 flex items-center justify-center">
                <div class="w-52 h-52">
                    <canvas id="priorityDoughnutChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-rose-500 inline-block"></span>
                    <span class="text-slate-600">Kritis: <b>{{ $prioritasData['counts']['critical'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-amber-500 inline-block"></span>
                    <span class="text-slate-600">Tinggi: <b>{{ $prioritasData['counts']['high'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-[#1a3668] inline-block"></span>
                    <span class="text-slate-600">Sedang: <b>{{ $prioritasData['counts']['medium'] ?? 0 }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-slate-400 inline-block"></span>
                    <span class="text-slate-600">Rendah: <b>{{ $prioritasData['counts']['low'] ?? 0 }}</b></span>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Total vs Selesai -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs lg:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-[#1a3668]"></i>
                    <span>Tingkat Penyelesaian Tiket</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan total tiket masuk terhadap tiket yang berhasil diselesaikan.</p>
            </div>

            <div class="my-4 h-72">
                <canvas id="priorityStatusBarChart"></canvas>
            </div>

            <div class="flex items-center justify-end gap-6 text-xs text-slate-500 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-slate-200 inline-block"></span>
                    <span>Total Masuk</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span>
                    <span>Telah Selesai</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: SLA Matrix -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs">
        <div class="mb-4">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-stopwatch text-[#1a3668]"></i>
                <span>Standar Durasi Maksimal SLA (Jam)</span>
            </h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Matriks batas waktu perbaikan tiket yang dijadikan acuan kepatuhan SLA.</p>
        </div>
        <div class="h-60">
            <canvas id="slaHorizontalBarChart"></canvas>
        </div>
    </div>

</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const doughnutData   = {!! json_encode($prioritasData['doughnutChart']) !!};
        const comparisonData = {!! json_encode($prioritasData['comparisonBarChart']) !!};
        const slaMatrixData  = {!! json_encode($prioritasData['slaMatrixChart']) !!};

        // 1. Doughnut Chart
        new Chart(document.getElementById('priorityDoughnutChart'), {
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

        // 2. Bar Chart (Total vs Selesai)
        new Chart(document.getElementById('priorityStatusBarChart'), {
            type: 'bar',
            data: comparisonData,
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

        // 3. Horizontal Bar Chart (SLA Matrix)
        new Chart(document.getElementById('slaHorizontalBarChart'), {
            type: 'bar',
            data: slaMatrixData,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` Target SLA: ${ctx.parsed.x} Jam`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: true, text: 'Durasi dalam Jam' },
                        grid: { color: '#f1f5f9' }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection