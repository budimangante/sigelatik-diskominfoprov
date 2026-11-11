@extends('components.b-master')

@section('title', 'Serangan Keamanan')
@section('subtitle', 'Monitoring insiden siber, kerentanan OPD, dan jenis ancaman keamanan')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] px-4 py-6 font-sans sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1900px] space-y-5">

        <!-- Header dashboard -->
        <header class="flex flex-col gap-3">
            <div class="inline-flex w-fit items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>CSIRT Monitoring</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Cyber Threat Intelligence</span>
            </div>
            <div>
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                    Pusat Keamanan Siber
                </h1>
                <p class="mt-1 max-w-3xl text-xs leading-relaxed text-[#71808c] sm:text-sm">
                    Visualisasi frekuensi serangan, ranking kerentanan OPD, serta distribusi vektor ancaman web dan jaringan.
                </p>
            </div>
        </header>

        <!-- 4 indikator metrik siber -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
            <!-- Total ancaman -->
            <article class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Total Ancaman Tercatat</p>
                    <h2 class="mt-2 text-[30px] font-extrabold leading-none tracking-tight text-[#263746]">{{ number_format($securityData['total_incidents'] ?? 0) }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#faecee] px-2.5 py-1 text-[10px] font-bold text-[#b64452]">Laporan Masuk</span>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#faecee] text-[#bd4654]">
                    <i class="fas fa-shield-virus text-base"></i>
                </span>
            </article>

            <!-- OPD paling sering ditarget -->
            <article class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div class="min-w-0 pr-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">OPD Paling Sering Ditarget</p>
                    <h2 class="mt-2 truncate text-sm font-extrabold text-[#263746]" title="{{ $securityData['most_vulnerable'] ?? '-' }}">
                        {{ $securityData['most_vulnerable'] ?? '-' }}
                    </h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#fbf3e7] px-2.5 py-1 text-[10px] font-bold text-[#a87928]">Target Terbanyak</span>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#fbf3e7] text-[#b78a39]">
                    <i class="fas fa-bullseye text-base"></i>
                </span>
            </article>

            <!-- Ancaman terbanyak -->
            <article class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div class="min-w-0 pr-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Ancaman Terbanyak</p>
                    <h2 class="mt-2 truncate text-sm font-extrabold text-[#263746]" title="{{ $securityData['dominant_attack'] ?? '-' }}">
                        {{ $securityData['dominant_attack'] ?? '-' }}
                    </h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">Vektor Utama</span>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]">
                    <i class="fas fa-biohazard text-base"></i>
                </span>
            </article>

            <!-- Mitigasi dan pemulihan: nilai mengikuti kode/controller saat ini -->
            <article class="flex min-h-[132px] items-start justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)]">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#84919a]">Mitigasi &amp; Pemulihan</p>
                    <h2 class="mt-2 text-[30px] font-extrabold leading-none tracking-tight text-[#23885d]">100%</h2>
                    <span class="mt-3 inline-flex rounded-full bg-[#eaf5ef] px-2.5 py-1 text-[10px] font-bold text-[#38845e]">SLA Tanggap Cepat</span>
                </div>
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf5ef] text-[#23885d]">
                    <i class="fas fa-user-shield text-base"></i>
                </span>
            </article>
        </section>

        <!-- Baris grafik 1: Top 10 OPD dan distribusi jenis serangan -->
        <section class="grid grid-cols-1 gap-5 2xl:grid-cols-3">
            <!-- Top 10 OPD -->
            <article class="flex min-w-0 flex-col rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6 2xl:col-span-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#faecee] text-[#bd4654]"><i class="fas fa-building"></i></span>
                        <div>
                            <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Top 10 OPD Paling Sering Terkena Serangan</h3>
                            <p class="mt-1 text-[11px] text-[#84919a]">Ranking perangkat daerah berdasarkan akumulasi insiden serangan siber.</p>
                        </div>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-[#faecee] px-3 py-1 text-[9px] font-extrabold uppercase tracking-wide text-[#b64452]">Prioritas Hardening</span>
                </div>

                <div class="relative mt-4 h-[260px] sm:h-[285px]">
                    <canvas id="topOpdChart"></canvas>
                </div>

                <div class="mt-3 flex flex-col gap-1 border-t border-[#edf1f4] pt-3 text-[10px] text-[#84919a] sm:flex-row sm:items-center sm:justify-between">
                    <span>Sumber: Rekap Laporan Helpdesk &amp; CSIRT Provinsi</span>
                    <span class="font-semibold text-[#647784]">Data otomatis teragregasi</span>
                </div>
            </article>

            <!-- Distribusi serangan -->
            <article class="flex min-w-0 flex-col rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-chart-pie"></i></span>
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Sebaran Jenis Serangan</h3>
                        <p class="mt-1 text-[11px] text-[#84919a]">Klasifikasi tipe serangan keamanan siber.</p>
                    </div>
                </div>

                <div class="relative mx-auto my-3 h-[205px] w-full max-w-[230px]">
                    <canvas id="attackTypeChart"></canvas>
                </div>

                <div class="max-h-40 space-y-2 overflow-y-auto border-t border-[#edf1f4] pt-3 pr-1">
                    @php
                        $badgeColors = ['#246d91', '#163f5b', '#dfa13d', '#5b9bb7', '#718ba0', '#465e75', '#9aafbd'];
                        $cIdx = 0;
                    @endphp
                    @forelse(($securityData['attack_types'] ?? []) as $typeName => $count)
                        <div class="flex items-center justify-between gap-3 text-[10px]">
                            <div class="flex min-w-0 items-center gap-2">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $badgeColors[$cIdx % count($badgeColors)] }}"></span>
                                <span class="truncate text-[#647784]">{{ $typeName }}</span>
                            </div>
                            <span class="shrink-0 font-bold tabular-nums text-[#293b49]">{{ $count }}</span>
                        </div>
                        @php $cIdx++; @endphp
                    @empty
                        <p class="py-3 text-center text-[11px] text-[#9aa7af]">Belum ada klasifikasi serangan.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <!-- Baris grafik 2: tren dan peringkat OPD -->
        <section class="grid grid-cols-1 gap-5 2xl:grid-cols-3">
            <!-- Tren serangan bulanan -->
            <article class="flex min-w-0 flex-col rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-chart-line"></i></span>
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Tren Serangan 6 Bulan Terakhir</h3>
                        <p class="mt-1 text-[11px] text-[#84919a]">Fluktuasi frekuensi serangan siber dari waktu ke waktu.</p>
                    </div>
                </div>

                <div class="relative my-4 h-[220px] sm:h-[240px]">
                    <canvas id="trendChart"></canvas>
                </div>

                <p class="mt-auto border-t border-[#edf1f4] pt-3 text-[10px] leading-relaxed text-[#84919a]">
                    Peningkatan sering terjadi menjelang periode update sistem atau libur panjang kedinasan.
                </p>
            </article>

            <!-- Tabel peringkat OPD -->
            <article class="flex min-w-0 flex-col overflow-hidden rounded-[18px] border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)] 2xl:col-span-2">
                <div class="flex items-center gap-3 border-b border-[#edf1f4] p-5 sm:px-6">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-list-ol"></i></span>
                    <div>
                        <h3 class="text-sm font-bold text-[#293b49] sm:text-base">Peringkat 10 OPD Paling Rentan</h3>
                        <p class="mt-1 text-[11px] text-[#84919a]">Rincian status mitigasi dan urgensi per perangkat daerah.</p>
                    </div>
                </div>

                <div class="flex-1 overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left text-xs">
                        <thead class="border-b border-[#edf1f4] bg-[#f8fafb] text-[9px] font-extrabold uppercase tracking-[0.1em] text-[#84919a]">
                            <tr>
                                <th class="w-14 px-5 py-3 text-center">Rank</th>
                                <th class="px-5 py-3">Perangkat Daerah / Dinas</th>
                                <th class="px-5 py-3 text-center">Total Serangan</th>
                                <th class="px-5 py-3 text-center">Urgensi Kritis</th>
                                <th class="px-5 py-3 text-center">Status Pemulihan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#edf1f4]">
                            @forelse(($securityData['top10_opd'] ?? []) as $idx => $opd)
                                <tr class="transition-colors hover:bg-[#f8fafb]">
                                    <td class="px-5 py-3 text-center font-bold">
                                        @if($idx == 0)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#bd4654] text-[10px] text-white">1</span>
                                        @elseif($idx == 1)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#d99a35] text-[10px] text-white">2</span>
                                        @elseif($idx == 2)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#397b9d] text-[10px] text-white">3</span>
                                        @else
                                            <span class="font-mono text-[#84919a]">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-[#344957]">{{ $opd['name'] }}</td>
                                    <td class="px-5 py-3 text-center font-bold tabular-nums text-[#bd4654]">{{ $opd['total'] }} kali</td>
                                    <td class="px-5 py-3 text-center">
                                        @if(($opd['critical'] ?? 0) > 0)
                                            <span class="inline-flex rounded-full border border-[#f2d4d8] bg-[#faecee] px-2.5 py-1 text-[9px] font-bold text-[#b64452]">{{ $opd['critical'] }} Kritis</span>
                                        @else
                                            <span class="text-[10px] text-[#a2adb4]">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-flex rounded-full border border-[#d7eadf] bg-[#eaf5ef] px-2.5 py-1 text-[9px] font-bold text-[#38845e]">{{ $opd['resolved'] }}/{{ $opd['total'] }} Pulih</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-[11px] italic text-[#9aa7af]">Belum ada data insiden serangan keamanan tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;

    const securityCharts = {
        topOpd: @json($securityData['topOpdChart'] ?? ['labels' => [], 'datasets' => []]),
        attackTypes: @json($securityData['attackTypeChart'] ?? ['labels' => [], 'datasets' => []]),
        trend: @json($securityData['trendChart'] ?? ['labels' => [], 'datasets' => []])
    };

    const colors = {
        navy: '#1d405c',
        blue: '#397b9d',
        red: '#bd4654',
        amber: '#d99a35',
        green: '#23885d',
        grid: '#edf1f4',
        text: '#84919a'
    };

    const font = { family: 'Inter, ui-sans-serif, system-ui, sans-serif', size: 10 };

    // Mencegah Chart.js membuat chart kedua pada canvas yang sama.
    function createSecurityChart(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !config.data) return null;

        const previousChart = Chart.getChart(canvas);
        if (previousChart) previousChart.destroy();

        return new Chart(canvas, config);
    }

    // Plugin angka total di tengah Doughnut.
    const doughnutCenterText = {
        id: 'securityDoughnutCenterText',
        afterDraw(chart) {
            if (chart.config.type !== 'doughnut' || !chart.chartArea) return;

            const total = (chart.data.datasets || []).reduce((sum, dataset) => {
                return sum + (dataset.data || []).reduce((subtotal, value) => subtotal + (Number(value) || 0), 0);
            }, 0);

            const { ctx, chartArea } = chart;
            const x = (chartArea.left + chartArea.right) / 2;
            const y = (chartArea.top + chartArea.bottom) / 2;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = colors.navy;
            ctx.font = '800 25px Inter, Arial, sans-serif';
            ctx.fillText(String(total), x, y - 3);
            ctx.fillStyle = colors.text;
            ctx.font = '500 9px Inter, Arial, sans-serif';
            ctx.fillText('Insiden', x, y + 17);
            ctx.restore();
        }
    };

    // Top 10 OPD: horizontal bar dengan warna berdasarkan peringkat.
    const topOpd = securityCharts.topOpd;
    const topOpdDatasets = (topOpd.datasets || []).map(dataset => {
        const values = dataset.data || [];
        const rankColors = values.map((_, index) => {
            if (index === 0) return colors.red;
            if (index === 1 || index === 2) return colors.amber;
            return colors.blue;
        });

        return {
            ...dataset,
            backgroundColor: rankColors,
            borderRadius: 7,
            borderSkipped: false,
            barThickness: 13,
            maxBarThickness: 16
        };
    });

    createSecurityChart('topOpdChart', {
        type: 'bar',
        data: { ...topOpd, datasets: topOpdDatasets },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 900, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: colors.navy,
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: font,
                    bodyFont: font,
                    callbacks: { label: context => ` ${context.parsed.x} insiden serangan tercatat` }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { color: colors.text, font, precision: 0, padding: 7 },
                    grid: { color: colors.grid, drawBorder: false },
                    border: { display: false }
                },
                y: {
                    ticks: { color: '#647784', font: { ...font, weight: '600' }, padding: 8 },
                    grid: { display: false },
                    border: { display: false }
                }
            }
        }
    });

    // Sebaran jenis serangan: rounded doughnut dengan total di tengah.
    const attackTypes = securityCharts.attackTypes;
    const attackPalette = ['#246d91', '#163f5b', '#dfa13d', '#5b9bb7', '#718ba0', '#465e75', '#9aafbd', '#c18b45'];
    const attackTypeDatasets = (attackTypes.datasets || []).map(dataset => ({
        ...dataset,
        backgroundColor: (dataset.data || []).map((_, index) => attackPalette[index % attackPalette.length]),
        borderWidth: 0,
        borderColor: 'transparent',
        borderRadius: 7,
        spacing: 4,
        hoverOffset: 2
    }));

    createSecurityChart('attackTypeChart', {
        type: 'doughnut',
        data: { ...attackTypes, datasets: attackTypeDatasets },
        plugins: [doughnutCenterText],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            rotation: -90,
            animation: { duration: 1100, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: colors.navy,
                    padding: 10,
                    cornerRadius: 8,
                    titleFont: font,
                    bodyFont: font,
                    callbacks: {
                        label(context) {
                            const total = context.dataset.data.reduce((sum, value) => sum + (Number(value) || 0), 0);
                            const value = Number(context.parsed) || 0;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                            return ` ${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Tren serangan 6 bulan terakhir.
    const trend = securityCharts.trend;
    const trendDatasets = (trend.datasets || []).map(dataset => ({
        ...dataset,
        borderColor: dataset.borderColor || colors.blue,
        backgroundColor: dataset.backgroundColor || 'rgba(57, 123, 157, 0.12)',
        borderWidth: 2.5,
        fill: dataset.fill !== undefined ? dataset.fill : true,
        tension: dataset.tension !== undefined ? dataset.tension : 0.35,
        pointRadius: 3,
        pointHoverRadius: 5,
        pointBackgroundColor: '#ffffff',
        pointBorderColor: dataset.borderColor || colors.blue,
        pointBorderWidth: 2
    }));

    createSecurityChart('trendChart', {
        type: 'line',
        data: { ...trend, datasets: trendDatasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1000, easing: 'easeOutQuart' },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: colors.navy, padding: 10, cornerRadius: 8, titleFont: font, bodyFont: font }
            },
            scales: {
                x: {
                    ticks: { color: colors.text, font, padding: 7 },
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: colors.text, font, precision: 0, padding: 7 },
                    grid: { color: colors.grid },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
