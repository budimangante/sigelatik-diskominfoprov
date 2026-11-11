@extends('components.b-master')

@section('title', 'Monitoring Tiket Layanan')
@section('subtitle', 'Monitoring performa penanganan tiket dan pelaporan kinerja per layanan TIK')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $serviceCategories = collect($monitoringData['reportingTable'] ?? [])->pluck('category')->filter()->unique()->values();
@endphp

<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>Service Monitoring</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Helpdesk Reporting</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Monitoring & Reporting Tiket Layanan</h1>
            <p class="mt-1 text-xs text-[#84929c]">Pemantauan terpadu beban kerja teknis, kepatuhan SLA, dan agregasi data aduan per jenis layanan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-[#dce4e9] bg-white px-4 py-2.5 text-xs font-bold text-[#526775] shadow-sm transition hover:bg-[#f8fbfc]"><i class="fas fa-print text-[#397b9d]"></i><span>Cetak Reporting</span></button>
            <span class="inline-flex items-center gap-2 rounded-xl border border-[#d7e6ee] bg-[#edf4f8] px-3.5 py-2.5 text-xs font-extrabold text-[#1d405c]"><i class="fas fa-cogs"></i>{{ $monitoringData['total_services'] ?? 0 }} Layanan Aktif</span>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Unit Layanan</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#1d405c]">{{ $monitoringData['total_services'] ?? 0 }}</h2><span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">Katalog Aktif</span></div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-lg text-[#347fa5]"><i class="fas fa-layer-group"></i></span>
        </div>
        <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Total Tiket Layanan</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#263746]">{{ $monitoringData['total_tickets'] ?? 0 }}</h2><span class="mt-3 inline-flex rounded-full bg-[#f0f3f5] px-2.5 py-1 text-[10px] font-bold text-[#667985]">Akumulasi Permintaan</span></div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#f0f3f5] text-lg text-[#617684]"><i class="fas fa-ticket-alt"></i></span>
        </div>
        <div class="flex min-h-[112px] min-w-0 items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="min-w-0"><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Layanan Terpopuler</p><h2 class="mt-2 truncate text-base font-extrabold leading-tight text-[#a87931]" title="{{ $monitoringData['top_service'] ?? '—' }}">{{ $monitoringData['top_service'] ?? '—' }}</h2><span class="mt-3 inline-flex rounded-full bg-[#fff8ec] px-2.5 py-1 text-[10px] font-bold text-[#a87931]">Paling Sering Masuk</span></div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#fff8ec] text-lg text-[#b58a45]"><i class="fas fa-chart-line"></i></span>
        </div>
        <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Rasio Selesai</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#25845c]">{{ $monitoringData['global_solve_rate'] ?? 0 }}%</h2><span class="mt-3 inline-flex rounded-full bg-[#edf7f1] px-2.5 py-1 text-[10px] font-bold text-[#25845c]">Tingkat Efektivitas</span></div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#edf7f1] text-lg text-[#25845c]"><i class="fas fa-check"></i></span>
        </div>
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <section class="flex min-w-0 flex-col rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] xl:col-span-2">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-column"></i></span>
                <div><h2 class="text-sm font-extrabold text-[#1d405c]">Monitoring Status Tiket Tiap Layanan</h2><p class="mt-0.5 text-[11px] text-[#8b99a3]">Perbandingan tiket masuk, diproses, dan telah selesai per layanan.</p></div>
            </div>
            <div class="relative my-4 h-[285px] w-full sm:h-[310px]"><canvas id="serviceBarChart"></canvas></div>
            <div id="serviceBarLegend" class="flex flex-wrap items-center justify-end gap-x-5 gap-y-2 border-t border-[#edf1f4] pt-3 text-[10px] font-semibold text-[#71808c]"></div>
        </section>

        <section class="flex min-w-0 flex-col rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-pie"></i></span>
                <div><h2 class="text-sm font-extrabold text-[#1d405c]">Proporsi Permintaan Layanan</h2><p class="mt-0.5 text-[11px] text-[#8b99a3]">Porsi volume tiket aduan berdasarkan jenis layanan.</p></div>
            </div>
            <div class="relative mx-auto my-3 h-[205px] w-full max-w-[230px]"><canvas id="serviceDoughnutChart"></canvas></div>
            <div class="mt-auto max-h-44 space-y-2 overflow-y-auto border-t border-[#edf1f4] pt-3 pr-1">
                @forelse($monitoringData['reportingTable'] ?? [] as $item)
                    <div class="flex items-center justify-between gap-3 text-[10px]">
                        <div class="flex min-w-0 items-center gap-2"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $item['color'] ?? '#347fa5' }}"></span><span class="truncate text-[#657985]">{{ $item['name'] }}</span></div>
                        <strong class="shrink-0 font-extrabold tabular-nums text-[#344957]">{{ $item['total'] }}</strong>
                    </div>
                @empty
                    <p class="py-3 text-center text-[10px] text-[#91a0a9]">Belum ada data layanan.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Reporting table with live category filter -->
    <section class="overflow-hidden rounded-[20px] border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="flex flex-col gap-3 border-b border-[#edf1f4] p-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-table"></i></span>
                <div><h2 class="text-sm font-extrabold text-[#1d405c]">Reporting Kinerja Tiket Layanan</h2><p class="mt-0.5 text-[11px] text-[#8b99a3]">Laporan agregasi tiket masuk, penyelesaian, dan target SLA per layanan.</p></div>
            </div>
            <label class="relative block w-full sm:w-[230px]">
                <span class="sr-only">Filter kategori layanan</span>
                <select id="serviceCategoryFilter" onchange="filterServiceTable()" class="w-full appearance-none rounded-xl border border-[#dce5eb] bg-[#f8fafc] px-3.5 py-2.5 pr-9 text-[11px] font-semibold text-[#526775] outline-none transition focus:border-[#6c9eb8] focus:bg-white focus:ring-2 focus:ring-[#e8f1f5]">
                    <option value="">Semua Kategori</option>
                    @foreach($serviceCategories as $category)
                        <option value="{{ strtolower($category) }}">{{ $category }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[9px] text-[#8b99a3]"></i>
            </label>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left text-xs">
                <thead class="border-b border-[#e7edf1] bg-[#f7fafc] text-[9px] font-extrabold uppercase tracking-wider text-[#81909a]">
                    <tr>
                        <th class="px-5 py-3.5">Nama Layanan</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5 text-center">Target SLA</th>
                        <th class="px-5 py-3.5 text-center">Masuk</th>
                        <th class="px-5 py-3.5 text-center">Diproses</th>
                        <th class="px-5 py-3.5 text-center">Selesai</th>
                        <th class="px-5 py-3.5">Tingkat Penyelesaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#edf1f4]" id="serviceReportingBody">
                    @forelse($monitoringData['reportingTable'] ?? [] as $row)
                        <tr class="service-report-row transition hover:bg-[#f8fbfc]" data-category="{{ strtolower($row['category'] ?? '') }}">
                            <td class="px-5 py-3.5 font-bold text-[#344957]"><span class="mr-2 inline-block h-2 w-2 rounded-full" style="background-color: {{ $row['color'] ?? '#347fa5' }}"></span>{{ $row['name'] }}</td>
                            <td class="px-5 py-3.5 text-[#71808c]">{{ $row['category'] }}</td>
                            <td class="px-5 py-3.5 text-center"><span class="rounded-md bg-[#eef4f7] px-2.5 py-1 text-[10px] font-extrabold tabular-nums text-[#397b9d]">{{ $row['sla_target'] }} jam</span></td>
                            <td class="px-5 py-3.5 text-center font-extrabold tabular-nums text-[#344957]">{{ $row['total'] }}</td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#eef5f9] px-2 py-1 font-bold tabular-nums text-[#347fa5]">{{ $row['process'] }}</span></td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#edf7f1] px-2 py-1 font-bold tabular-nums text-[#25845c]">{{ $row['resolved'] }}</span></td>
                            <td class="px-5 py-3.5">
                                <div class="flex min-w-[150px] items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[#edf1f4]"><div class="h-full rounded-full bg-[#25845c]" style="width: {{ max(0, min(100, (float) $row['solve_rate'])) }}%"></div></div>
                                    <span class="w-10 text-right text-[10px] font-extrabold tabular-nums text-[#586d7b]">{{ $row['solve_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-xs text-[#91a0a9]">Belum ada data tiket layanan yang tercatat dalam sistem.</td></tr>
                    @endforelse
                    <tr id="serviceFilterEmpty" class="hidden"><td colspan="7" class="px-5 py-10 text-center text-xs text-[#91a0a9]">Tidak ada layanan pada kategori yang dipilih.</td></tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const barData = {!! json_encode($monitoringData['serviceBarChart'] ?? ['labels' => [], 'datasets' => []]) !!};
    const doughnutData = {!! json_encode($monitoringData['serviceDoughnutChart'] ?? ['labels' => [], 'datasets' => []]) !!};
    const reportingRows = {!! json_encode($monitoringData['reportingTable'] ?? []) !!};
    const numberFormat = value => new Intl.NumberFormat('id-ID').format(Number(value) || 0);

    function buildLegend(containerId, datasets, colors) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        (datasets || []).forEach((dataset, index) => {
            const item = document.createElement('span');
            item.className = 'inline-flex items-center gap-1.5';
            const swatch = document.createElement('span');
            swatch.className = 'h-2.5 w-2.5 rounded-[3px]';
            swatch.style.backgroundColor = colors[index] || '#347fa5';
            const label = document.createElement('span');
            label.textContent = dataset.label || `Seri ${index + 1}`;
            item.append(swatch, label);
            container.appendChild(item);
        });
    }

    // Grouped bar chart; only colors are presentation, values/labels remain from controller.
    const barColors = ['#91bfd2', '#347fa5', '#25845c', '#1d405c'];
    const styledBarDatasets = (barData.datasets || []).map((dataset, index) => {
        const label = String(dataset.label || '').toLowerCase();
        let color = barColors[index % barColors.length];
        if (label.includes('masuk')) color = '#91bfd2';
        else if (label.includes('proses')) color = '#347fa5';
        else if (label.includes('selesai')) color = '#25845c';
        return { ...dataset, backgroundColor: color, borderColor: color, borderWidth: 0, borderRadius: 5, borderSkipped: false, maxBarThickness: 19 };
    });
    const barCanvas = document.getElementById('serviceBarChart');
    if (barCanvas) {
        new Chart(barCanvas, {
            type: 'bar',
            data: { labels: barData.labels || [], datasets: styledBarDatasets },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#173952', titleColor: '#d8e5ed', bodyColor: '#ffffff', padding: 10, usePointStyle: true }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9 }, color: '#81909a', maxRotation: 35, minRotation: 0 }, border: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 }, color: '#81909a' }, grid: { color: 'rgba(224, 232, 237, 0.75)', borderDash: [4, 5] }, border: { display: false } }
                }
            }
        });
    }
    buildLegend('serviceBarLegend', styledBarDatasets, styledBarDatasets.map(ds => ds.backgroundColor));

    // Doughnut count in the middle uses the same total-tickets value sent by the controller.
    const serviceTotal = {{ (int) ($monitoringData['total_tickets'] ?? 0) }};
    const reportingColors = reportingRows.map(row => row.color || '#347fa5');
    const styledDoughnutData = doughnutData;
    if (styledDoughnutData.datasets?.[0]) {
        styledDoughnutData.datasets[0].backgroundColor = (styledDoughnutData.labels || []).map((_, index) => reportingColors[index] || ['#1d405c', '#347fa5', '#5a9cba', '#83b5cb'][index % 4]);
        styledDoughnutData.datasets[0].borderWidth = 0;
        styledDoughnutData.datasets[0].borderRadius = 8;
        styledDoughnutData.datasets[0].spacing = 3;
        styledDoughnutData.datasets[0].hoverOffset = 5;
    }
    const serviceCenterPlugin = {
        id: 'serviceTotalCenter',
        afterDraw(chart) {
            const area = chart.chartArea;
            if (!area) return;
            const x = (area.left + area.right) / 2;
            const y = (area.top + area.bottom) / 2;
            const ctx = chart.ctx;
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#263746';
            ctx.font = '800 26px Inter, sans-serif';
            ctx.fillText(numberFormat(serviceTotal), x, y - 5);
            ctx.fillStyle = '#82919c';
            ctx.font = '600 10px Inter, sans-serif';
            ctx.fillText('Total Tiket', x, y + 17);
            ctx.restore();
        }
    };
    const doughnutCanvas = document.getElementById('serviceDoughnutChart');
    if (doughnutCanvas) {
        new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: styledDoughnutData,
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '73%', rotation: -90,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => {
                        const total = context.dataset.data.reduce((sum, value) => sum + (Number(value) || 0), 0);
                        const value = Number(context.parsed) || 0;
                        const percent = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                        return ` ${context.label}: ${numberFormat(value)} tiket (${percent}%)`;
                    } } }
                }
            },
            plugins: [serviceCenterPlugin]
        });
    }

    // Set category filter values from categories included in the current reporting data.
    window.filterServiceTable = function () {
        const selected = (document.getElementById('serviceCategoryFilter')?.value || '').toLocaleLowerCase('id-ID');
        const rows = Array.from(document.querySelectorAll('.service-report-row'));
        let visible = 0;
        rows.forEach(row => {
            const category = (row.dataset.category || '').toLocaleLowerCase('id-ID');
            const show = !selected || category === selected;
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        document.getElementById('serviceFilterEmpty')?.classList.toggle('hidden', visible > 0 || rows.length === 0);
    };
});
</script>
@endsection
