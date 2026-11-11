@extends('components.b-master')

@section('title', 'Dasbor Pemantauan SLA')
@section('subtitle', 'Gambaran kepatuhan layanan TIK seluruh perangkat daerah')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-6">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>Pemerintah Provinsi</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Mode Pantau</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Dasbor Pemantauan SLA</h1>
            <p class="mt-1 text-xs text-[#71808c]">Gambaran kepatuhan layanan TIK seluruh perangkat daerah. Akses hanya-baca.</p>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <div class="flex min-h-[112px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#81909a]">Kepatuhan SLA</p>
                <div class="mt-2 text-[32px] font-extrabold leading-none tracking-tight text-[#263746]">{{ $pemprovData['kepatuhan_sla'] ?? 0 }}<span class="ml-0.5 text-xl">%</span></div>
                <span class="mt-3 inline-flex rounded-full bg-[#eaf5ef] px-2.5 py-1 text-[10px] font-bold text-[#25845c]">Kinerja operasional helpdesk</span>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#23845d] text-lg text-white"><i class="fas fa-check"></i></span>
        </div>

        <div class="flex min-h-[112px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#81909a]">Rata-rata Penyelesaian</p>
                <div class="mt-2 text-[32px] font-extrabold leading-none tracking-tight text-[#263746]">{{ $pemprovData['avg_resolution_hours'] ?? 0 }}<span class="ml-1 text-base font-bold">jam</span></div>
                <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">Target resolusi 4 jam</span>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#347fa5] text-lg text-white"><i class="far fa-clock"></i></span>
        </div>

        <div class="flex min-h-[112px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#81909a]">Tiket Berjalan</p>
                <div class="mt-2 text-[32px] font-extrabold leading-none tracking-tight text-[#263746]">{{ $pemprovData['berjalan'] ?? 0 }}</div>
                <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">Dalam batas SLA</span>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#5a9cba] text-lg text-white"><i class="fas fa-cog"></i></span>
        </div>

        <div class="flex min-h-[112px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#81909a]">SLA Terlampaui</p>
                <div class="mt-2 text-[32px] font-extrabold leading-none tracking-tight text-[#263746]">{{ $pemprovData['terlampaui'] ?? 0 }}</div>
                <span class="mt-3 inline-flex rounded-full bg-[#fff0f1] px-2.5 py-1 text-[10px] font-bold text-[#bd4250]">Perlu perhatian teknisi</span>
            </div>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#b33d50] text-lg text-white"><i class="fas fa-exclamation-triangle"></i></span>
        </div>
    </div>

    <!-- Trend + SLA donut. Donut tetap memakai konfigurasi database/controller yang sudah ada. -->
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <section class="min-w-0 rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] xl:col-span-2">
            <div class="mb-4 flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-line"></i></span>
                <div>
                    <h2 class="text-sm font-extrabold text-[#1d405c]">Tren Kepatuhan SLA</h2>
                    <p class="mt-0.5 text-[11px] text-[#8a98a2]">Persentase tiket selesai tepat waktu, 6 bulan terakhir.</p>
                </div>
            </div>
            <div class="relative h-[280px] w-full sm:h-[300px]">
                <canvas id="trenSlaChart"></canvas>
            </div>
        </section>

        <section class="flex min-w-0 flex-col rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-chart-pie"></i></span>
                <div>
                    <h2 class="text-sm font-extrabold text-[#1d405c]">Status SLA</h2>
                    <p class="mt-0.5 text-[11px] text-[#8a98a2]">Distribusi pencapaian seluruh tiket tahun berjalan.</p>
                </div>
            </div>
            <div class="relative mx-auto my-4 h-[205px] w-full max-w-[230px]">
                <canvas id="statusSlaChart"></canvas>
            </div>
            <div class="mt-auto space-y-3 border-t border-[#edf1f4] pt-4">
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2 text-[#71808c]"><span class="h-2.5 w-2.5 rounded-full bg-[#23845d]"></span>Tercapai</span>
                    <strong class="font-extrabold tabular-nums text-[#263746]">{{ $pemprovData['tercapai'] ?? 0 }}</strong>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2 text-[#71808c]"><span class="h-2.5 w-2.5 rounded-full bg-[#347fa5]"></span>Berjalan</span>
                    <strong class="font-extrabold tabular-nums text-[#263746]">{{ $pemprovData['berjalan'] ?? 0 }}</strong>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2 text-[#71808c]"><span class="h-2.5 w-2.5 rounded-full bg-[#b33d50]"></span>Terlampaui</span>
                    <strong class="font-extrabold tabular-nums text-[#263746]">{{ $pemprovData['terlampaui'] ?? 0 }}</strong>
                </div>
            </div>
        </section>
    </div>

    <!-- Compliance by division -->
    <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="mb-4 flex items-start gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-layer-group"></i></span>
            <div>
                <h2 class="text-sm font-extrabold text-[#1d405c]">Kepatuhan SLA per Bidang</h2>
                <p class="mt-0.5 text-[11px] text-[#8a98a2]">Persentase tiket selesai tepat waktu pada tiap bidang layanan.</p>
            </div>
        </div>
        <div class="relative h-[280px] w-full sm:h-[310px]">
            <canvas id="bidangSlaChart"></canvas>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "Inter, ui-sans-serif, system-ui, sans-serif";
    Chart.defaults.color = '#8796a1';

    const trendConfig  = {!! json_encode($pemprovData['trendChart'] ?? ['labels' => [], 'data' => []]) !!};
    const donutConfig  = {!! json_encode($pemprovData['donutChart'] ?? ['labels' => [], 'data' => []]) !!};
    const bidangConfig = {!! json_encode($pemprovData['bidangChart'] ?? ['labels' => [], 'data' => []]) !!};

    const gridColor = 'rgba(224, 232, 237, 0.75)';
    const statusValues = Array.isArray(donutConfig.data) ? donutConfig.data : [];
    const statusLabels = Array.isArray(donutConfig.labels) ? donutConfig.labels : [];
    const statusColors = statusLabels.map((label, index) => {
        const name = String(label || '').toLowerCase();
        if (name.includes('tercapai')) return '#23845d';
        if (name.includes('berjalan')) return '#347fa5';
        if (name.includes('terlampaui')) return '#b33d50';
        return ['#23845d', '#347fa5', '#b33d50', '#5a9cba'][index % 4];
    });

    // Plugin untuk angka total di tengah donut; total dihitung dari dataset yang dikirim controller.
    const slaCenterPlugin = {
        id: 'slaCenterText',
        afterDraw(chart) {
            const values = chart.data.datasets?.[0]?.data || [];
            const total = values.reduce((sum, value) => sum + (Number(value) || 0), 0);
            const area = chart.chartArea;
            if (!area) return;
            const x = (area.left + area.right) / 2;
            const y = (area.top + area.bottom) / 2;
            const ctx = chart.ctx;
            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#263746';
            ctx.font = '800 27px Inter, sans-serif';
            ctx.fillText(new Intl.NumberFormat('id-ID').format(total), x, y - 5);
            ctx.fillStyle = '#82919c';
            ctx.font = '600 10px Inter, sans-serif';
            ctx.fillText('Total Tiket SLA', x, y + 17);
            ctx.restore();
        }
    };

    const trendCanvas = document.getElementById('trenSlaChart');
    if (trendCanvas) {
        const ctx = trendCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(52, 127, 165, 0.20)');
        gradient.addColorStop(1, 'rgba(52, 127, 165, 0.015)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendConfig.labels || [],
                datasets: [{
                    label: 'Kepatuhan SLA',
                    data: trendConfig.data || [],
                    borderColor: '#347fa5',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.36,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#347fa5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        min: 0, max: 100,
                        ticks: { stepSize: 25, callback: value => value + '%', font: { size: 10 } },
                        border: { display: false },
                        grid: { color: gridColor, borderDash: [4, 5], drawTicks: false }
                    },
                    x: {
                        ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true },
                        border: { display: false },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const donutCanvas = document.getElementById('statusSlaChart');
    if (donutCanvas) {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderWidth: 0,
                    borderRadius: 8,
                    spacing: 3,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                rotation: -90,
                plugins: { legend: { display: false }, tooltip: { enabled: true } }
            },
            plugins: [slaCenterPlugin]
        });
    }

    const bidangCanvas = document.getElementById('bidangSlaChart');
    if (bidangCanvas) {
        const palette = ['#347fa5', '#1d405c', '#5a9cba', '#83b5cb', '#647887', '#27698e', '#a7c9d8'];
        new Chart(bidangCanvas, {
            type: 'bar',
            data: {
                labels: bidangConfig.labels || [],
                datasets: [{
                    label: 'Kepatuhan SLA',
                    data: bidangConfig.data || [],
                    backgroundColor: (bidangConfig.labels || []).map((_, index) => palette[index % palette.length]),
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.52,
                    categoryPercentage: 0.68
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => ` Kepatuhan: ${context.parsed.y}%` } }
                },
                scales: {
                    y: {
                        min: 0, max: 100,
                        ticks: { stepSize: 25, callback: value => value + '%', font: { size: 10 } },
                        border: { display: false },
                        grid: { color: gridColor, drawTicks: false }
                    },
                    x: {
                        ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: false },
                        border: { display: false },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endsection
