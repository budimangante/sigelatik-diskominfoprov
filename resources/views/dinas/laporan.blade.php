@extends('components.b-master')

@section('title', 'Laporan Kinerja Bidang & Teknisi')
@section('subtitle', 'Laporan kepatuhan SLA per bidang layanan dan efisiensi teknisi penangan')

@section('content')
<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section + Dropdown Unduh Laporan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
                <span class="text-[#1a3668]">EXECUTIVE REPORT</span>
                <span class="text-slate-300">•</span>
                <span>BIDANG & TEKNISI DISKOMINFO</span>
            </div>
            <h1 class="text-2xl font-black text-[#1a3668] tracking-tight">
                Laporan Kinerja Bidang & Penanganan Teknisi
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Monitoring volume aduan bidang, kepatuhan batas waktu SLA, dan kecepatan penyelesaian oleh teknisi.
            </p>
        </div>

        <!-- Tombol Aksi Unduh -->
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportMenu()" class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-[#1a3668] hover:bg-[#12264a] text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                <i class="fas fa-file-arrow-down text-sm"></i>
                <span>Unduh Laporan</span>
                <i class="fas fa-chevron-down text-[10px] ml-1 opacity-70"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="exportDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50 text-xs font-medium text-slate-700">
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
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Bidang Layanan</p>
                <h3 class="text-2xl font-black text-[#1a3668] mt-1">{{ $laporanData['total_bidang'] }}</h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded inline-block mt-1">Sektor Penanganan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Teknisi Aktif</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $laporanData['total_teknisi'] }}</h3>
                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded inline-block mt-1">Personel Penangan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-user-gear"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Rata-rata Waktu Selesai</p>
                <h3 class="text-2xl font-black text-[#1a3668] mt-1">{{ $laporanData['global_avg_duration'] }}<span class="text-xs font-semibold text-slate-500 ml-1">Jam</span></h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">Efisiensi Teknisi</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-stopwatch"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Tingkat Penyelesaian</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $laporanData['global_solve_rate'] }}%</h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">Tiket Dituntaskan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- 2 Charts Berdampingan (Beban Bidang & Kecepatan Teknisi) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: Beban & Resolusi per Bidang -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex flex-col justify-between min-w-0">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-column text-[#1a3668]"></i>
                        <span>Beban Tiket per Bidang Layanan</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan jumlah tiket masuk vs tiket yang berhasil diselesaikan.</p>
                </div>
            </div>

            <div class="relative w-full h-64 my-3">
                <canvas id="bidangChart"></canvas>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-center gap-6 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-slate-500">
                    <span class="w-2.5 h-2.5 rounded bg-slate-300"></span> Total Masuk
                </span>
                <span class="flex items-center gap-1.5 text-emerald-600">
                    <span class="w-2.5 h-2.5 rounded bg-emerald-500"></span> Tiket Selesai
                </span>
            </div>
        </div>

        <!-- Chart 2: Kecepatan Penanganan per Teknisi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex flex-col justify-between min-w-0">
            <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-bolt text-[#1a3668]"></i>
                        <span>Kecepatan Penyelesaian per Teknisi</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rata-rata durasi kerja penanganan tiket hingga tuntas (Jam).</p>
                </div>
            </div>

            <div class="relative w-full h-64 my-3">
                <canvas id="teknisiChart"></canvas>
            </div>

            <div class="pt-3 border-t border-slate-100 text-center text-[11px] text-slate-400 font-medium">
                Semakin pendek bar durasi = performa teknisi semakin responsif
            </div>
        </div>

    </div>

    <!-- Area Print Laporan: Tabel Bidang & Teknisi -->
    <div id="printArea" class="space-y-6">

        <!-- Tabel 1: Rekapitulasi per Bidang Layanan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-table text-[#1a3668]"></i>
                    <span>Tabel Kinerja per Bidang Layanan</span>
                </h3>
                <span class="text-[10px] text-slate-400">Target SLA vs Realisasi</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left whitespace-nowrap text-xs export-table">
                    <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">
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
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-users-gear text-[#1a3668]"></i>
                    <span>Tabel Kinerja & Kecepatan Teknisi</span>
                </h3>
                <span class="text-[10px] text-slate-400">Efisiensi Kerja Personel</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table id="laporanTeknisiTable" class="w-full text-left whitespace-nowrap text-xs export-table">
                    <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">
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
            
            <div class="p-3.5 bg-slate-50 border-t border-slate-100 text-right text-[11px] text-slate-400">
                Laporan digenerate otomatis: {{ now()->translatedFormat('d F Y H:i') }} WITA
            </div>
        </div>

    </div>

</div>

<!-- Inisialisasi Script Chart.js & Download -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bidangConfig  = {!! json_encode($laporanData['bidangChart'] ?? ['labels' => [], 'datasets' => []]) !!};
        const teknisiConfig = {!! json_encode($laporanData['teknisiChart'] ?? ['labels' => [], 'datasets' => []]) !!};

        // 1. Chart Beban per Bidang
        new Chart(document.getElementById('bidangChart'), {
            type: 'bar',
            data: bidangConfig,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });

        // 2. Chart Efisiensi Waktu Teknisi
        new Chart(document.getElementById('teknisiChart'), {
            type: 'bar',
            data: teknisiConfig,
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` Rata-rata: ${ctx.parsed.x} Jam`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: (v) => v + 'j'
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    y: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });
    });

    // Toggle dropdown export
    function toggleExportMenu() {
        const menu = document.getElementById('exportDropdownMenu');
        menu.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const container = document.getElementById('exportDropdownContainer');
        if (!container.contains(e.target)) {
            document.getElementById('exportDropdownMenu').classList.add('hidden');
        }
    });

    // 1. Export Excel / CSV Gabungan
    function exportToCSV() {
        let csv = [];
        const tables = document.querySelectorAll(".export-table");
        
        tables.forEach((table, index) => {
            csv.push(index === 0 ? '"--- LAPORAN KINERJA PER BIDANG ---"' : '"--- LAPORAN KINERJA PER TEKNISI ---"');
            const rows = table.querySelectorAll("tr");
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) {
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                    text = text.replace(/"/g, '""');
                    row.push('"' + text + '"');
                }
                csv.push(row.join(","));
            }
            csv.push(""); // baris pemisah antar tabel
        });

        const csvFile = new Blob([csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        const downloadLink = document.createElement("a");
        downloadLink.download = "Laporan_Kinerja_Bidang_Teknisi_" + new Date().toISOString().slice(0,10) + ".csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        document.getElementById('exportDropdownMenu').classList.add('hidden');
    }

    // 2. Cetak / Simpan PDF
    function printLaporanPDF() {
        document.getElementById('exportDropdownMenu').classList.add('hidden');
        window.print();
    }
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection