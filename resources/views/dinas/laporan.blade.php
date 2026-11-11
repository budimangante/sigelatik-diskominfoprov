@extends('components.b-master')

@section('title', 'Laporan Kinerja Bidang & Teknisi')
@section('subtitle', 'Laporan kepatuhan SLA per bidang layanan dan efisiensi teknisi penangan')

@section('content')
<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-5 lg:p-6">

    <!-- Header Section + Dropdown Unduh Laporan -->
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>EXECUTIVE REPORT</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>BIDANG & TEKNISI</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Laporan Kinerja Bidang & Teknisi
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitoring volume aduan bidang, kepatuhan batas waktu SLA, dan kecepatan penyelesaian oleh teknisi.
            </p>
        </div>

        <!-- Tombol Aksi Unduh -->
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportMenu()" class="inline-flex items-center gap-2.5 rounded-xl bg-[#2f789e] px-5 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-[#245f7f] cursor-pointer">
                <i class="fas fa-file-arrow-down text-sm"></i>
                <span>Unduh Laporan</span>
                <i class="fas fa-chevron-down text-[10px] ml-1 opacity-70"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="exportDropdownMenu" class="hidden absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white py-1.5 shadow-xl z-50 text-xs font-medium text-slate-700">
                <button onclick="exportToCSV()" class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2.5 text-emerald-600 font-semibold cursor-pointer">
                    <i class="fas fa-file-excel w-4 text-center"></i>
                    <span>Ekspor Excel / CSV</span>
                </button>
                <button onclick="printLaporanPDF()" class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center gap-2.5 text-rose-600 font-semibold cursor-pointer">
                    <i class="fas fa-file-pdf w-4 text-center"></i>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Indikator Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="min-h-[158px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex items-start justify-between gap-3 sm:p-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Bidang Layanan</p>
                <h3 class="mt-2 text-[36px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ $laporanData['total_bidang'] }}</h3>
                <span class="mt-4 inline-flex rounded-full bg-[#edf4f8] px-3 py-1.5 text-[11px] font-semibold text-[#397b9d]">Sektor Penanganan</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#2f789e] text-lg text-white shadow-sm">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <div class="min-h-[158px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex items-start justify-between gap-3 sm:p-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Teknisi Aktif</p>
                <h3 class="mt-2 text-[36px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ $laporanData['total_teknisi'] }}</h3>
                <span class="mt-4 inline-flex rounded-full bg-[#edf1f7] px-3 py-1.5 text-[11px] font-semibold text-[#1d405c]">Personel Penangan</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#1d405c] text-lg text-white shadow-sm">
                <i class="fas fa-user-gear"></i>
            </div>
        </div>

        <div class="min-h-[158px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex items-start justify-between gap-3 sm:p-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Rata-rata Waktu Selesai</p>
                <h3 class="mt-2 text-[36px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ $laporanData['global_avg_duration'] }}<span class="ml-1 text-xs font-semibold text-slate-500">Jam</span></h3>
                <span class="mt-4 inline-flex rounded-full bg-[#eaf5ef] px-3 py-1.5 text-[11px] font-semibold text-[#23885d]">Efisiensi Teknisi</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5599b8] text-lg text-white shadow-sm">
                <i class="fas fa-stopwatch"></i>
            </div>
        </div>

        <div class="min-h-[158px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex items-start justify-between gap-3 sm:p-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Tingkat Penyelesaian</p>
                <h3 class="mt-2 text-[36px] font-extrabold leading-none tracking-tight text-[#23885d]">{{ $laporanData['global_solve_rate'] }}%</h3>
                <span class="mt-4 inline-flex rounded-full bg-[#eaf5ef] px-3 py-1.5 text-[11px] font-semibold text-[#23885d]">Tiket Dituntaskan</span>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#23885d] text-lg text-white shadow-sm">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- 2 Charts Berdampingan (Beban Bidang & Kecepatan Teknisi) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Beban & Resolusi per Bidang -->
        <div class="min-w-0 rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex flex-col justify-between sm:p-6">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="flex items-center gap-3 text-sm font-extrabold text-[#293b49]">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-chart-column"></i></span>
                        <span>Beban Tiket per Bidang Layanan</span>
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">Perbandingan jumlah tiket masuk vs tiket yang berhasil diselesaikan.</p>
                </div>
            </div>

            <div class="relative my-4 h-[280px] w-full">
                <canvas id="bidangChart"></canvas>
            </div>

            <div class="flex items-center justify-center gap-6 border-t border-slate-100 pt-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-slate-500">
                    <span class="h-2.5 w-2.5 rounded bg-[#8dbbd0]"></span> Total Masuk
                </span>
                <span class="flex items-center gap-1.5 text-emerald-600">
                    <span class="h-2.5 w-2.5 rounded bg-[#2d9565]"></span> Tiket Selesai
                </span>
            </div>
        </div>

        <!-- Chart 2: Kecepatan Penanganan per Teknisi -->
        <div class="min-w-0 rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] flex flex-col justify-between sm:p-6">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="flex items-center gap-3 text-sm font-extrabold text-[#293b49]">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-bolt"></i></span>
                        <span>Kecepatan Penyelesaian per Teknisi</span>
                    </h3>
                    <p class="mt-1 text-xs text-slate-500">Rata-rata durasi kerja penanganan tiket hingga tuntas (Jam).</p>
                </div>
            </div>

            <div class="relative my-4 h-[280px] w-full">
                <canvas id="teknisiChart"></canvas>
            </div>

            <div class="border-t border-slate-100 pt-4 text-center text-[11px] font-medium text-slate-400">
                Semakin pendek bar durasi = performa teknisi semakin responsif
            </div>
        </div>

    </div>

    <!-- Area Print Laporan: Tabel Bidang & Teknisi -->
    <div id="printArea" class="space-y-6">

        <!-- Tabel 1: Rekapitulasi per Bidang Layanan -->
        <div class="overflow-hidden rounded-[22px] border border-[#dce4e9] bg-white shadow-[0_4px_16px_rgba(25,55,75,0.045)]">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-white p-5 sm:p-6">
                <h3 class="flex items-center gap-3 text-sm font-extrabold text-[#293b49]">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-table"></i></span>
                    <span>Tabel Kinerja per Bidang Layanan</span>
                </h3>
                <span class="text-[11px] font-semibold text-slate-400">Target SLA vs Realisasi</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left whitespace-nowrap text-xs export-table">
                    <thead class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500 bg-[#f7f9fb] border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">BIDANG LAYANAN</th>
                            <th class="px-4 py-3 text-center">TOTAL TIKET</th>
                            <th class="px-4 py-3 text-center">DIPROSES</th>
                            <th class="px-4 py-3 text-center">SELESAI</th>
                            <th class="px-4 py-3 text-center">TARGET SLA</th>
                            <th class="px-4 py-3 text-center">REALISASI DURASI</th>
                            <th class="px-4 py-3 text-center">KEPATUHAN SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laporanData['bidangTable'] as $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3 font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#1a3668]"></span>
                                    <span>{{ $b['name'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-700 font-mono">{{ $b['total'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600 font-mono">{{ $b['process'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-600 font-mono">{{ $b['resolved'] }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-slate-600 bg-slate-50/60">{{ $b['target_sla'] }} Jam</td>
                                <td class="px-4 py-3 text-center font-mono font-black text-[#1a3668] bg-blue-50/20">{{ $b['avg_duration'] }} Jam</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $b['sla_rate'] >= 90 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $b['sla_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400 italic">Belum ada data bidang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 2: Rekapitulasi per Teknisi Penangan -->
        <div class="overflow-hidden rounded-[22px] border border-[#dce4e9] bg-white shadow-[0_4px_16px_rgba(25,55,75,0.045)]">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-white p-5 sm:p-6">
                <h3 class="flex items-center gap-3 text-sm font-extrabold text-[#293b49]">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-users-gear"></i></span>
                    <span>Tabel Kinerja & Kecepatan Teknisi</span>
                </h3>
                <span class="text-[10px] text-slate-400">Efisiensi Kerja Personel</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table id="laporanTeknisiTable" class="w-full text-left whitespace-nowrap text-xs export-table">
                    <thead class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500 bg-[#f7f9fb] border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">NAMA TEKNISI</th>
                            <th class="px-4 py-3 text-center">TOTAL TIKET DITANGANI</th>
                            <th class="px-4 py-3 text-center">SEDANG DIPROSES</th>
                            <th class="px-4 py-3 text-center">BERHASIL SELESAI</th>
                            <th class="px-4 py-3 text-center bg-emerald-50/40 text-emerald-900">RATA-RATA WAKTU PENYELESAIAN</th>
                            <th class="px-4 py-3 text-center">KEPATUHAN SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laporanData['teknisiTable'] as $tek)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-3 font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                    <span>{{ $tek['name'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-slate-700 font-mono">{{ $tek['total'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-blue-600 font-mono">{{ $tek['process'] }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-600 font-mono">{{ $tek['resolved'] }}</td>
                                <td class="px-4 py-3 text-center font-mono font-black text-emerald-700 bg-emerald-50/30">
                                    {{ $tek['avg_duration'] }} Jam
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $tek['sla_rate'] >= 90 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ $tek['sla_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400 italic">Belum ada data teknisi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-slate-100 bg-[#f7f9fb] p-4 text-right text-[11px] text-slate-400">
                Laporan digenerate otomatis: {{ now()->translatedFormat('d F Y H:i') }} WITA
            </div>
        </div>

    </div>

</div>

<!-- Inisialisasi grafik dan fungsi unduh laporan -->
<script>
(function () {
    function initDinasReportCharts() {
        if (!window.Chart) return;

        const bidangConfig = @json($laporanData['bidangChart'] ?? ['labels' => [], 'datasets' => []]);
        const teknisiConfig = @json($laporanData['teknisiChart'] ?? ['labels' => [], 'datasets' => []]);

        // Data tetap dari controller; bagian ini hanya mengatur tampilan dataset.
        const bidangData = {
            ...bidangConfig,
            datasets: (bidangConfig.datasets || []).map((dataset, index) => ({
                ...dataset,
                backgroundColor: index === 0 ? '#8dbbd0' : '#2d9565',
                borderColor: index === 0 ? '#8dbbd0' : '#2d9565',
                borderRadius: 8,
                borderSkipped: false,
                maxBarThickness: 30
            }))
        };

        const teknisiData = {
            ...teknisiConfig,
            datasets: (teknisiConfig.datasets || []).map(dataset => ({
                ...dataset,
                backgroundColor: dataset.backgroundColor || '#2f789e',
                borderColor: dataset.borderColor || '#2f789e',
                borderRadius: 9,
                borderSkipped: false,
                barThickness: dataset.barThickness || 18
            }))
        };

        const bidangCanvas = document.getElementById('bidangChart');
        if (bidangCanvas && !Chart.getChart(bidangCanvas)) {
            new Chart(bidangCanvas, {
                type: 'bar',
                data: bidangData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1d405c', padding: 11, cornerRadius: 9 }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#71808c', font: { size: 10 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e8eef2', borderDash: [4, 5] },
                            border: { display: false },
                            ticks: { color: '#71808c', font: { size: 10 }, stepSize: 1 }
                        }
                    }
                }
            });
        }

        const teknisiCanvas = document.getElementById('teknisiChart');
        if (teknisiCanvas && !Chart.getChart(teknisiCanvas)) {
            new Chart(teknisiCanvas, {
                type: 'bar',
                data: teknisiData,
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1200, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1d405c',
                            padding: 11,
                            cornerRadius: 9,
                            callbacks: { label: context => ` Rata-rata: ${context.parsed.x} Jam` }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#e8eef2', borderDash: [4, 5] },
                            border: { display: false },
                            ticks: {
                                color: '#71808c',
                                font: { size: 10 },
                                stepSize: 1,
                                callback: value => value + 'j'
                            }
                        },
                        y: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#647789', font: { size: 11 } }
                        }
                    }
                }
            });
        }
    }

    // DOMContentLoaded untuk pemuatan awal halaman biasa.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDinasReportCharts, { once: true });
    } else {
        initDinasReportCharts();
    }

    // Dropdown ekspor.
    window.toggleExportMenu = function () {
        document.getElementById('exportDropdownMenu')?.classList.toggle('hidden');
    };

    // Tutup dropdown bila pengguna klik di luar area menu.
    document.addEventListener('click', function (event) {
        const container = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportDropdownMenu');
        if (container && menu && !container.contains(event.target)) menu.classList.add('hidden');
    });

    // Ekspor kedua tabel berdasarkan isi yang sedang ditampilkan.
    window.exportToCSV = function () {
        const csv = [];
        const tables = document.querySelectorAll('.export-table');

        tables.forEach((table, index) => {
            csv.push(index === 0 ? '"--- LAPORAN KINERJA PER BIDANG ---"' : '"--- LAPORAN KINERJA PER TEKNISI ---"');
            table.querySelectorAll('tr').forEach(rowElement => {
                const cells = Array.from(rowElement.querySelectorAll('td, th'));
                const row = cells.map(cell => '"' + cell.innerText.replace(/[\r\n]+/g, ' ').trim().replace(/"/g, '""') + '"');
                csv.push(row.join(','));
            });
            csv.push('');
        });

        const csvFile = new Blob(['\ufeff' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const downloadLink = document.createElement('a');
        const objectUrl = window.URL.createObjectURL(csvFile);
        downloadLink.href = objectUrl;
        downloadLink.download = 'Laporan_Kinerja_Bidang_Teknisi_' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        downloadLink.remove();
        window.URL.revokeObjectURL(objectUrl);
        document.getElementById('exportDropdownMenu')?.classList.add('hidden');
    };

    window.printLaporanPDF = function () {
        document.getElementById('exportDropdownMenu')?.classList.add('hidden');
        window.print();
    };
})();
</script>

<style>
@media print {
    body { background: #fff !important; }
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        background: #fff !important;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection