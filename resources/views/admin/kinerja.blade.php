@extends('components.b-master')

@section('title', 'Kinerja Dinas & Bidang')
@section('subtitle', 'Evaluasi Kinerja Pelayanan TIK per Dinas dan Bidang')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-6">
    <!-- Header and live summary -->
    <div class="flex flex-col gap-4 rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>Analisis Kinerja</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Perangkat Daerah</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Pusat Layanan & SLA</h1>
            <p class="mt-1 text-xs text-[#84929c]">Pemantauan tren penyelesaian dan durasi layanan seluruh perangkat daerah.</p>
        </div>
        <div class="grid grid-cols-1 gap-2 min-[420px]:grid-cols-3 lg:min-w-[420px]">
            <div class="rounded-xl border border-[#e3eaf0] bg-[#f8fafc] px-3.5 py-2.5 text-center">
                <p class="text-[9px] font-extrabold uppercase tracking-wider text-[#8b99a3]">Total Dinas</p>
                <p class="mt-1 text-lg font-extrabold tabular-nums text-[#1d405c]">{{ $kinerjaData['total_dinas'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-[#d9e8ef] bg-[#f0f7fa] px-3.5 py-2.5 text-center">
                <p class="text-[9px] font-extrabold uppercase tracking-wider text-[#4c85a1]">Total Aduan</p>
                <p class="mt-1 text-lg font-extrabold tabular-nums text-[#347fa5]">{{ $kinerjaData['total_tickets'] ?? 0 }}</p>
            </div>
            <div class="rounded-xl border border-[#dcece4] bg-[#f0f8f3] px-3.5 py-2.5 text-center">
                <p class="text-[9px] font-extrabold uppercase tracking-wider text-[#4d9270]">Rata-rata SLA</p>
                <p class="mt-1 text-lg font-extrabold tabular-nums text-[#25845c]">{{ $kinerjaData['global_avg_hours'] ?? 0 }}<span class="ml-1 text-[10px]">Jam</span></p>
            </div>
        </div>
    </div>

    <!-- Two dynamic trend charts -->
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <section class="flex min-w-0 flex-col rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-start justify-between gap-3 border-b border-[#edf1f4] pb-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#8b99a3]"><i class="fas fa-chart-line text-[#5a9cba]"></i>Indikator Kinerja</div>
                    <h2 class="mt-1.5 text-sm font-extrabold text-[#1d405c]">Indeks Kepatuhan & Efektivitas</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span id="kinerjaLatestValue" class="text-2xl font-extrabold tracking-tight text-[#263746]">—</span>
                        <span id="kinerjaChangeBadge" class="hidden items-center gap-1 rounded-full bg-[#eaf5ef] px-2 py-1 text-[10px] font-bold text-[#25845c]"></span>
                    </div>
                    <p id="kinerjaChangeCaption" class="mt-1 text-[10px] text-[#91a0a9]">Nilai terbaru dari seri grafik yang diterima.</p>
                </div>
                <span class="shrink-0 rounded-lg border border-[#e2eaf0] bg-[#f7fafc] px-2.5 py-1.5 text-[9px] font-extrabold uppercase tracking-wider text-[#66869a]">6 Bulan</span>
            </div>
            <div class="relative my-3 h-[250px] w-full sm:h-[270px]">
                <canvas id="stockKinerjaChart"></canvas>
            </div>
            <div id="stockKinerjaLegend" class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-[#edf1f4] pt-3 text-[10px] font-semibold text-[#6f818d]"></div>
        </section>

        <section class="flex min-w-0 flex-col rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-start justify-between gap-3 border-b border-[#edf1f4] pb-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#8b99a3]"><i class="fas fa-bolt text-[#5a9cba]"></i>Efisiensi Waktu</div>
                    <h2 class="mt-1.5 text-sm font-extrabold text-[#1d405c]">Kecepatan Resolusi SLA</h2>
                    <div class="mt-2 flex flex-wrap items-baseline gap-2">
                        <span id="durationLatestValue" class="text-2xl font-extrabold tracking-tight text-[#263746]">—</span>
                        <span class="text-xs font-bold text-[#70828e]">Jam</span>
                        <span id="durationCompareBadge" class="hidden items-center gap-1 rounded-full bg-[#eaf5ef] px-2 py-1 text-[10px] font-bold text-[#25845c]"></span>
                    </div>
                    <p id="durationCompareCaption" class="mt-1 text-[10px] text-[#91a0a9]">Durasi terbaru dari seri grafik yang diterima.</p>
                </div>
                <span class="shrink-0 rounded-lg border border-[#e2eaf0] bg-[#f7fafc] px-2.5 py-1.5 text-[9px] font-extrabold uppercase tracking-wider text-[#66869a]">Rata-rata</span>
            </div>
            <div class="relative my-3 h-[250px] w-full sm:h-[270px]">
                <canvas id="stockDurasiChart"></canvas>
            </div>
            <div id="stockDurasiLegend" class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-[#edf1f4] pt-3 text-[10px] font-semibold text-[#6f818d]"></div>
        </section>
    </div>

    <!-- Stacked horizontal comparison -->
    <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="flex flex-col gap-3 border-b border-[#edf1f4] pb-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-bars-progress"></i></span>
                <div>
                    <h2 class="text-sm font-extrabold text-[#1d405c]">Proporsi Penanganan Tiket per Dinas</h2>
                    <p class="mt-0.5 text-[11px] text-[#8b99a3]">Komposisi tiket selesai dibanding tiket yang masih diproses pada tiap dinas.</p>
                </div>
            </div>
            <div id="dinasStackedLegend" class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] font-semibold text-[#6f818d]"></div>
        </div>
        <div class="relative mt-3 h-[300px] w-full sm:h-[330px]">
            <canvas id="dinasStackedChart"></canvas>
        </div>
    </section>

    <!-- Table -->
    <section class="overflow-hidden rounded-[20px] border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="flex items-start gap-3 border-b border-[#edf1f4] p-5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-table"></i></span>
            <div>
                <h2 class="text-sm font-extrabold text-[#1d405c]">Tabel Kinerja Penanganan per Dinas</h2>
                <p class="mt-0.5 text-[11px] text-[#8b99a3]">Rincian status tiket yang diajukan masing-masing perangkat daerah.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs">
                <thead class="border-b border-[#e9eef2] bg-[#f7fafc] text-[10px] font-extrabold uppercase tracking-wider text-[#81909a]">
                    <tr>
                        <th class="px-5 py-3.5">Perangkat Daerah / Dinas</th>
                        <th class="px-5 py-3.5 text-center">Total Masuk</th>
                        <th class="px-5 py-3.5 text-center">Sedang Diproses</th>
                        <th class="px-5 py-3.5 text-center">Telah Selesai</th>
                        <th class="px-5 py-3.5 text-center">Tingkat Penyelesaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf1f4]">
                    @forelse($kinerjaData['dinasTable'] as $row)
                        <tr class="transition hover:bg-[#f8fbfc]">
                            <td class="px-5 py-3.5 font-bold text-[#344957]"><span class="mr-2 inline-block h-2 w-2 rounded-full bg-[#347fa5]"></span>{{ $row['name'] }}</td>
                            <td class="px-5 py-3.5 text-center font-bold tabular-nums text-[#344957]">{{ $row['total'] }}</td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#eef5f9] px-2 py-1 font-bold tabular-nums text-[#347fa5]">{{ $row['process'] }}</span></td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#edf7f1] px-2 py-1 font-bold tabular-nums text-[#25845c]">{{ $row['resolved'] }}</span></td>
                            <td class="px-5 py-3.5">
                                <div class="flex min-w-[150px] items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[#edf1f4]"><div class="h-full rounded-full bg-[#25845c]" style="width: {{ max(0, min(100, (float) $row['solve_rate'])) }}%"></div></div>
                                    <span class="w-10 text-right text-[11px] font-extrabold tabular-nums text-[#586d7b]">{{ $row['solve_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-[#91a0a9]">Belum ada data dinas yang tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const stockKinerjaData = {!! json_encode($kinerjaData['stockKinerja'] ?? ['labels' => [], 'datasets' => []]) !!};
    const stockDurasiData  = {!! json_encode($kinerjaData['stockDurasi'] ?? ['labels' => [], 'datasets' => []]) !!};
    const stackedData      = {!! json_encode($kinerjaData['dinasStackedChart'] ?? ['labels' => [], 'datasets' => []]) !!};
    const numberFormat = value => Number(value).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
    const valuesOf = dataset => Array.isArray(dataset?.data) ? dataset.data.filter(value => value !== null && value !== '').map(Number).filter(Number.isFinite) : [];
    const average = values => values.length ? values.reduce((sum, value) => sum + value, 0) / values.length : null;
    const gridColor = 'rgba(224, 232, 237, 0.75)';

    // Header metric: derive latest and change from the actual series, never hardcode demo numbers.
    const kinerjaSeries = (stockKinerjaData.datasets || []).map(ds => ({ ds, values: valuesOf(ds) })).filter(item => item.values.length);
    if (kinerjaSeries.length) {
        const latestAverage = average(kinerjaSeries.map(item => item.values[item.values.length - 1]));
        const initialAverage = average(kinerjaSeries.map(item => item.values[0]));
        document.getElementById('kinerjaLatestValue').textContent = numberFormat(latestAverage) + '%';
        const badge = document.getElementById('kinerjaChangeBadge');
        const delta = latestAverage - initialAverage;
        if (Number.isFinite(delta)) {
            badge.classList.remove('hidden');
            badge.classList.add('inline-flex');
            badge.classList.toggle('bg-[#eaf5ef]', delta >= 0);
            badge.classList.toggle('text-[#25845c]', delta >= 0);
            badge.classList.toggle('bg-[#fff0f1]', delta < 0);
            badge.classList.toggle('text-[#bd4250]', delta < 0);
            badge.innerHTML = `<i class="fas ${delta >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'}"></i>${delta >= 0 ? '+' : ''}${numberFormat(delta)}%`;
            document.getElementById('kinerjaChangeCaption').textContent = delta >= 0 ? 'Perubahan dibanding titik awal periode.' : 'Penurunan dibanding titik awal periode.';
        }
    }

    // Identify actual and target series by their real labels; use series values for summary.
    const durationDatasets = stockDurasiData.datasets || [];
    const actualDataset = durationDatasets.find(ds => /aktual|realisasi|durasi/i.test(String(ds.label || ''))) || durationDatasets[0];
    const targetDataset = durationDatasets.find(ds => /target|batas|limit/i.test(String(ds.label || '')) && ds !== actualDataset) || durationDatasets.find(ds => ds !== actualDataset);
    const actualValues = valuesOf(actualDataset);
    const targetValues = valuesOf(targetDataset);
    if (actualValues.length) {
        const latestActual = actualValues[actualValues.length - 1];
        document.getElementById('durationLatestValue').textContent = numberFormat(latestActual);
        if (targetValues.length) {
            const latestTarget = targetValues[targetValues.length - 1];
            const difference = latestTarget - latestActual;
            const badge = document.getElementById('durationCompareBadge');
            badge.classList.remove('hidden');
            badge.classList.add('inline-flex');
            badge.classList.toggle('bg-[#eaf5ef]', difference >= 0);
            badge.classList.toggle('text-[#25845c]', difference >= 0);
            badge.classList.toggle('bg-[#fff0f1]', difference < 0);
            badge.classList.toggle('text-[#bd4250]', difference < 0);
            badge.innerHTML = `<i class="fas ${difference >= 0 ? 'fa-bolt' : 'fa-triangle-exclamation'}"></i>${numberFormat(Math.abs(difference))} jam ${difference >= 0 ? 'di bawah target' : 'di atas target'}`;
            document.getElementById('durationCompareCaption').textContent = `Nilai pembanding diambil dari seri "${targetDataset.label || 'target SLA'}".`;
        }
    }

    function buildLegend(containerId, datasets, colors) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        (datasets || []).forEach((dataset, index) => {
            const item = document.createElement('span');
            item.className = 'inline-flex items-center gap-1.5';
            const swatch = document.createElement('span');
            swatch.className = 'h-2.5 w-2.5 rounded-full';
            swatch.style.backgroundColor = colors[index] || dataset.borderColor || '#347fa5';
            const label = document.createElement('span');
            label.textContent = dataset.label || `Seri ${index + 1}`;
            item.append(swatch, label);
            container.appendChild(item);
        });
    }

    // Chart 1: multi-series adherence trend.
    const kinerjaCanvas = document.getElementById('stockKinerjaChart');
    if (kinerjaCanvas) {
        const ctx = kinerjaCanvas.getContext('2d');
        const seriesColors = ['#1d405c', '#347fa5', '#5a9cba', '#83b5cb'];
        const styledDatasets = (stockKinerjaData.datasets || []).map((dataset, index) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            const color = seriesColors[index % seriesColors.length];
            gradient.addColorStop(0, index === 0 ? 'rgba(29, 64, 92, 0.18)' : 'rgba(52, 127, 165, 0.13)');
            gradient.addColorStop(1, 'rgba(52, 127, 165, 0.005)');
            return { ...dataset, borderColor: color, borderWidth: 2.2, backgroundColor: gradient, fill: true, tension: 0.34, pointRadius: 2.5, pointHoverRadius: 5, pointBackgroundColor: '#ffffff', pointBorderColor: color, pointBorderWidth: 1.8 };
        });
        new Chart(ctx, {
            type: 'line',
            data: { labels: stockKinerjaData.labels || [], datasets: styledDatasets },
            options: {
                responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#173952', titleColor: '#d8e5ed', bodyColor: '#ffffff', padding: 10, usePointStyle: true,
                        callbacks: { label: context => ` ${context.dataset.label || 'Kepatuhan'}: ${numberFormat(context.parsed.y)}%` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#8a99a3', maxRotation: 0 }, border: { display: false } },
                    y: { position: 'right', min: 0, max: 100, ticks: { stepSize: 10, callback: value => value + '%', font: { size: 10 }, color: '#8a99a3' }, grid: { color: gridColor, borderDash: [4, 5] }, border: { display: false } }
                }
            }
        });
        buildLegend('stockKinerjaLegend', styledDatasets, seriesColors);
    }

    // Chart 2: actual resolution line with an optional target/boundary series.
    const durasiCanvas = document.getElementById('stockDurasiChart');
    if (durasiCanvas) {
        const ctx = durasiCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(29, 64, 92, 0.18)');
        gradient.addColorStop(1, 'rgba(29, 64, 92, 0.005)');
        const styledDatasets = durationDatasets.map(dataset => {
            const isTarget = dataset === targetDataset;
            return {
                ...dataset,
                borderColor: isTarget ? '#c49a55' : '#1d405c',
                borderWidth: isTarget ? 1.8 : 2.4,
                borderDash: isTarget ? [6, 5] : [],
                backgroundColor: isTarget ? 'transparent' : gradient,
                fill: !isTarget,
                tension: 0.28,
                pointRadius: isTarget ? 0 : 2.5,
                pointHoverRadius: isTarget ? 0 : 5,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: isTarget ? '#c49a55' : '#1d405c',
                pointBorderWidth: 1.8
            };
        });
        const durationMaxValue = Math.max(0, ...styledDatasets.flatMap(ds => valuesOf(ds)));
        const durationAxisMax = Math.max(1, Math.ceil(durationMaxValue * 1.2));
        new Chart(ctx, {
            type: 'line',
            data: { labels: stockDurasiData.labels || [], datasets: styledDatasets },
            options: {
                responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#173952', titleColor: '#d8e5ed', bodyColor: '#ffffff', padding: 10, usePointStyle: true,
                        callbacks: { label: context => ` ${context.dataset.label || 'Durasi'}: ${numberFormat(context.parsed.y)} jam` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#8a99a3', maxRotation: 0 }, border: { display: false } },
                    y: { position: 'right', beginAtZero: true, max: durationAxisMax, ticks: { stepSize: Math.max(1, Math.ceil(durationAxisMax / 4)), callback: value => value + 'j', font: { size: 10 }, color: '#8a99a3' }, grid: { color: gridColor, borderDash: [4, 5] }, border: { display: false } }
                }
            }
        });
        buildLegend('stockDurasiLegend', styledDatasets, styledDatasets.map(ds => ds.borderColor));
    }

    // Chart 3: horizontal stacked workload by agency.
    const stackedCanvas = document.getElementById('dinasStackedChart');
    if (stackedCanvas) {
        const stackedDatasets = (stackedData.datasets || []).map((dataset, index) => {
            const label = String(dataset.label || '').toLowerCase();
            const color = label.includes('selesai') ? '#25845c' : (label.includes('proses') ? '#347fa5' : ['#25845c', '#347fa5', '#5a9cba'][index % 3]);
            return { ...dataset, backgroundColor: color, borderColor: color, borderWidth: 0, borderRadius: 7, barThickness: 13, maxBarThickness: 16 };
        });
        new Chart(stackedCanvas, {
            type: 'bar',
            data: { labels: stackedData.labels || [], datasets: stackedDatasets },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#173952', titleColor: '#d8e5ed', bodyColor: '#ffffff', padding: 10, usePointStyle: true }
                },
                scales: {
                    x: { stacked: true, beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: '#8a99a3' }, grid: { color: gridColor, borderDash: [4, 5] }, border: { display: false } },
                    y: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 }, color: '#617684' }, border: { display: false } }
                }
            }
        });
        buildLegend('dinasStackedLegend', stackedDatasets, stackedDatasets.map(ds => ds.backgroundColor));
    }
});
</script>
@endsection
