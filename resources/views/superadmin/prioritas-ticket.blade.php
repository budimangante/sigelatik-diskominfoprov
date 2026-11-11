@extends('components.b-master')

@section('title', 'Prioritas Tiket')
@section('subtitle', 'Daftar dan Analisis Prioritas Tiket')

@section('content')
<div class="min-h-screen space-y-6 bg-[#f3f6f8] p-4 font-sans sm:p-6">
    <div class="mx-auto w-full max-w-[1900px]">
        {{-- Header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>Helpdesk Analytics</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>Urgency Breakdown</span>
                </div>
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Prioritas Tiket</h1>
                <p class="mt-1 text-xs text-[#84919a] sm:text-sm">Visualisasi beban kerja tiket berdasarkan urgensi penanganan dan standar durasi SLA.</p>
            </div>

            {{-- Total tetap berasal dari controller; tombol tambah prioritas tidak ditampilkan --}}
            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-[#dce4e9] bg-white px-4 py-2.5 text-xs font-bold text-[#496c82] shadow-sm">
                <i class="fas fa-ticket-alt text-[#397b9d]"></i>
                <span>Total: {{ number_format($prioritasData['total'] ?? 0) }} Tiket</span>
            </div>
        </div>

        {{-- KPI Prioritas --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
            {{-- Kritis --}}
            <section class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Kritis (Critical)</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($prioritasData['counts']['critical'] ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#faecee] px-2.5 py-1 text-[10px] font-bold text-[#b64452]">SLA: 4 Jam</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-[#b83f4d] text-base text-white shadow-sm"><i class="fas fa-bolt"></i></span>
            </section>

            {{-- Tinggi --}}
            <section class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Tinggi (High)</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($prioritasData['counts']['high'] ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#fbf3e5] px-2.5 py-1 text-[10px] font-bold text-[#b47c20]">SLA: 8 Jam</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-[#bd8b35] text-base text-white shadow-sm"><i class="fas fa-exclamation-triangle"></i></span>
            </section>

            {{-- Sedang --}}
            <section class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Sedang (Medium)</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($prioritasData['counts']['medium'] ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">SLA: 24 Jam</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-[#347d9f] text-base text-white shadow-sm"><i class="fas fa-layer-group"></i></span>
            </section>

            {{-- Rendah --}}
            <section class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Rendah (Low)</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($prioritasData['counts']['low'] ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#eef1f4] px-2.5 py-1 text-[10px] font-bold text-[#647482]">SLA: 48 Jam</span>
                </div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[13px] bg-[#667987] text-base text-white shadow-sm"><i class="fas fa-clipboard-check"></i></span>
            </section>
        </div>

        {{-- Komposisi urgensi + tingkat penyelesaian --}}
        <div class="mb-6 grid grid-cols-1 gap-4 2xl:grid-cols-3">
            {{-- Donut --}}
            <section class="rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-pie"></i></span>
                        <h3 class="text-sm font-bold text-[#293b49]">Komposisi Urgensi</h3>
                    </div>
                    <p class="mt-1 text-[11px] text-[#84919a]">Persentase tiket masuk berdasarkan prioritas.</p>
                </div>

                <div class="relative mx-auto my-4 h-[220px] w-full max-w-[230px]">
                    <canvas id="priorityDoughnutChart" data-total="{{ $prioritasData['total'] ?? 0 }}"></canvas>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-2 border-t border-[#edf1f4] pt-4 text-[11px]">
                    <div class="flex items-center gap-2 text-[#667782]"><span class="h-2.5 w-2.5 rounded-full bg-[#bd4654]"></span><span>Kritis: <b class="text-[#293b49]">{{ number_format($prioritasData['counts']['critical'] ?? 0) }}</b></span></div>
                    <div class="flex items-center gap-2 text-[#667782]"><span class="h-2.5 w-2.5 rounded-full bg-[#d39a36]"></span><span>Tinggi: <b class="text-[#293b49]">{{ number_format($prioritasData['counts']['high'] ?? 0) }}</b></span></div>
                    <div class="flex items-center gap-2 text-[#667782]"><span class="h-2.5 w-2.5 rounded-full bg-[#347d9f]"></span><span>Sedang: <b class="text-[#293b49]">{{ number_format($prioritasData['counts']['medium'] ?? 0) }}</b></span></div>
                    <div class="flex items-center gap-2 text-[#667782]"><span class="h-2.5 w-2.5 rounded-full bg-[#7d8c98]"></span><span>Rendah: <b class="text-[#293b49]">{{ number_format($prioritasData['counts']['low'] ?? 0) }}</b></span></div>
                </div>
            </section>

            {{-- Bar total vs selesai --}}
            <section class="rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] 2xl:col-span-2 sm:p-6">
                <div class="mb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-column"></i></span>
                        <h3 class="text-sm font-bold text-[#293b49]">Tingkat Penyelesaian Tiket</h3>
                    </div>
                    <p class="mt-1 text-[11px] text-[#84919a]">Perbandingan total tiket masuk terhadap tiket yang berhasil diselesaikan.</p>
                </div>

                <div class="relative h-[245px] sm:h-[275px]">
                    <canvas id="priorityStatusBarChart"></canvas>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-x-5 gap-y-2 border-t border-[#edf1f4] pt-3 text-[11px] text-[#71808c]">
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-[#9bbfd0]"></span>Total Masuk</span>
                    <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-[#25875d]"></span>Telah Selesai</span>
                </div>
            </section>
        </div>

        {{-- Standar SLA --}}
        <section class="rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
            <div class="mb-4">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-stopwatch"></i></span>
                    <h3 class="text-sm font-bold text-[#293b49]">Standar Durasi Maksimal SLA (Jam)</h3>
                </div>
                <p class="mt-1 text-[11px] text-[#84919a]">Matriks batas waktu perbaikan tiket yang dijadikan acuan kepatuhan SLA.</p>
            </div>
            <div class="relative h-[230px] sm:h-[250px]">
                <canvas id="slaHorizontalBarChart"></canvas>
            </div>
        </section>
    </div>
</div>

<script>
(function () {
    function initPriorityTicketCharts() {
        if (!window.Chart) return;

        const doughnutCanvas = document.getElementById('priorityDoughnutChart');
        const comparisonCanvas = document.getElementById('priorityStatusBarChart');
        const slaCanvas = document.getElementById('slaHorizontalBarChart');

        if (!doughnutCanvas || !comparisonCanvas || !slaCanvas) return;

        // Seluruh angka dan label grafik tetap berasal dari controller/database.
        const doughnutSource = @json($prioritasData['doughnutChart'] ?? ['labels' => [], 'datasets' => []]);
        const comparisonSource = @json($prioritasData['comparisonBarChart'] ?? ['labels' => [], 'datasets' => []]);
        const slaSource = @json($prioritasData['slaMatrixChart'] ?? ['labels' => [], 'datasets' => []]);

        // Mencegah grafik dibuat dua kali pada canvas yang sama.
        [doughnutCanvas, comparisonCanvas, slaCanvas].forEach(canvas => {
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();
        });

        const palette = {
            critical: '#bd4654',
            high: '#d39a36',
            medium: '#347d9f',
            low: '#7d8c98',
            total: '#9bbfd0',
            resolved: '#25875d',
            grid: '#e9eff3',
            text: '#80909b'
        };

        const matchPriorityColor = (label, fallback) => {
            const value = String(label || '').toLowerCase();
            if (value.includes('kritis') || value.includes('critical')) return palette.critical;
            if (value.includes('tinggi') || value.includes('high')) return palette.high;
            if (value.includes('sedang') || value.includes('medium')) return palette.medium;
            if (value.includes('rendah') || value.includes('low')) return palette.low;
            return fallback;
        };

        // Teks total di tengah Donut dihitung dari nilai total controller.
        const priorityCenterLabel = {
            id: 'sigelatikPriorityCenterLabel',
            afterDraw(chart) {
                if (chart.canvas.id !== 'priorityDoughnutChart' || !chart.chartArea) return;

                const { ctx, chartArea } = chart;
                const centerX = (chartArea.left + chartArea.right) / 2;
                const centerY = (chartArea.top + chartArea.bottom) / 2;
                const total = chart.canvas.dataset.total || '0';

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#293b49';
                ctx.font = '700 27px Inter, ui-sans-serif, system-ui, sans-serif';
                ctx.fillText(total, centerX, centerY - 7);
                ctx.fillStyle = '#84919a';
                ctx.font = '500 10px Inter, ui-sans-serif, system-ui, sans-serif';
                ctx.fillText('Total tiket', centerX, centerY + 17);
                ctx.restore();
            }
        };

        if (!Chart.registry.plugins.get('sigelatikPriorityCenterLabel')) {
            Chart.register(priorityCenterLabel);
        }

        const doughnutData = {
            ...doughnutSource,
            datasets: (doughnutSource.datasets || []).map(dataset => ({
                ...dataset,
                backgroundColor: (dataset.data || []).map((_, index) => {
                    const label = (doughnutSource.labels || [])[index];
                    return matchPriorityColor(label, [palette.critical, palette.high, palette.medium, palette.low][index % 4]);
                }),
                borderWidth: 0,
                borderColor: 'transparent',
                borderRadius: 12,
                spacing: 5,
                hoverOffset: 2
            }))
        };

        new Chart(doughnutCanvas, {
            type: 'doughnut',
            data: doughnutData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                rotation: -90,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1d405c',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label(context) {
                                const values = context.dataset.data || [];
                                const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
                                const value = Number(context.parsed || 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                return ` ${context.label}: ${value} tiket (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        const comparisonData = {
            ...comparisonSource,
            datasets: (comparisonSource.datasets || []).map((dataset, index) => ({
                ...dataset,
                backgroundColor: index === 0 ? palette.total : palette.resolved,
                borderColor: 'transparent',
                borderRadius: 7,
                borderSkipped: false,
                maxBarThickness: 30
            }))
        };

        new Chart(comparisonCanvas, {
            type: 'bar',
            data: comparisonData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1100, easing: 'easeOutQuart' },
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: palette.text, font: { family: 'Inter', size: 10 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: palette.grid, drawBorder: false, borderDash: [4, 4] },
                        border: { display: false },
                        ticks: { color: palette.text, precision: 0, stepSize: 1, font: { family: 'Inter', size: 10 } }
                    }
                }
            }
        });

        const slaData = {
            ...slaSource,
            datasets: (slaSource.datasets || []).map(dataset => ({
                ...dataset,
                backgroundColor: (dataset.data || []).map((_, index) => {
                    const label = (slaSource.labels || [])[index];
                    return matchPriorityColor(label, [palette.critical, palette.high, palette.medium, palette.low][index % 4]);
                }),
                borderColor: 'transparent',
                borderRadius: 7,
                borderSkipped: false,
                barThickness: 15
            }))
        };

        new Chart(slaCanvas, {
            type: 'bar',
            data: slaData,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1d405c',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: { label: context => ` Target SLA: ${context.parsed.x} Jam` }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: { display: true, text: 'Durasi dalam Jam', color: palette.text, font: { family: 'Inter', size: 10 } },
                        grid: { color: palette.grid, drawBorder: false, borderDash: [4, 4] },
                        border: { display: false },
                        ticks: { color: palette.text, font: { family: 'Inter', size: 10 } }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#667782', font: { family: 'Inter', size: 10, weight: '600' } }
                    }
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPriorityTicketCharts, { once: true });
    } else {
        initPriorityTicketCharts();
    }
})();
</script>
@endsection
