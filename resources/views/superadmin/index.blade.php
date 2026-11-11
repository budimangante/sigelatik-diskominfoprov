@extends('components.b-master')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] px-4 py-6 font-sans sm:px-6 lg:px-8 dashboard-page">
    <div class="mx-auto w-full max-w-[1900px]">
        <!-- Dashboard heading -->
        <div class="mb-7 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between dashboard-header">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>Super Admin</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>Diskominfo</span>
                </div>
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[30px] uppercase">Dashboard</h1>
                <p class="text-sm text-[#71808c]">Selamat datang di panel administrasi SIGELATIK.</p>
            </div>

            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-[#dce4e9] bg-white px-4 py-2.5 text-xs font-semibold text-[#496c82] shadow-sm">
                <i class="far fa-clock text-[#397b9d]"></i>
                <span>{{ now()->locale('id')->translatedFormat('l, j F Y') }}</span>
            </div>
        </div>

        <!-- KPI cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-3 dashboard-stagger dashboard-stats">
            <!-- Jumlah Tiket -->
            <a href="{{ route('daftar.ticket') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Jumlah Tiket</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($totalTickets ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">+{{ $thisMonthTickets ?? 0 }} bulan ini</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#2f789e] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="fas fa-ticket-alt"></i></span>
            </a>

            <!-- Perangkat Daerah -->
            <a href="{{ route('perangkat') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Perangkat Daerah</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($activeOpdCount ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">Aktif</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#1d405c] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="fas fa-building"></i></span>
            </a>

            <!-- Jumlah Bidang -->
            <a href="{{ route('bidang') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Jumlah Bidang</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($totalBidang ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">Terdaftar</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#5599b8] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="fas fa-layer-group"></i></span>
            </a>

            <!-- Jumlah Pegawai -->
            <a href="{{ route('pengguna') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Jumlah Pegawai</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($totalPegawai ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">SSO BKD</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#2f789e] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="fas fa-users"></i></span>
            </a>

            <!-- Teknisi Helpdesk -->
            <a href="{{ route('pengguna') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Teknisi Helpdesk</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($teknisiCount ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">Bertugas</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#1d405c] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="fas fa-cog"></i></span>
            </a>

            <!-- Prioritas Tiket -->
            <a href="{{ route('prioritas.ticket') }}" class="group flex min-h-[158px] items-start justify-between rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:border-[#b9d0dd] hover:shadow-[0_12px_28px_rgba(25,55,75,0.10)] sm:p-6">
                <div class="flex h-full flex-col items-start">
                    <p class="text-sm font-medium text-[#71808c]">Prioritas Tiket</p>
                    <h2 class="mt-1 text-[42px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($criticalTickets ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-3 py-1 text-xs font-semibold text-[#47718a]">Kritis</span>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-[15px] bg-[#5599b8] text-xl text-white shadow-sm transition group-hover:scale-105"><i class="far fa-flag"></i></span>
            </a>
        </div>

        <!-- Main charts -->
        <div class="mb-6 grid grid-cols-1 gap-4 2xl:grid-cols-2 dashboard-stagger dashboard-main-charts">
            <!-- Ticket monthly -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-[#293b49] sm:text-[17px]">Statistik Tiket Per Bulan</h3>
                        <p class="mt-1 text-sm text-[#84919a]">Jumlah tiket masuk setiap bulan</p>
                    </div>
                    <a href="{{ route('daftar.ticket') }}" class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#edf4f8] px-4 py-2.5 text-xs font-bold text-[#397b9d] transition hover:bg-[#dcecf4]">
                        Lihat <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                <div class="relative h-[250px] sm:h-[285px]">
                    <canvas id="tiketBulanChart"></canvas>
                </div>
            </section>

            <!-- SLA -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
                <div class="mb-4 flex flex-col justify-between gap-3 xl:flex-row xl:items-start">
                    <div>
                        <h3 class="text-base font-bold text-[#293b49] sm:text-[17px]">Statistik Rata-rata SLA</h3>
                        <p class="mt-1 text-sm text-[#84919a]">Durasi respon triage vs penanganan teknisi (menit)</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-semibold text-[#71808c]">
                        <span class="inline-flex items-center gap-2"><span class="h-1.5 w-5 rounded-full bg-[#347d9f]"></span>Respon (SLA 1)</span>
                        <span class="inline-flex items-center gap-2"><span class="h-1.5 w-5 rounded-full bg-[#dda43c]"></span>Penanganan (SLA 2)</span>
                    </div>
                </div>
                <div class="relative h-[250px] sm:h-[285px]">
                    <canvas id="averageSlaChart"></canvas>
                </div>
            </section>
        </div>

        <!-- Supporting analytics -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4 dashboard-stagger dashboard-bottom-charts">
            <!-- Category -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div class="mb-4 flex items-start justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Tiket per Kategori</h3>
                        <p class="mt-1 text-xs text-[#84919a]">Distribusi kategori</p>
                    </div>
                    <a href="{{ route('kategori.ticket') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#edf4f8] px-3 py-2 text-[11px] font-bold text-[#397b9d] transition hover:bg-[#dcecf4]">Lihat <i class="fas fa-chevron-right text-[9px]"></i></a>
                </div>
                <div class="relative h-[190px]"><canvas id="kategoriChart"></canvas></div>
            </section>

            <!-- Status -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div class="mb-4 flex items-start justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Tiket per Status</h3>
                        <p class="mt-1 text-xs text-[#84919a]">Status penanganan</p>
                    </div>
                    <a href="{{ route('daftar.ticket') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#edf4f8] px-3 py-2 text-[11px] font-bold text-[#397b9d] transition hover:bg-[#dcecf4]">Lihat <i class="fas fa-chevron-right text-[9px]"></i></a>
                </div>
                <div class="relative h-[190px]"><canvas id="statusChart"></canvas></div>
            </section>

            <!-- Unverified users -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div class="mb-2 flex items-start justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">User Belum Terverifikasi</h3>
                        <p class="mt-1 text-xs text-[#84919a]">Belum aktivasi email/SSO</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-[#faecee] px-2.5 py-1 text-[11px] font-bold text-[#b64452]">{{ $chartData['userVerification']['unverified']['count'] ?? 0 }} User</span>
                </div>
                <div class="relative mx-auto mt-1 h-[170px] max-w-[220px]">
                    <canvas id="unverifiedChart" class="relative z-10"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                        <span class="text-2xl font-extrabold text-[#1d405c]">{{ $chartData['userVerification']['unverified']['percentage'] ?? 0 }}%</span>
                        <span class="text-[10px] font-semibold text-[#bd4654] mt-0.5">Belum terverifikasi</span>
                    </div>
                </div>
            </section>

            <!-- Verified users -->
            <section class="rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div class="mb-2 flex items-start justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">User Terverifikasi</h3>
                        <p class="mt-1 text-xs text-[#84919a]">Terdaftar aktif di sistem</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-[#eaf5ef] px-2.5 py-1 text-[11px] font-bold text-[#38845e]">{{ $chartData['userVerification']['verified']['count'] ?? 0 }} User</span>
                </div>
                <div class="relative mx-auto mt-1 h-[170px] max-w-[220px]">
                    <canvas id="verifiedChart" class="relative z-10"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                        <span class="text-2xl font-extrabold text-[#1d405c]">{{ $chartData['userVerification']['verified']['percentage'] ?? 0 }}%</span>
                        <span class="text-[10px] font-semibold text-[#319267] mt-0.5">Terverifikasi SSO</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const charts = @json($chartData ?? []);
    const palette = {
        blue: '#347d9f',
        navy: '#1d405c',
        amber: '#dda43c',
        grid: '#e8eef2',
        text: '#82909a'
    };

    const chartFont = { family: "Inter, ui-sans-serif, system-ui, sans-serif", size: 11 };
    const commonScales = {
        ticks: { color: palette.text, font: chartFont, padding: 8 },
        grid: { color: palette.grid, borderDash: [4, 5], drawBorder: false }
    };

    const createChart = (id, config) => {
        const canvas = document.getElementById(id);
        if (canvas && window.Chart && config.data) {
            return new Chart(canvas, config);
        }
        return null;
    };

    // Plugin custom untuk background abu-abu melengkung di belakang bar
    const barBackgroundPlugin = {
        id: 'barBackgroundPlugin',
        beforeDatasetsDraw(chart, args, pluginOptions) {
            const { ctx, chartArea: { left, right }, scales: { y } } = chart;
            const datasetMeta = chart.getDatasetMeta(0);
            if (!datasetMeta.hidden && datasetMeta.type === 'bar') {
                ctx.save();
                ctx.fillStyle = '#f1f5f9'; // Warna abu-abu background seperti figma
                datasetMeta.data.forEach((bar) => {
                    const yPos = bar.y - bar.height / 2;
                    ctx.beginPath();
                    ctx.roundRect(left, yPos, right - left, bar.height, bar.height / 2);
                    ctx.fill();
                });
                ctx.restore();
            }
        }
    };

    // Ticket trend
    const monthly = charts.ticketMonthly || { labels: [], datasets: [] };
    const monthlyDatasets = (monthly.datasets || []).map(dataset => ({
        ...dataset,
        borderColor: dataset.borderColor || palette.blue,
        backgroundColor: dataset.backgroundColor || 'rgba(52, 125, 159, 0.13)',
        borderWidth: dataset.borderWidth || 3,
        fill: dataset.fill !== undefined ? dataset.fill : true,
        tension: dataset.tension !== undefined ? dataset.tension : 0.35,
        pointRadius: dataset.pointRadius !== undefined ? dataset.pointRadius : 4,
        pointHoverRadius: 6,
        pointBackgroundColor: dataset.pointBackgroundColor || '#ffffff',
        pointBorderColor: dataset.pointBorderColor || dataset.borderColor || palette.blue,
        pointBorderWidth: 2
    }));

    createChart('tiketBulanChart', {
        type: 'line',
        data: { ...monthly, datasets: monthlyDatasets },
        options: {
            animation: { duration: 1400, easing: 'easeOutQuart', delay: 450 },
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1d405c', padding: 11, cornerRadius: 9, titleFont: chartFont, bodyFont: chartFont }
            },
            scales: {
                y: { ...commonScales, beginAtZero: true, grid: { ...commonScales.grid, drawTicks: false } },
                x: { ...commonScales, grid: { display: false }, ticks: { ...commonScales.ticks, maxRotation: 0 } }
            }
        }
    });

    // Average SLA
    const sla = charts.averageSla || { labels: [], datasets: [] };
    const slaDatasets = (sla.datasets || []).map((dataset, index) => {
        const lineColor = index === 1 ? palette.amber : palette.blue;
        return {
            ...dataset,
            borderColor: dataset.borderColor || lineColor,
            backgroundColor: dataset.backgroundColor || lineColor,
            borderWidth: dataset.borderWidth || 3,
            tension: dataset.tension !== undefined ? dataset.tension : 0.25,
            fill: false,
            pointRadius: dataset.pointRadius !== undefined ? dataset.pointRadius : 4,
            pointHoverRadius: 6,
            pointBackgroundColor: dataset.pointBackgroundColor || '#ffffff',
            pointBorderColor: dataset.pointBorderColor || dataset.borderColor || lineColor,
            pointBorderWidth: 2
        };
    });

    createChart('averageSlaChart', {
        type: 'line',
        data: { ...sla, datasets: slaDatasets },
        options: {
            animation: { duration: 1400, easing: 'easeOutQuart', delay: 450 },
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1d405c', padding: 11, cornerRadius: 9,
                    titleFont: chartFont, bodyFont: chartFont,
                    callbacks: { label: context => context.dataset.label + ': ' + context.parsed.y + ' Menit' }
                }
            },
            scales: {
                y: {
                    ...commonScales,
                    beginAtZero: true,
                    ticks: { ...commonScales.ticks, callback: value => value + ' mnt' }
                },
                x: { ...commonScales, grid: { display: false }, ticks: { ...commonScales.ticks, maxRotation: 0 } }
            }
        }
    });

    // Ticket category
    const category = charts.ticketCategory || { labels: [], datasets: [] };
    const catColors = ['#347d9f', '#1d405c', '#5599b8', '#88b5c9']; // Variasi warna biru
    createChart('kategoriChart', {
        type: 'bar',
        data: {
            ...category,
            datasets: (category.datasets || []).map(dataset => ({
                ...dataset,
                backgroundColor: dataset.data.map((_, i) => catColors[i % catColors.length]),
                borderRadius: 20,
                borderSkipped: false, // Bikin ujungnya bulat luar dalam
                barThickness: 15
            }))
        },
        plugins: [barBackgroundPlugin], // Tambah background
        options: {
            animation: { duration: 1300, easing: 'easeOutQuart', delay: 850 },
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 } },
            scales: {
                x: { display: false }, // Hilangkan X axis angka dan garis bawah
                y: { grid: { display: false }, border: { display: false }, ticks: { color: '#667782', font: { size: 11, weight: '600' }, padding: 10 } }
            }
        }
    });

    // Ticket status
    const status = charts.ticketStatus || { labels: [], datasets: [] };
    const statusColors = ['#347d9f', '#1d405c', '#dda43c', '#319267', '#84919a']; // Sesuai warna status
    createChart('statusChart', {
        type: 'bar',
        data: {
            ...status,
            datasets: (status.datasets || []).map(dataset => ({
                ...dataset,
                backgroundColor: dataset.data.map((_, i) => statusColors[i % statusColors.length]),
                borderRadius: 20,
                borderSkipped: false,
                barThickness: 15
            }))
        },
        plugins: [barBackgroundPlugin],
        options: {
            animation: { duration: 1300, easing: 'easeOutQuart', delay: 850 },
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 } },
            scales: {
                x: { display: false },
                y: { grid: { display: false }, border: { display: false }, ticks: { color: '#667782', font: { size: 11, weight: '600' }, padding: 10 } }
            }
        }
    });

    // Verification donuts
    const unverified = charts.userVerification && charts.userVerification.unverified
        ? charts.userVerification.unverified.chart : { labels: [], datasets: [{ data: [] }] };

    if (unverified.datasets && unverified.datasets.length > 0) {
        unverified.datasets[0].backgroundColor = ['#bd4654', '#faecee'];
        unverified.datasets[0].borderWidth = 0;
        unverified.datasets[0].borderRadius = 20; // Ujung donut bulat
    }

    createChart('unverifiedChart', {
        type: 'doughnut',
        data: unverified,
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 } }
        }
    });

    const verified = charts.userVerification && charts.userVerification.verified
        ? charts.userVerification.verified.chart : { labels: [], datasets: [{ data: [] }] };

    if (verified.datasets && verified.datasets.length > 0) {
        verified.datasets[0].backgroundColor = ['#319267', '#eaf5ef'];
        verified.datasets[0].borderWidth = 0;
        verified.datasets[0].borderRadius = 20;
    }

    createChart('verifiedChart', {
        type: 'doughnut',
        data: verified,
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1d405c', padding: 10, cornerRadius: 8 } }
        }
    });
});
</script>
@endsection