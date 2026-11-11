@extends('components.b-master')

@section('title', 'Laporan Kinerja SLA')
@section('subtitle', 'Laporan Kinerja SLA Perangkat Daerah Pemerintah Provinsi')

@section('content')
<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-6">
    <!-- Page heading and export actions -->
    <div class="flex flex-col gap-4 rounded-[20px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>Executive Report</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>SLA Service Delivery</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Laporan Kinerja</h1>
            <p class="mt-1 max-w-3xl text-xs text-[#84929c]">Akumulasi tiket dan kalkulasi SLA dinas berdasarkan target bidang serta realisasi penanganan teknisi.</p>
        </div>
        <div class="relative shrink-0" id="exportDropdownContainer">
            <button type="button" onclick="toggleExportMenu()" aria-expanded="false" aria-controls="exportDropdownMenu" class="inline-flex items-center gap-2.5 rounded-xl bg-[#1d405c] px-4 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-[#17344b] focus:outline-none focus:ring-2 focus:ring-[#8eb4c8] focus:ring-offset-2">
                <i class="fas fa-file-arrow-down"></i><span>Unduh Laporan</span><i class="fas fa-chevron-down ml-1 text-[10px] opacity-75"></i>
            </button>
            <div id="exportDropdownMenu" class="absolute right-0 z-30 mt-2 hidden w-52 overflow-hidden rounded-xl border border-[#dce4e9] bg-white py-1.5 text-xs font-semibold text-[#526775] shadow-[0_12px_30px_rgba(23,57,82,0.14)]">
                <button type="button" onclick="exportToCSV()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-[#25845c] transition hover:bg-[#f2f8f4]"><i class="fas fa-file-excel w-4 text-center"></i><span>Ekspor Excel / CSV</span></button>
                <button type="button" onclick="printLaporanPDF()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-[#bd5360] transition hover:bg-[#fff5f5]"><i class="fas fa-file-pdf w-4 text-center"></i><span>Cetak / Simpan PDF</span></button>
            </div>
        </div>
    </div>

    <div id="printArea" class="space-y-5">
        <!-- KPI cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
            <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Dinas Terlayani</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#1d405c]">{{ $laporanData['total_dinas'] ?? 0 }}</h2><span class="mt-3 inline-flex rounded-full bg-[#edf4f8] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">Perangkat Daerah</span></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-lg text-[#347fa5]"><i class="fas fa-landmark"></i></span>
            </div>
            <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Total Aduan Tiket</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#263746]">{{ $laporanData['total_tickets'] ?? 0 }}</h2><span class="mt-3 inline-flex rounded-full bg-[#f0f3f5] px-2.5 py-1 text-[10px] font-bold text-[#667985]">Akumulasi Masuk</span></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#f0f3f5] text-lg text-[#617684]"><i class="fas fa-ticket-alt"></i></span>
            </div>
            <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Rata-rata SLA Dinas</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#1d405c]">{{ $laporanData['global_avg_sla'] ?? 0 }}<span class="ml-1 text-sm font-bold text-[#70828e]">Jam</span></h2><span class="mt-3 inline-flex rounded-full bg-[#edf7f1] px-2.5 py-1 text-[10px] font-bold text-[#25845c]">Integrasi Bidang + Teknisi</span></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#edf7f1] text-lg text-[#25845c]"><i class="far fa-clock"></i></span>
            </div>
            <div class="flex min-h-[112px] items-center justify-between rounded-[18px] border border-[#dce4e9] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div><p class="text-[10px] font-extrabold uppercase tracking-[0.13em] text-[#84939d]">Tingkat Penyelesaian</p><h2 class="mt-2 text-[30px] font-extrabold leading-none text-[#25845c]">{{ $laporanData['global_solve_rate'] ?? 0 }}%</h2><span class="mt-3 inline-flex rounded-full bg-[#edf7f1] px-2.5 py-1 text-[10px] font-bold text-[#25845c]">Telah Dituntaskan</span></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#edf7f1] text-lg text-[#25845c]"><i class="fas fa-check"></i></span>
            </div>
        </div>

        <!-- Search bar separated as in the reference design -->
        <div class="flex flex-col gap-3 rounded-[18px] border border-[#dce4e9] bg-white p-3 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 px-2 text-[11px] font-semibold text-[#7e8f9a]"><i class="fas fa-filter text-[#5a9cba]"></i><span>Filter data laporan</span></div>
            <label class="relative block w-full sm:max-w-[320px]">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-[#91a0a9]"></i>
                <input type="text" id="searchInput" oninput="filterTable()" placeholder="Cari nama dinas..." class="w-full rounded-xl border border-[#dce5eb] bg-[#f8fafc] py-2.5 pl-9 pr-3 text-xs text-[#405765] outline-none transition placeholder:text-[#9aa8b1] focus:border-[#6c9eb8] focus:bg-white focus:ring-2 focus:ring-[#e8f1f5]">
            </label>
        </div>

        <!-- Report table -->
        <section class="overflow-hidden rounded-[20px] border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-center gap-3 border-b border-[#edf1f4] p-5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-clipboard-list"></i></span>
                <div><h2 class="text-sm font-extrabold text-[#1d405c]">Tabel Rekapitulasi SLA per Dinas</h2><p class="mt-0.5 text-[11px] text-[#8b99a3]">Rincian target bidang, realisasi teknisi, rata-rata SLA, dan predikat setiap dinas.</p></div>
            </div>
            <div class="overflow-x-auto">
                <table id="laporanTable" class="w-full min-w-[1040px] text-left text-xs">
                    <thead class="border-b border-[#e7edf1] bg-[#f7fafc] text-[9px] font-extrabold uppercase tracking-wider text-[#81909a]">
                        <tr>
                            <th class="px-5 py-3.5">Perangkat Daerah / Dinas</th>
                            <th class="px-4 py-3.5 text-center">Total Tiket</th>
                            <th class="px-4 py-3.5 text-center">Selesai</th>
                            <th class="px-4 py-3.5 text-center">Diproses</th>
                            <th class="bg-[#f0f6fa] px-4 py-3.5 text-center text-[#397b9d]">SLA Target Bidang</th>
                            <th class="bg-[#f5f7f8] px-4 py-3.5 text-center text-[#617684]">Realisasi Teknisi</th>
                            <th class="bg-[#f0f7f3] px-4 py-3.5 text-center text-[#25845c]">Rata-rata SLA Dinas</th>
                            <th class="px-4 py-3.5 text-center">Kepatuhan</th>
                            <th class="px-4 py-3.5 text-center">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf1f4]">
                        @forelse($laporanData['laporan_table'] as $row)
                            <tr class="dinas-row transition hover:bg-[#f8fbfc]">
                                <td class="dinas-name px-5 py-3.5 font-bold text-[#344957]"><span class="mr-2 inline-block h-2 w-2 rounded-full bg-[#347fa5]"></span>{{ $row['name'] }}</td>
                                <td class="px-4 py-3.5 text-center font-extrabold tabular-nums text-[#344957]">{{ $row['total_ticket'] }}</td>
                                <td class="px-4 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#edf7f1] px-2 py-1 font-bold tabular-nums text-[#25845c]">{{ $row['resolved_ticket'] }}</span></td>
                                <td class="px-4 py-3.5 text-center"><span class="inline-flex min-w-8 justify-center rounded-full bg-[#eef5f9] px-2 py-1 font-bold tabular-nums text-[#347fa5]">{{ $row['process_ticket'] }}</span></td>
                                <td class="bg-[#f7fafc] px-4 py-3.5 text-center font-bold tabular-nums text-[#397b9d]">{{ $row['avg_target_bidang'] }} Jam</td>
                                <td class="bg-[#fafbfc] px-4 py-3.5 text-center font-semibold tabular-nums text-[#617684]">{{ $row['avg_realisasi_teknisi'] }} Jam</td>
                                <td class="bg-[#f5faf7] px-4 py-3.5 text-center font-extrabold tabular-nums text-[#25845c]">{{ $row['avg_sla_dinas'] }} Jam</td>
                                <td class="px-4 py-3.5 text-center font-extrabold tabular-nums text-[#526775]">{{ $row['kepatuhan_percent'] }}%</td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($row['badge_color'] === 'emerald')
                                        <span class="inline-flex rounded-full border border-[#cce8d8] bg-[#edf7f1] px-2.5 py-1 text-[9px] font-extrabold text-[#25845c]">{{ $row['status_kinerja'] }}</span>
                                    @elseif($row['badge_color'] === 'blue')
                                        <span class="inline-flex rounded-full border border-[#d1e4ed] bg-[#eef5f9] px-2.5 py-1 text-[9px] font-extrabold text-[#347fa5]">{{ $row['status_kinerja'] }}</span>
                                    @else
                                        <span class="inline-flex rounded-full border border-[#f0e0c3] bg-[#fff8ec] px-2.5 py-1 text-[9px] font-extrabold text-[#a87931]">{{ $row['status_kinerja'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-5 py-12 text-center text-xs text-[#91a0a9]">Belum ada riwayat tiket dinas yang dapat dilaporkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-2 border-t border-[#edf1f4] bg-[#f8fafc] p-4 text-[10px] text-[#81909a] sm:flex-row sm:items-center sm:justify-between">
                <span>* Rumus: <strong class="text-[#617684]">Rata-rata SLA Dinas = (Target SLA Bidang + Realisasi Penanganan Teknisi) ÷ 2</strong></span>
                <span>Diperbarui otomatis: {{ now()->translatedFormat('d F Y H:i') }} WITA</span>
            </div>
        </section>
    </div>
</div>

<script>
function toggleExportMenu() {
    const menu = document.getElementById('exportDropdownMenu');
    const button = document.querySelector('[aria-controls="exportDropdownMenu"]');
    if (!menu) return;
    menu.classList.toggle('hidden');
    if (button) button.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
}

window.addEventListener('click', function (event) {
    const container = document.getElementById('exportDropdownContainer');
    const menu = document.getElementById('exportDropdownMenu');
    const button = document.querySelector('[aria-controls="exportDropdownMenu"]');
    if (container && menu && !container.contains(event.target)) {
        menu.classList.add('hidden');
        if (button) button.setAttribute('aria-expanded', 'false');
    }
});

function filterTable() {
    const input = (document.getElementById('searchInput')?.value || '').toLocaleLowerCase('id-ID').trim();
    document.querySelectorAll('.dinas-row').forEach(row => {
        const name = row.querySelector('.dinas-name')?.textContent || '';
        row.style.display = name.toLocaleLowerCase('id-ID').includes(input) ? '' : 'none';
    });
}

function exportToCSV() {
    const table = document.getElementById('laporanTable');
    if (!table) return;
    const rows = Array.from(table.querySelectorAll('tr'));
    const csv = rows.map(row => Array.from(row.querySelectorAll('th,td')).map(cell => {
        const value = cell.innerText.replace(/[\r\n]+/g, ' ').trim().replace(/"/g, '""');
        return `"${value}"`;
    }).join(',')).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'Laporan_Kinerja_SLA_Dinas_' + new Date().toISOString().slice(0, 10) + '.csv';
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
    document.getElementById('exportDropdownMenu')?.classList.add('hidden');
}

function printLaporanPDF() {
    document.getElementById('exportDropdownMenu')?.classList.add('hidden');
    window.print();
}
</script>

<style>
@media print {
    body * { visibility: hidden !important; }
    #printArea, #printArea * { visibility: visible !important; }
    #printArea { position: absolute; inset: 0; width: 100%; padding: 0; background: #fff; }
    #searchInput, #exportDropdownContainer { display: none !important; }
    #printArea section, #printArea > div { break-inside: avoid; box-shadow: none !important; }
}
</style>
@endsection
