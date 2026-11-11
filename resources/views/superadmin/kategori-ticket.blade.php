@extends('components.b-master')

@section('title', 'Kategori Tiket')
@section('subtitle', 'Analisis dan Distribusi Kategori Layanan Tiket')

@section('content')
<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-5 lg:p-6">
    <div class="mx-auto w-full max-w-[1900px]">
        <!-- Header -->
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.15em] text-[#397b9d] shadow-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#397b9d]"></span>
                    <span>Helpdesk Analytics</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>Category Breakdown</span>
                </div>
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Kategori Layanan</h1>
                <p class="mt-1 text-xs text-[#84919a] sm:text-sm">Monitoring beban penanganan tiket dan rasio penyelesaian per kelompok layanan teknis.</p>
            </div>

            <!-- Hanya menampilkan jumlah kategori; tidak menambahkan tombol tambah kategori -->
            <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-[#dce4e9] bg-white px-3.5 py-2.5 text-xs font-bold text-[#496c82] shadow-sm">
                <i class="fas fa-folder-open text-[#397b9d]"></i>
                <span>{{ $kategoriData['total_categories'] ?? 0 }} Kategori Terdaftar</span>
            </div>
        </div>

        <!-- KPI Cards: seluruh angka berasal dari $kategoriData -->
        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 2xl:grid-cols-4">
            <!-- Kategori Aktif -->
            <section class="flex min-h-[112px] items-center justify-between rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_9px_22px_rgba(25,55,75,0.08)] sm:p-5">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Kategori Aktif</p>
                    <h2 class="mt-1 text-[30px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ $kategoriData['total_categories'] ?? 0 }}</h2>
                    <span class="mt-2 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-semibold text-[#47718a]">Kelompok Layanan</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#2f789e] text-base text-white shadow-sm"><i class="fas fa-layer-group"></i></span>
            </section>

            <!-- Total Aduan Masuk -->
            <section class="flex min-h-[112px] items-center justify-between rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_9px_22px_rgba(25,55,75,0.08)] sm:p-5">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Total Aduan Masuk</p>
                    <h2 class="mt-1 text-[30px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ $kategoriData['total_tickets'] ?? 0 }}</h2>
                    <span class="mt-2 inline-flex rounded-full bg-[#f0f3f5] px-2.5 py-1 text-[10px] font-semibold text-[#647783]">Akumulasi Seluruh Kategori</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#1d405c] text-base text-white shadow-sm"><i class="fas fa-ticket-alt"></i></span>
            </section>

            <!-- Aduan Terbanyak -->
            <section class="flex min-h-[112px] items-center justify-between rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_9px_22px_rgba(25,55,75,0.08)] sm:p-5">
                <div class="min-w-0 max-w-[75%]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Aduan Terbanyak</p>
                    <h2 class="mt-1 truncate text-base font-extrabold leading-tight text-[#293b49]" title="{{ $kategoriData['top_category'] ?? '-' }}">{{ $kategoriData['top_category'] ?? '-' }}</h2>
                    <span class="mt-2 inline-flex rounded-full bg-[#fbf4e7] px-2.5 py-1 text-[10px] font-semibold text-[#a87924]">Kategori Paling Sering</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#e7a83d] text-base text-white shadow-sm"><i class="fas fa-fire"></i></span>
            </section>

            <!-- Rata-rata Selesai -->
            <section class="flex min-h-[112px] items-center justify-between rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_9px_22px_rgba(25,55,75,0.08)] sm:p-5">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Rata-rata Selesai</p>
                    <h2 class="mt-1 text-[30px] font-extrabold leading-none tracking-tight text-[#23885d]">{{ $kategoriData['global_solve_rate'] ?? 0 }}%</h2>
                    <span class="mt-2 inline-flex rounded-full bg-[#eaf5ef] px-2.5 py-1 text-[10px] font-semibold text-[#38845e]">Tingkat Efektivitas</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#23885d] text-base text-white shadow-sm"><i class="fas fa-check"></i></span>
            </section>
        </div>

        <!-- Charts: Doughnut + grouped bar -->
        <div class="mb-5 grid grid-cols-1 gap-4 2xl:grid-cols-12">
            <!-- Doughnut -->
            <section class="rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-5 2xl:col-span-4">
                <div class="mb-2 flex items-start gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#edf4f8] text-sm text-[#397b9d]"><i class="fas fa-chart-pie"></i></span>
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49]">Proporsi Aduan Kategori</h3>
                        <p class="mt-0.5 text-[11px] text-[#84919a]">Persentase distribusi tiket berdasarkan kategori layanan.</p>
                    </div>
                </div>

                <div class="relative mx-auto my-3 h-[205px] w-full max-w-[250px]">
                    <canvas id="categoryDoughnutChart"></canvas>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 border-t border-[#edf1f4] pt-3">
                    @forelse(($kategoriData['tableSummary'] ?? []) as $item)
                        <div class="flex min-w-0 items-center justify-between gap-2 text-[10px]">
                            <div class="flex min-w-0 items-center gap-2">
                                <span class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $item['color'] ?? '#8aa5b5' }}"></span>
                                <span class="truncate text-[#647783]">{{ $item['name'] }}</span>
                            </div>
                            <span class="shrink-0 font-bold text-[#293b49]">{{ $item['total'] }}</span>
                        </div>
                    @empty
                        <p class="col-span-2 py-3 text-center text-[11px] italic text-[#9aa6ae]">Belum ada data kategori.</p>
                    @endforelse
                </div>
            </section>

            <!-- Grouped Bar Chart -->
            <section class="rounded-2xl border border-[#dce4e9] bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-5 2xl:col-span-8">
                <div class="mb-2 flex items-start gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#edf4f8] text-sm text-[#397b9d]"><i class="fas fa-chart-column"></i></span>
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49]">Status Tiket Tiap Kategori</h3>
                        <p class="mt-0.5 text-[11px] text-[#84919a]">Perbandingan jumlah tiket masuk, telah selesai, dan sedang diproses.</p>
                    </div>
                </div>

                <div class="relative mt-2 h-[235px] sm:h-[255px]">
                    <canvas id="categoryStatusBarChart"></canvas>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-x-5 gap-y-2 border-t border-[#edf1f4] pt-3 text-[10px] text-[#71808c]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-[#8db8ce]"></span>Total Masuk</span>
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-[#23885d]"></span>Telah Selesai</span>
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-[#4d93b8]"></span>Sedang Diproses</span>
                </div>
            </section>
        </div>

        <!-- Table -->
        <section class="overflow-hidden rounded-2xl border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-center gap-3 border-b border-[#edf1f4] px-4 py-4 sm:px-5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#edf4f8] text-sm text-[#397b9d]"><i class="fas fa-list"></i></span>
                <div>
                    <h3 class="text-sm font-bold text-[#293b49]">Tabel Rincian Kinerja per Kategori Layanan</h3>
                    <p class="mt-0.5 text-[10px] text-[#84919a]">Ringkasan jumlah tiket dan tingkat penyelesaian tiap kategori.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead class="border-b border-[#edf1f4] bg-[#f8fafb] text-[10px] font-bold uppercase tracking-[0.08em] text-[#87949d]">
                        <tr>
                            <th class="px-5 py-3.5">Nama Kategori Layanan</th>
                            <th class="px-5 py-3.5 text-center">Total Masuk</th>
                            <th class="px-5 py-3.5 text-center">Sedang Diproses</th>
                            <th class="px-5 py-3.5 text-center">Telah Selesai</th>
                            <th class="px-5 py-3.5 text-center">Tingkat Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1f4] text-xs">
                        @forelse(($kategoriData['tableSummary'] ?? []) as $row)
                            <tr class="transition-colors hover:bg-[#f8fbfd]">
                                <td class="px-5 py-3.5 font-semibold text-[#354957]">
                                    <div class="flex items-center gap-2.5">
                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $row['color'] ?? '#8aa5b5' }}"></span>
                                        <span>{{ $row['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold tabular-nums text-[#354957]">{{ $row['total'] }}</td>
                                <td class="px-5 py-3.5 text-center font-semibold tabular-nums text-[#397b9d]">{{ $row['process'] }}</td>
                                <td class="px-5 py-3.5 text-center font-semibold tabular-nums text-[#23885d]">{{ $row['resolved'] }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="mx-auto flex max-w-[150px] items-center gap-2">
                                        <div class="h-1.5 min-w-0 flex-1 overflow-hidden rounded-full bg-[#edf1f4]">
                                            <div class="h-full rounded-full bg-[#23885d] transition-all duration-700" style="width: {{ max(0, min(100, (float) ($row['solve_rate'] ?? 0))) }}%"></div>
                                        </div>
                                        <span class="w-10 shrink-0 text-right font-bold tabular-nums text-[#647783]">{{ $row['solve_rate'] ?? 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-xs italic text-[#9aa6ae]">Belum ada data tiket per kategori yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
(function () {
    function initKategoriTicketCharts() {
        if (!window.Chart) return;

        const doughnutCanvas = document.getElementById('categoryDoughnutChart');
        const barCanvas = document.getElementById('categoryStatusBarChart');
        if (!doughnutCanvas || !barCanvas) return;

        // Hindari canvas dibuat dua kali bila inisialisasi terpanggil ulang.
        [doughnutCanvas, barCanvas].forEach(canvas => {
            const existing = Chart.getChart(canvas);
            if (existing) existing.destroy();
        });

        // Kedua dataset berikut sepenuhnya berasal dari controller/database.
        const doughnutData = @json($kategoriData['doughnutChart'] ?? ['labels' => [], 'datasets' => []]);
        const comparisonData = @json($kategoriData['comparisonBarChart'] ?? ['labels' => [], 'datasets' => []]);

        const sourceDoughnutDatasets = Array.isArray(doughnutData.datasets) ? doughnutData.datasets : [];
        const doughnutTotal = sourceDoughnutDatasets.reduce((sum, dataset) => {
            const values = Array.isArray(dataset.data) ? dataset.data : [];
            return sum + values.reduce((subtotal, value) => subtotal + (Number(value) || 0), 0);
        }, 0);

        // Teks tengah menghitung total dari nilai dataset aktual, bukan angka contoh.
        const categoryCenterText = {
            id: 'categoryCenterText',
            afterDraw(chart) {
                const area = chart.chartArea;
                if (!area) return;

                const ctx = chart.ctx;
                const centerX = (area.left + area.right) / 2;
                const centerY = (area.top + area.bottom) / 2;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#293b49';
                ctx.font = '700 25px Inter, ui-sans-serif, system-ui, sans-serif';
                ctx.fillText(String(doughnutTotal), centerX, centerY - 5);
                ctx.fillStyle = '#84919a';
                ctx.font = '500 10px Inter, ui-sans-serif, system-ui, sans-serif';
                ctx.fillText('Total Tiket', centerX, centerY + 15);
                ctx.restore();
            }
        };

        if (sourceDoughnutDatasets.length) {
            new Chart(doughnutCanvas, {
                type: 'doughnut',
                data: {
                    ...doughnutData,
                    datasets: sourceDoughnutDatasets.map(dataset => ({
                        ...dataset,
                        borderWidth: 0,
                        borderColor: 'transparent',
                        borderRadius: 9,
                        spacing: 4,
                        hoverOffset: 3
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    rotation: -90,
                    animation: { duration: 1100, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1d405c',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label(context) {
                                    const datasetValues = context.dataset.data || [];
                                    const total = datasetValues.reduce((sum, value) => sum + (Number(value) || 0), 0);
                                    const value = Number(context.parsed) || 0;
                                    const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                    return ` ${context.label}: ${value} tiket (${percent}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [categoryCenterText]
            });
        }

        if (Array.isArray(comparisonData.datasets) && comparisonData.datasets.length) {
            const seriesColors = ['#8db8ce', '#23885d', '#4d93b8'];
            new Chart(barCanvas, {
                type: 'bar',
                data: {
                    ...comparisonData,
                    datasets: comparisonData.datasets.map((dataset, index) => ({
                        ...dataset,
                        backgroundColor: seriesColors[index % seriesColors.length],
                        borderColor: seriesColors[index % seriesColors.length],
                        borderWidth: 0,
                        borderRadius: 5,
                        borderSkipped: false,
                        maxBarThickness: 24
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1000, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#71808c', font: { family: 'Inter', size: 10 } },
                            categoryPercentage: 0.62,
                            barPercentage: 0.78
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#edf1f4', drawBorder: false },
                            border: { display: false },
                            ticks: { color: '#84919a', precision: 0, stepSize: 1, font: { family: 'Inter', size: 10 } }
                        }
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKategoriTicketCharts, { once: true });
    } else {
        initKategoriTicketCharts();
    }
})();
</script>
@endsection
