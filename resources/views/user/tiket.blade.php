@extends('components.b-master')

@section('title', 'Tiket Saya')
@section('subtitle', 'Daftar permohonan layanan dan status penanganan tiket Anda')

@section('content')
<div class="min-h-screen bg-[#f3f6f9] p-5 font-sans md:p-8">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>LAYANAN PENGGUNA</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>TIKET SAYA</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Riwayat Tiket
            </h1>
            <p class="mt-1 text-xs text-slate-500 md:text-sm">
                Pantau proses verifikasi, progres pengerjaan teknisi lapangan, dan riwayat penyelesaian keluhan Anda.
            </p>
        </div>

        <div>
            <a href="{{ route('user.index') }}" 
               class="inline-flex items-center gap-2 rounded-xl bg-[#347fa5] px-5 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-[#286a8d]">
                <i class="fas fa-plus text-xs"></i>
                <span>Buat Pengaduan Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-5 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
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
    <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        
        <!-- Total Tiket -->
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Total Diajukan</p>
                <h3 class="mt-1 text-3xl font-black text-[#1d405c]">{{ $totalCount }}</h3>
                <span class="text-[10px] text-slate-500 font-medium mt-1 inline-block">Seluruh pengaduan</span>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#1d405c] text-base text-white shadow-sm">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>

        <!-- Tiket Aktif / Diproses -->
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Sedang Diproses</p>
                <h3 class="mt-1 text-3xl font-black text-[#347fa5]">{{ $activeCount }}</h3>
                <span class="text-[10px] text-blue-500 font-medium mt-1 inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span> Dalam Penanganan
                </span>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#347fa5] text-base text-white shadow-sm">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <!-- Tiket Selesai -->
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Tuntas / Selesai</p>
                <h3 class="mt-1 text-3xl font-black text-emerald-600">{{ $solvedCount }}</h3>
                <span class="text-[10px] text-emerald-600 font-medium mt-1 inline-block">Berhasil ditangani</span>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-base text-white shadow-sm">
                <i class="fas fa-check-double"></i>
            </div>
        </div>

        <!-- Tiket Ditolak / Dibatalkan -->
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-400">Ditolak / Batal</p>
                <h3 class="mt-1 text-3xl font-black text-rose-600">{{ $rejectedCount }}</h3>
                <span class="text-[10px] text-rose-500 font-medium mt-1 inline-block">Di luar kewenangan/batal</span>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-base text-white shadow-sm">
                <i class="fas fa-ban"></i>
            </div>
        </div>

    </div>

    <!-- 2. PENCARIAN & FILTER INTERAKTIF -->
    <div class="mb-4 flex flex-col items-stretch gap-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm md:flex-row md:items-center">
        <div class="relative w-full min-w-0 flex-1">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text" 
                   id="myTicketSearch" 
                   oninput="applyLiveFilter()"
                   placeholder="Cari nomor tiket, judul masalah, atau layanan..." 
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-9 pr-4 text-xs text-slate-700 placeholder:text-slate-400 shadow-inner outline-none transition focus:border-[#347fa5] focus:ring-2 focus:ring-[#347fa5]/15">
        </div>

        <div class="relative w-full shrink-0 md:w-56">
            <select id="myTicketStatusFilter" 
                    onchange="applyLiveFilter()"
                    class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-xs text-slate-700 outline-none transition focus:border-[#347fa5] focus:ring-2 focus:ring-[#347fa5]/15">
                <option value="">Semua Status Tiket</option>
                @foreach($statuses as $st)
                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
        </div>
    </div>

    <!-- 3. TABEL DAFTAR TIKET SAYA -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3.5"><div class="flex items-center gap-2 text-xs font-bold text-slate-700"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-[#347fa5]"><i class="fas fa-list-ul"></i></span>Daftar Permohonan</div><span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-600">{{ $tickets->count() }} tiket</span></div>
        <div class="overflow-x-auto"><table class="w-full whitespace-nowrap text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-[10px] font-extrabold uppercase tracking-[0.12em] text-slate-500">
                <tr>
                    <th class="px-5 py-4 w-40">NOMOR TIKET</th>
                    <th class="px-5 py-4">PERMASALAHAN / LAYANAN</th>
                    <th class="px-5 py-4 w-36">STATUS</th>
                    <th class="px-5 py-4 w-48">TEKNISI</th>
                    <th class="px-5 py-4 w-40">TANGGAL MASUK</th>
                    <th class="px-5 py-4 w-36 text-center">AKSI</th>
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

                    <tr class="ticket-row border-b border-slate-100 transition last:border-0 hover:bg-[#f7fafc]"
                        data-search="{{ $searchIndex }}"
                        data-status="{{ $t->ticket_status_id }}">
                        
                        <!-- Nomor Tiket -->
                        <td class="px-5 py-4 align-middle">
                            <span class="font-mono font-bold text-blue-600">{{ $tNumber }}</span>
                        </td>

                        <!-- Permasalahan & Layanan -->
                        <td class="px-5 py-4 align-middle">
                            <div class="font-bold text-slate-800 leading-snug line-clamp-1 max-w-md">
                                {{ $t->subject }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Layanan: <span class="font-medium text-slate-600">{{ $t->service->name ?? 'Layanan Umum/Teknis' }}</span>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-4 align-middle">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeCls }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotCls }}"></span>
                                {{ $t->status->name ?? 'BARU' }}
                            </span>
                        </td>

                        <!-- Teknisi -->
                        <td class="px-5 py-4 align-middle">
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
                        <td class="px-5 py-4 align-middle font-mono text-slate-600">
                            {{ $t->created_at->translatedFormat('d M Y, H:i') }}
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-5 py-4 align-middle text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Tombol Chat Langsung -->
                                <a href="{{ route('user.chat', ['ticket_id' => $t->id]) }}" 
                                   title="Buka Chat Penanganan"
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-[#347fa5] shadow-sm transition hover:bg-blue-100">
                                    <i class="fas fa-comments text-xs"></i>
                                </a>

                                <!-- Tombol Modal Detail Kendala -->
                                <button type="button"
                                        data-open-ticket-detail
                                        data-ticket-code="{{ $tNumber }}"
                                        data-ticket-subject="{{ $t->subject }}"
                                        data-ticket-description="{{ $t->description ?? 'Tidak ada rincian tambahan.' }}"
                                        data-ticket-status="{{ $t->status->name ?? 'BARU' }}"
                                        data-ticket-tech="{{ $techName }}"
                                        title="Lihat Detail Tiket"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 shadow-sm transition hover:bg-slate-200">
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
        </table></div>

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

    document.querySelectorAll('[data-open-ticket-detail]').forEach(button => {
        button.addEventListener('click', function () {
            openTicketDetailModal(
                this.dataset.ticketCode || '',
                this.dataset.ticketSubject || '',
                this.dataset.ticketDescription || '',
                this.dataset.ticketStatus || '',
                this.dataset.ticketTech || ''
            );
        });
    });

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