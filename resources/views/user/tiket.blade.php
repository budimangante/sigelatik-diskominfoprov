@extends('components.b-master')

@section('title', 'Tiket Saya')
@section('subtitle', 'Daftar permohonan layanan dan status penanganan tiket Anda')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
                <span class="text-[#1a3668]">LAYANAN PENGGUNA</span>
                <span class="text-slate-300">•</span>
                <span>TIKET SAYA</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Riwayat Tiket Permohonan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau proses verifikasi, progres pengerjaan teknisi lapangan, dan riwayat penyelesaian keluhan Anda.
            </p>
        </div>

        <div>
            <a href="{{ route('user.index') }}" 
               class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fas fa-plus text-xs"></i>
                <span>Buat Pengaduan Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- 1. KARTU METRIK KPI RINGKASAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Total Tiket -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Diajukan</p>
                <h3 class="text-2xl font-extrabold text-[#1a3668] mt-1">{{ $totalCount }}</h3>
                <span class="text-[10px] text-slate-500 font-medium mt-1 inline-block">Seluruh pengaduan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-[#1a3668] text-lg shrink-0">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>

        <!-- Tiket Aktif / Diproses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</p>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $activeCount }}</h3>
                <span class="text-[10px] text-blue-500 font-medium mt-1 inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span> Dalam Penanganan
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-lg shrink-0">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <!-- Tiket Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tuntas / Selesai</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $solvedCount }}</h3>
                <span class="text-[10px] text-emerald-600 font-medium mt-1 inline-block">Berhasil ditangani</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-lg shrink-0">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        <!-- Tiket Ditolak / Dibatalkan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditolak / Batal</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $rejectedCount }}</h3>
                <span class="text-[10px] text-rose-500 font-medium mt-1 inline-block">Di luar kewenangan/batal</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 text-lg shrink-0">
                <i class="fas fa-ban"></i>
            </div>
        </div>

    </div>

    <!-- 2. PENCARIAN & FILTER INTERAKTIF -->
    <div class="mb-6 flex flex-col md:flex-row items-center gap-3.5">
        <div class="relative flex-1 w-full">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" 
                   id="myTicketSearch" 
                   oninput="applyLiveFilter()"
                   placeholder="Cari nomor tiket, judul masalah, atau layanan..." 
                   class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition shadow-2xs">
        </div>

        <div class="relative w-full md:w-56 shrink-0">
            <select id="myTicketStatusFilter" 
                    onchange="applyLiveFilter()"
                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] appearance-none cursor-pointer shadow-2xs">
                <option value="">Semua Status Tiket</option>
                @foreach($statuses as $st)
                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
        </div>
    </div>

    <!-- 3. TABEL DAFTAR TIKET SAYA -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 w-40">NOMOR TIKET</th>
                    <th class="px-6 py-4">PERMASALAHAN / LAYANAN</th>
                    <th class="px-6 py-4 w-36">STATUS</th>
                    <th class="px-6 py-4 w-48">TEKNISI</th>
                    <th class="px-6 py-4 w-40">TANGGAL MASUK</th>
                    <th class="px-6 py-4 w-36 text-center">AKSI</th>
                </tr>
            </thead>

            <tbody id="myTicketsTableBody" class="divide-y divide-slate-100 text-xs">
                @forelse($tickets as $t)
                    @php
                        $tNumber = $t->ticket_number ?? ('HD-2610-00' . $t->id);
                        $hasTech = count($t->technicians) > 0;
                        $techName = $hasTech ? implode(', ', $t->technicians) : 'Belum Ditugaskan';

                        $statusRaw = strtolower($t->status->name ?? 'baru');
                        if (str_contains($statusRaw, 'selesai') || str_contains($statusRaw, 'solved')) {
                            $badgeCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            $dotCls = 'bg-emerald-500';
                        } elseif (str_contains($statusRaw, 'pending') || str_contains($statusRaw, 'jeda')) {
                            $badgeCls = 'bg-amber-50 text-amber-700 border-amber-200';
                            $dotCls = 'bg-amber-500';
                        } elseif (str_contains($statusRaw, 'tolak') || str_contains($statusRaw, 'batal') || str_contains($statusRaw, 'cancel')) {
                            $badgeCls = 'bg-rose-50 text-rose-700 border-rose-200';
                            $dotCls = 'bg-rose-500';
                        } else {
                            $badgeCls = 'bg-blue-50 text-blue-700 border-blue-200';
                            $dotCls = 'bg-blue-600';
                        }

                        $searchIndex = strtolower($tNumber . ' ' . $t->subject . ' ' . ($t->service->name ?? '') . ' ' . $techName);
                    @endphp

                    <tr class="ticket-row hover:bg-slate-50/60 transition"
                        data-search="{{ $searchIndex }}"
                        data-status="{{ $t->ticket_status_id }}">
                        
                        <!-- Nomor Tiket -->
                        <td class="px-6 py-4 align-middle">
                            <span class="font-mono font-bold text-blue-600">{{ $tNumber }}</span>
                        </td>

                        <!-- Permasalahan & Layanan -->
                        <td class="px-6 py-4 align-middle">
                            <div class="font-bold text-slate-800 leading-snug line-clamp-1 max-w-md">
                                {{ $t->subject }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Layanan: <span class="font-medium text-slate-600">{{ $t->service->name ?? 'Layanan Umum/Teknis' }}</span>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeCls }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotCls }}"></span>
                                {{ $t->status->name ?? 'BARU' }}
                            </span>
                        </td>

                        <!-- Teknisi -->
                        <td class="px-6 py-4 align-middle">
                            @if($hasTech)
                                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-user-check text-blue-600 text-[10px]"></i>
                                    <span>{{ $techName }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 italic font-medium flex items-center gap-1">
                                    <i class="far fa-clock text-[10px]"></i> Menunggu Penugasan
                                </span>
                            @endif
                        </td>

                        <!-- Tanggal Masuk -->
                        <td class="px-6 py-4 align-middle font-mono text-slate-600">
                            {{ $t->created_at->translatedFormat('d M Y, H:i') }}
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Tombol Chat Langsung -->
                                <a href="{{ route('user.chat', ['ticket_id' => $t->id]) }}" 
                                   title="Buka Chat Penanganan"
                                   class="p-2 rounded-xl bg-blue-50 text-[#1a3668] hover:bg-blue-100 transition shadow-2xs border border-blue-100">
                                    <i class="fas fa-comments text-xs"></i>
                                </a>

                                <!-- Tombol Modal Detail Kendala -->
                                <button type="button" 
                                        onclick="openTicketDetailModal('{{ $tNumber }}', '{{ addslashes($t->subject) }}', '{{ addslashes($t->description ?? 'Tidak ada rincian tambahan.') }}', '{{ $t->status->name ?? 'BARU' }}', '{{ $techName }}')"
                                        title="Lihat Detail Tiket"
                                        class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition shadow-2xs">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyRowPlaceholder">
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400 text-xs italic">
                            Belum ada permohonan tiket yang Anda ajukan saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- State Pencarian Kosong -->
        <div id="noMatchSearchState" class="hidden py-12 text-center">
            <i class="fas fa-search text-2xl text-slate-300 mb-2"></i>
            <p class="text-xs font-semibold text-slate-600">Tidak ada tiket yang cocok</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Coba gunakan kata kunci atau pilihan status lain.</p>
        </div>
    </div>

</div>

<!-- ================= MODAL DETAIL TIKET PENGGUNA ================= -->
<div id="ticketDetailModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="bg-[#1a3668] text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-info-circle text-blue-200"></i>
                <h4 class="text-xs font-bold uppercase tracking-wider">Detail Permohonan Tiket</h4>
            </div>
            <button type="button" onclick="closeTicketDetailModal()" class="text-white/70 hover:text-white transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <span id="modalTicketCode" class="text-xs font-mono font-bold text-blue-600 block mb-1"></span>
                <h3 id="modalTicketSubject" class="text-base font-bold text-slate-800 leading-snug"></h3>
            </div>

            <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block mb-0.5">Status Tiket</span>
                    <span id="modalTicketStatus" class="font-bold text-slate-700"></span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-0.5">Teknisi Penanggung Jawab</span>
                    <span id="modalTicketTech" class="font-bold text-slate-700"></span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi Masalah</label>
                <div id="modalTicketDesc" class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs text-slate-700 leading-relaxed max-h-48 overflow-y-auto"></div>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeTicketDetailModal()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- ================= JAVASCRIPT: FILTER & MODAL ================= -->
<script>
    function applyLiveFilter() {
        const query = document.getElementById('myTicketSearch').value.toLowerCase().trim();
        const selectedStatus = document.getElementById('myTicketStatusFilter').value;
        const rows = document.querySelectorAll('.ticket-row');
        const noMatchState = document.getElementById('noMatchSearchState');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSearch = row.getAttribute('data-search') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchQuery = query === '' || rowSearch.includes(query);
            const matchStatus = selectedStatus === '' || rowStatus === selectedStatus;

            if (matchQuery && matchStatus) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        if (noMatchState) {
            if (visibleCount === 0 && rows.length > 0) {
                noMatchState.classList.remove('hidden');
            } else {
                noMatchState.classList.add('hidden');
            }
        }
    }

    function openTicketDetailModal(code, subject, desc, status, tech) {
        document.getElementById('modalTicketCode').textContent = code;
        document.getElementById('modalTicketSubject').textContent = subject;
        document.getElementById('modalTicketDesc').textContent = desc;
        document.getElementById('modalTicketStatus').textContent = status;
        document.getElementById('modalTicketTech').textContent = tech;

        document.getElementById('ticketDetailModal').classList.remove('hidden');
    }

    function closeTicketDetailModal() {
        document.getElementById('ticketDetailModal').classList.add('hidden');
    }
</script>
@endsection