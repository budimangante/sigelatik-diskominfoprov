@extends('components.b-master')

@section('title', 'Kategori Tiket')
@section('subtitle', 'Analisis dan Distribusi Kategori Layanan Tiket')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">HELPDESK ANALYTICS</span>
                <span class="text-slate-300">•</span>
                <span>CATEGORY BREAKDOWN</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Statistik Kategori Layanan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Monitoring beban penanganan tiket dan rasio penyelesaian per kelompok layanan teknis.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-2 bg-white rounded-xl border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
            <i class="fas fa-folder-open text-[#1a3668]"></i>
            <span>{{ $kategoriData['total_categories'] ?? 0 }} Kategori Terdaftar</span>
        </div>
    </div>

    <!-- 4 Indikator Metrik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Kategori -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kategori Aktif</p>
                <h3 class="text-2xl font-extrabold text-[#1a3668] mt-1">{{ $kategoriData['total_categories'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-1">Kelompok Layanan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-th-large"></i>
            </div>
        </div>

        <!-- Total Tiket Masuk -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Aduan Masuk</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $kategoriData['total_tickets'] ?? 0 }}</h3>
                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-1">Akumulasi Seluruh Kategori</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>

        <!-- Kategori Paling Banyak Masuk -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div class="max-w-[70%]">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aduan Terbanyak</p>
                <h3 class="text-sm font-extrabold text-amber-600 mt-1 truncate" title="{{ $kategoriData['top_category'] }}">
                    {{ $kategoriData['top_category'] ?? '-' }}
                </h3>
                <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md inline-block mt-1">Kategori Paling Sering</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-fire"></i>
            </div>
        </div>

        <!-- Tingkat Penyelesaian Global -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rata-rata Selesai</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $kategoriData['global_solve_rate'] ?? 0 }}%</h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md inline-block mt-1">Tingkat Efektivitas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row: Doughnut + Bar Comparison -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Doughnut Chart: Komposisi Tiket per Kategori -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-[#1a3668]"></i>
                    <span>Proporsi Aduan Kategori</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Persentase distribusi tiket berdasarkan kategori layanan.</p>
            </div>
            
            <div class="relative my-6 flex items-center justify-center">
                <div class="w-52 h-52">
                    <canvas id="categoryDoughnutChart"></canvas>
                </div>
            </div>

            <div class="space-y-1.5 text-xs pt-4 border-t border-slate-100 max-h-40 overflow-y-auto pr-1">
                @foreach($kategoriData['tableSummary'] as $item)
                    <div class="flex items-center justify-between py-0.5">
                        <div class="flex items-center gap-2 truncate max-w-[70%]">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                            <span class="text-slate-600 truncate text-[11px]">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-bold text-slate-800 text-[11px]">{{ $item['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bar Chart: Perbandingan Masuk, Selesai, Diproses -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs lg:col-span-2 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-[#1a3668]"></i>
                    <span>Status Tiket Tiap Kategori</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan jumlah tiket masuk, telah selesai, dan sedang diproses.</p>
            </div>

            <div class="my-4 h-72">
                <canvas id="categoryStatusBarChart"></canvas>
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
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>
                    <span>Sedang Diproses</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Ringkasan Rincian Kategori -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-list text-[#1a3668]"></i>
                <span>Tabel Rincian Kinerja per Kategori Layanan</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">NAMA KATEGORI LAYANAN</th>
                        <th class="px-6 py-4 text-center">TOTAL MASUK</th>
                        <th class="px-6 py-4 text-center">SEDANG DIPROSES</th>
                        <th class="px-6 py-4 text-center">TELAH SELESAI</th>
                        <th class="px-6 py-4 text-center">TINGKAT PENYELESAIAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($kategoriData['tableSummary'] as $row)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-bold text-slate-800 flex items-center gap-2.5">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $row['color'] }}"></span>
                                <span>{{ $row['name'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">{{ $row['total'] }}</td>
                            <td class="px-6 py-4 text-center text-blue-600 font-semibold">{{ $row['process'] }}</td>
                            <td class="px-6 py-4 text-center text-emerald-600 font-semibold">{{ $row['resolved'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <div class="w-24 bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $row['solve_rate'] }}%"></div>
                                    </div>
                                    <span class="font-bold text-[11px] font-mono text-slate-600">{{ $row['solve_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">
                                Belum ada data tiket per kategori yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const doughnutData   = {!! json_encode($kategoriData['doughnutChart']) !!};
        const comparisonData = {!! json_encode($kategoriData['comparisonBarChart']) !!};

        // 1. Doughnut Chart: Proporsi Kategori
        new Chart(document.getElementById('categoryDoughnutChart'), {
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

        // 2. Bar Chart: Status Tiket per Kategori
        new Chart(document.getElementById('categoryStatusBarChart'), {
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
    });
</script>
@endsection