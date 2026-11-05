@extends('components.b-master')

@section('title', 'Laporan Kinerja SLA')
@section('subtitle', 'Laporan Kinerja SLA Perangkat Daerah Pemerintah Provinsi')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section + Dropdown Download -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
                <span class="text-[#1a3668]">EXECUTIVE REPORT</span>
                <span class="text-slate-300">•</span>
                <span>SLA SERVICE DELIVERY</span>
            </div>
            <h1 class="text-2xl font-black text-[#1a3668] tracking-tight">
                Laporan Kinerja & Rata-rata SLA Dinas
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Akumulasi seluruh tiket dan kalkulasi SLA dinas berbasis integrasi durasi bidang layanan dan realisasi teknisi.
            </p>
        </div>

        <!-- Tombol Aksi & Dropdown Unduh -->
        <div class="flex items-center gap-3">
            <div class="relative inline-block text-left" id="exportDropdownContainer">
                <button type="button" onclick="toggleExportMenu()" class="inline-flex items-center gap-2.5 px-4 py-2.5 bg-[#1a3668] hover:bg-[#12264a] text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                    <i class="fas fa-file-arrow-down text-sm"></i>
                    <span>Unduh Laporan</span>
                    <i class="fas fa-chevron-down text-[10px] ml-1 opacity-70"></i>
                </button>

                <!-- Dropdown Menu Item -->
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
    </div>

    <!-- 4 Indikator Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Dinas Terlayani</p>
                <h3 class="text-2xl font-black text-[#1a3668] mt-1">{{ $laporanData['total_dinas'] }}</h3>
                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded inline-block mt-1">Perangkat Daerah</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-landmark"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total Aduan Tiket</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $laporanData['total_tickets'] }}</h3>
                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded inline-block mt-1">Akumulasi Masuk</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-ticket"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Rata-rata SLA Dinas</p>
                <h3 class="text-2xl font-black text-[#1a3668] mt-1">{{ $laporanData['global_avg_sla'] }}<span class="text-xs font-semibold text-slate-500 ml-1">Jam</span></h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">Integrasi Bidang + Teknisi</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Tingkat Penyelesaian</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $laporanData['global_solve_rate'] }}%</h3>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded inline-block mt-1">Telah Dituntaskan</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan Komprehensif -->
    <div id="printArea" class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <!-- Filter Bar Sederhana -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-[#1a3668]"></i>
                    <span>Tabel Rekapitulasi SLA per Dinas</span>
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari nama dinas..." class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-[#1a3668] w-56">
            </div>
        </div>

        <!-- Tabel Render -->
        <div class="overflow-x-auto w-full">
            <table id="laporanTable" class="w-full text-left whitespace-nowrap text-xs">
                <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">PERANGKAT DAERAH / DINAS</th>
                        <th class="px-4 py-3.5 text-center">TOTAL TIKET</th>
                        <th class="px-4 py-3.5 text-center">SELESAI</th>
                        <th class="px-4 py-3.5 text-center">DIPROSES</th>
                        <th class="px-4 py-3.5 text-center bg-blue-50/40 text-blue-800">SLA TARGET BIDANG</th>
                        <th class="px-4 py-3.5 text-center bg-slate-100/50 text-slate-800">REALISASI TEKNISI</th>
                        <th class="px-4 py-3.5 text-center bg-emerald-50/50 text-emerald-900 font-extrabold">RATA-RATA SLA DINAS</th>
                        <th class="px-4 py-3.5 text-center">KEPATUHAN</th>
                        <th class="px-4 py-3.5 text-center">PREDIKAT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporanData['laporan_table'] as $row)
                        <tr class="hover:bg-slate-50/70 transition dinas-row">
                            <td class="px-5 py-3.5 font-bold text-slate-800 flex items-center gap-2 dinas-name">
                                <span class="w-2 h-2 rounded-full bg-[#1a3668] shrink-0"></span>
                                <span>{{ $row['name'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-700 font-mono">{{ $row['total_ticket'] }}</td>
                            <td class="px-4 py-3.5 text-center text-emerald-600 font-semibold font-mono">{{ $row['resolved_ticket'] }}</td>
                            <td class="px-4 py-3.5 text-center text-blue-600 font-semibold font-mono">{{ $row['process_ticket'] }}</td>
                            <td class="px-4 py-3.5 text-center font-mono font-bold text-blue-700 bg-blue-50/20">
                                {{ $row['avg_target_bidang'] }} Jam
                            </td>
                            <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-700 bg-slate-50/40">
                                {{ $row['avg_realisasi_teknisi'] }} Jam
                            </td>
                            <td class="px-4 py-3.5 text-center font-mono font-black text-emerald-700 bg-emerald-50/30">
                                {{ $row['avg_sla_dinas'] }} Jam
                            </td>
                            <td class="px-4 py-3.5 text-center font-mono font-bold text-slate-600">
                                {{ $row['kepatuhan_percent'] }}%
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($row['badge_color'] === 'emerald')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $row['status_kinerja'] }}
                                    </span>
                                @elseif($row['badge_color'] === 'blue')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $row['status_kinerja'] }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $row['status_kinerja'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400 italic">
                                Belum ada riwayat tiket dinas yang dapat dilaporkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-500 gap-2">
            <span>* Rumus Kalkulasi: <strong>Rata-rata SLA Dinas = (Target SLA Bidang + Realisasi Penanganan Teknisi) ÷ 2</strong></span>
            <span>Diperbarui otomatis: {{ now()->translatedFormat('d F Y H:i') }} WITA</span>
        </div>
    </div>

</div>

<!-- Script Filter & Ekspor Interaktif -->
<script>
    // Toggle dropdown ekspor
    function toggleExportMenu() {
        const menu = document.getElementById('exportDropdownMenu');
        menu.classList.toggle('hidden');
    }

    // Tutup dropdown jika klik di luar
    window.addEventListener('click', function(e) {
        const container = document.getElementById('exportDropdownContainer');
        if (!container.contains(e.target)) {
            document.getElementById('exportDropdownMenu').classList.add('hidden');
        }
    });

    // Fitur Live Search Table
    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.dinas-row');
        rows.forEach(row => {
            const name = row.querySelector('.dinas-name').innerText.toLowerCase();
            row.style.display = name.includes(input) ? '' : 'none';
        });
    }

    // 1. Ekspor ke Excel / CSV
    function exportToCSV() {
        let csv = [];
        const rows = document.querySelectorAll("#laporanTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                text = text.replace(/"/g, '""'); // escape double quotes
                row.push('"' + text + '"');
            }
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], { type: "text/csv;charset=utf-8;" });
        const downloadLink = document.createElement("a");
        downloadLink.download = "Laporan_Kinerja_SLA_Dinas_" + new Date().toISOString().slice(0,10) + ".csv";
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
        border: none;
        box-shadow: none;
    }
    #searchInput {
        display: none;
    }
}
</style>
@endsection