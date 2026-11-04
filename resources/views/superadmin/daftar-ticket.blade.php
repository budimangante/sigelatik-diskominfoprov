@extends('components.b-master')

@section('title', 'Daftar Tiket Layanan')
@section('subtitle', 'Monitoring penanganan insiden, disposisi bidang, penugasan teknisi, dan SLA')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">SUPER ADMIN</span>
                <span class="text-slate-300">•</span>
                <span>MANAGEMENT TICKETING</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Daftar Tiket Masuk
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola tiket aduan gangguan, disposisi ke bidang terkait, tetapkan teknisi, dan pantau SLA layanan.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
            <i class="fas fa-plus-circle text-xs"></i>
            <span>Buat Tiket Baru</span>
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-2xs">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs mb-6">
        <form method="GET" action="{{ route('daftar.ticket') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative lg:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari kode tiket, subjek, atau pelapor..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <!-- Filter Status -->
            <div>
                <select name="status_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-[#1a3668] transition">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $st)
                        <option value="{{ $st->id }}" {{ ($statusId ?? '') == $st->id ? 'selected' : '' }}>
                            {{ $st->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter OPD -->
            <div class="flex items-center gap-2">
                <select name="perangkat_daerah_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-[#1a3668] transition">
                    <option value="">Semua Dinas / OPD</option>
                    @foreach($perangkatDaerahList as $opd)
                        <option value="{{ $opd->id }}" {{ ($opdId ?? '') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->name }}
                        </option>
                    @endforeach
                </select>

                @if(!empty($search) || !empty($statusId) || !empty($opdId))
                    <a href="{{ route('daftar.ticket') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl font-medium transition shrink-0" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Tiket -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">NO. TIKET & KELUHAN</th>
                    <th class="px-6 py-4">PELAPOR & DINAS ASAL</th>
                    <th class="px-6 py-4">DISPOSISI & TEKNISI</th>
                    <th class="px-6 py-4 text-center">STATUS</th>
                    <th class="px-6 py-4 w-44">SLA</th>
                    <th class="px-6 py-4 text-center w-28">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($tickets as $t)
                    @php
                        // 1. Data Identitas Tiket
                        $ticketCode = $t->ticket_number ?? $t->code ?? ('TCK-' . $t->id);
                        $ticketSubject = $t->subject ?? $t->title ?? 'Tanpa Judul Keluhan';

                        // 2. Prioritas Tiket
                        $priorityVal = strtolower($t->priority ?? 'sedang');
                        $priorityBadge = match($priorityVal) {
                            'critical', 'kritis' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'high', 'tinggi'     => 'bg-amber-50 text-amber-700 border-amber-200',
                            'low', 'rendah'      => 'bg-slate-100 text-slate-600 border-slate-200',
                            default              => 'bg-blue-50 text-blue-700 border-blue-200',
                        };

                        // 3. Status Badge
                        $statusName = strtolower($t->status?->name ?? 'baru');
                        $isCompleted = str_contains($statusName, 'selesai') || str_contains($statusName, 'closed') || str_contains($statusName, 'resolved') || !empty($t->solved_at);
                        $isPaused = str_contains($statusName, 'pending') || str_contains($statusName, 'tunda');

                        $statusBadge = match(true) {
                            $isCompleted => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            str_contains($statusName, 'proses') || str_contains($statusName, 'progress') => 'bg-sky-50 text-sky-700 border-sky-200',
                            $isPaused => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        };

                        // 4. Kalkulasi Durasi & Persentase SLA
                        $targetMinutes = match($priorityVal) {
                            'critical', 'kritis' => 240,   // 4 jam
                            'high', 'tinggi'     => 480,   // 8 jam
                            'low', 'rendah'      => 2880,  // 48 jam
                            default              => 1440,  // 24 jam (sedang)
                        };

                        if ($t->service && !empty($t->service->sla_minutes)) {
                            $targetMinutes = (int) $t->service->sla_minutes;
                        }

                        $createdAt = $t->created_at ? \Carbon\Carbon::parse($t->created_at) : now();
                        $elapsedMinutes = max(0, (int) $createdAt->diffInMinutes(now()));

                        if ($isCompleted) {
                            $slaTitle     = 'Tercapai';
                            $slaPercent   = 100;
                            $slaTextColor = 'text-emerald-700';
                            $slaBarColor  = 'bg-emerald-600';
                        } elseif ($isPaused) {
                            $slaTitle     = 'Dibekukan';
                            $slaPercent   = min(100, max(5, (int) round(($elapsedMinutes / max(1, $targetMinutes)) * 100)));
                            $slaTextColor = 'text-amber-600';
                            $slaBarColor  = 'bg-amber-500';
                        } elseif ($elapsedMinutes >= $targetMinutes) {
                            $slaTitle     = 'Terlampaui';
                            $slaPercent   = 100;
                            $slaTextColor = 'text-red-600';
                            $slaBarColor  = 'bg-red-600';
                        } else {
                            $slaTitle     = 'Berjalan';
                            $slaPercent   = min(100, max(8, (int) round(($elapsedMinutes / max(1, $targetMinutes)) * 100)));
                            $slaTextColor = 'text-[#1a3668]';
                            $slaBarColor  = 'bg-[#1a3668]';
                        }

                        // 5. Teknisi Ditugaskan
                        $assignedTech = $t->assigned_technician 
                            ?? $t->tasks->first()?->assignees->first() 
                            ?? null;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Nomor Tiket & Judul -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1a3668] flex items-center justify-center font-bold text-xs shrink-0 border border-blue-100 mt-0.5">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-[#1a3668]">{{ $ticketCode }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border uppercase {{ $priorityBadge }}">
                                            {{ $t->priority ?? 'Sedang' }}
                                        </span>
                                    </div>
                                    <div class="font-bold text-slate-800 text-xs mt-0.5 line-clamp-1 max-w-xs" title="{{ $ticketSubject }}">
                                        {{ $ticketSubject }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                        {{ $t->created_at?->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Pelapor & OPD Asal -->
                        <td class="px-6 py-4 align-middle">
                            <div class="font-bold text-slate-800 leading-snug">{{ $t->user?->name ?? 'Pelapor Umum' }}</div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                <i class="fas fa-building text-slate-400 text-[10px]"></i>
                                <span>{{ $t->perangkatDaerah?->name ?? 'Pemerintah Provinsi' }}</span>
                            </div>
                        </td>

                        <!-- Disposisi Bidang & Teknisi -->
                        <td class="px-6 py-4 align-middle">
                            <div class="text-[11px] font-semibold text-slate-700 flex items-center gap-1.5">
                                <i class="fas fa-sitemap text-slate-400 text-[10px]"></i>
                                <span>{{ $t->bidang?->name ?? 'Belum terdisposisi' }}</span>
                            </div>
                            <div class="text-[11px] mt-1 flex items-center gap-1.5">
                                <i class="fas fa-user-cog text-blue-500 text-[10px]"></i>
                                @if($assignedTech)
                                    <span class="font-bold text-[#1a3668]">{{ $assignedTech->name }}</span>
                                @else
                                    <span class="text-slate-400 italic font-medium">Belum ditugaskan</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status Tiket -->
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $statusBadge }}">
                                {{ $t->status?->name ?? 'Baru' }}
                            </span>
                        </td>

                        <!-- Target SLA: Tampilan Batang & Persenan Sesuai UI Target -->
                        <td class="px-6 py-4 align-middle">
                            <div class="w-36">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-bold {{ $slaTextColor }}">
                                        {{ $slaTitle }}
                                    </span>
                                    <span class="text-[11px] font-bold font-mono text-slate-500">
                                        {{ $slaPercent }}%
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="{{ $slaBarColor }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $slaPercent }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="bukaModalEdit(
                                            {{ $t->id }}, 
                                            '{{ addslashes($ticketCode) }}', 
                                            '{{ addslashes($ticketSubject) }}', 
                                            '{{ $t->ticket_status_id }}', 
                                            '{{ $t->perangkat_daerah_id }}', 
                                            '{{ $t->bidang_id }}', 
                                            '{{ $assignedTech?->id ?? '' }}'
                                        )"
                                        class="p-2 border border-slate-200 text-slate-600 hover:text-[#1a3668] hover:border-slate-300 rounded-xl transition cursor-pointer"
                                        title="Ubah & Disposisi Tiket">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <form action="{{ route('daftar.ticket.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tiket [{{ $ticketCode }}]? Seluruh data tugas dan histori terkait akan dihapus.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 border border-rose-100 bg-rose-50/50 text-rose-600 hover:bg-rose-100 rounded-xl transition cursor-pointer" 
                                            title="Hapus Tiket">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-xs text-slate-400 italic">
                            Tidak ditemukan data tiket yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Paginasi -->
        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: BUAT TIKET BARU ================= -->
<div id="modalTambahTiket" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150 max-h-[90vh]">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-ticket-alt text-blue-200"></i>
                    <span>Buat Tiket Permohonan / Insiden</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Daftarkan tiket layanan baru untuk ditindaklanjuti.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTambahTiket')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('daftar.ticket.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Permohonan / Gangguan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" required placeholder="Contoh: Gangguan Koneksi Jaringan Fiber Optic"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Dinas / OPD Pemohon <span class="text-rose-500">*</span>
                    </label>
                    <select name="perangkat_daerah_id" required
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                        <option value="" disabled selected>Pilih Perangkat Daerah...</option>
                        @foreach($perangkatDaerahList as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Bidang Penanganan <span class="text-rose-500">*</span>
                    </label>
                    <select name="bidang_id" required
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                        <option value="" disabled selected>Pilih Bidang...</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tugaskan Teknisi (Opsional)
                </label>
                <select name="technician_id"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                    <option value="">Belum Ditugaskan</option>
                    @foreach($teknisiList as $tek)
                        <option value="{{ $tek->id }}">{{ $tek->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Detail Masalah <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="3" required placeholder="Jelaskan detail kendala teknis atau permohonan yang diajukan..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahTiket')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Terbitkan Tiket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH STATUS & PENUGASAN TIKET ================= -->
<div id="modalEditTiket" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-edit text-blue-200"></i>
                    <span>Perbarui Informasi & Disposisi Tiket</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5" id="editTiketSubjudul">Perbarui penugasan teknisi dan status tiket.</p>
            </div>
            <button type="button" onclick="tutupModal('modalEditTiket')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditTiket" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Status Tiket <span class="text-rose-500">*</span>
                </label>
                <select name="ticket_status_id" id="editStatusId" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                    @foreach($statusList as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Dinas / OPD Terkait <span class="text-rose-500">*</span>
                    </label>
                    <select name="perangkat_daerah_id" id="editOpdId" required
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                        @foreach($perangkatDaerahList as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Disposisi Bidang <span class="text-rose-500">*</span>
                    </label>
                    <select name="bidang_id" id="editBidangId" required
                            class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Teknisi Penanggung Jawab
                </label>
                <select name="technician_id" id="editTechnicianId"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                    <option value="">Belum Ditugaskan</option>
                    @foreach($teknisiList as $tek)
                        <option value="{{ $tek->id }}">{{ $tek->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditTiket')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalTambahTiket').classList.remove('hidden');
    }

    function bukaModalEdit(id, code, title, statusId, opdId, bidangId, technicianId) {
        document.getElementById('editTiketSubjudul').innerText = 'Tiket: ' + code + ' - ' + title;
        document.getElementById('editStatusId').value = statusId;
        document.getElementById('editOpdId').value = opdId;
        document.getElementById('editBidangId').value = bidangId;
        document.getElementById('editTechnicianId').value = technicianId || '';

        document.getElementById('formEditTiket').action = '{{ url("/superadmin/daftar-ticket") }}/' + id;
        document.getElementById('modalEditTiket').classList.remove('hidden');
    }

    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection