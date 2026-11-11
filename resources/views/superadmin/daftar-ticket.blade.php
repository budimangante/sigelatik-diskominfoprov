@extends('components.b-master')

@section('title', 'Daftar Tiket Layanan')
@section('subtitle', 'Monitoring penanganan insiden, disposisi bidang, penugasan teknisi, dan SLA')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] px-4 py-6 font-sans sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1900px]">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>SUPER ADMIN</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>MANAGEMENT TICKETING</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Daftar Tiket Masuk
            </h1>
            <p class="mt-1 text-sm text-[#71808c]">
                Kelola tiket aduan gangguan, disposisi ke bidang terkait, tetapkan teknisi, dan pantau SLA layanan.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2f789e] px-5 py-3 text-xs font-bold text-white shadow-[0_4px_12px_rgba(47,120,158,0.22)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#256887] hover:shadow-lg cursor-pointer">
            <i class="fas fa-plus-circle text-xs"></i>
            <span>Buat Tiket Baru</span>
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-5 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
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
        <div class="mb-5 space-y-1 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800 shadow-sm">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="mb-4 rounded-2xl border border-[#dce4e9] bg-white p-3 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-3.5">
        <form method="GET" action="{{ route('daftar.ticket') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <!-- Search -->
            <div class="relative w-full lg:max-w-[440px]">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari kode tiket, subjek, atau pelapor..."
                       class="w-full rounded-xl border border-[#dce4e9] bg-[#f7f9fb] py-3 pl-10 pr-4 text-xs text-[#334b5d] placeholder:text-[#94a3ad] transition focus:border-[#397b9d] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <!-- Filter Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 min-w-0">
                <div>
                    <select name="status_id"
                            onchange="this.form.submit()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-3 text-xs text-slate-700 focus:outline-none focus:border-[#1a3668] transition">
                        <option value="">Semua Status</option>

                        @foreach($statusList as $st)
                            <option value="{{ $st->id }}"
                                {{ ($statusId ?? '') == $st->id ? 'selected' : '' }}>
                                {{ $st->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter OPD -->
                <div class="flex items-center gap-2 min-w-0">

                    <select name="perangkat_daerah_id"
                            onchange="this.form.submit()"
                            class="flex-1 min-w-0 w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-3 text-xs text-slate-700 focus:outline-none focus:border-[#1a3668] transition">
                        <option value="">Semua Dinas / OPD</option>

                        @foreach($perangkatDaerahList as $opd)
                            <option value="{{ $opd->id }}"
                                {{ ($opdId ?? '') == $opd->id ? 'selected' : '' }}>
                                {{ $opd->name }}
                            </option>
                        @endforeach
                    </select>

                    @if(!empty($search) || !empty($statusId) || !empty($opdId))
                        <a href="{{ route('daftar.ticket') }}"
                        class="px-3 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl transition shrink-0"
                        title="Reset Filter">
                            <i class="fas fa-redo-alt"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Tiket -->
    <div class="overflow-hidden rounded-2xl border border-[#dce4e9] bg-white shadow-[0_3px_14px_rgba(25,55,75,0.05)]">
        <div class="flex items-center justify-between border-b border-[#edf1f4] px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-ticket-alt"></i></span>
                <div>
                    <p class="text-sm font-bold text-[#293b49]">{{ $tickets->count() }} dari {{ $tickets->total() }} tiket</p>
                    <p class="mt-0.5 text-[10px] text-[#8a98a3]">Data tiket sesuai filter yang dipilih</p>
                </div>
            </div>
            <span class="hidden rounded-full bg-[#f4f7f9] px-3 py-1.5 text-[10px] font-semibold text-[#70818d] sm:inline-flex">Daftar tiket layanan</span>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[1050px] text-left whitespace-nowrap">
            <thead class="border-b border-[#e7edf1] bg-[#f8fafb] text-[10px] font-bold uppercase tracking-[0.12em] text-[#7c8d99]">
                <tr>
                    <th class="px-5 py-4">NO. TIKET & KELUHAN</th>
                    <th class="px-5 py-4">PELAPOR & DINAS ASAL</th>
                    <th class="px-5 py-4">DISPOSISI & TEKNISI</th>
                    <th class="px-5 py-4 text-center">STATUS</th>
                    <th class="px-5 py-4 w-44">SLA</th>
                    <th class="px-6 py-4 text-center w-28">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[#edf1f4] text-xs">
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
                    <tr class="transition-colors duration-150 hover:bg-[#f8fbfd]">
                        <!-- Nomor Tiket & Judul -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-[#dceaf2] bg-[#edf5f9] text-sm font-bold text-[#397b9d]">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[10px] font-bold tracking-wide text-[#397b9d]">{{ $ticketCode }}</span>
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wide {{ $priorityBadge }}">
                                            {{ $t->priority ?? 'Sedang' }}
                                        </span>
                                    </div>
                                    <div class="mt-1 line-clamp-1 max-w-xs text-xs font-semibold text-[#344b5b]" title="{{ $ticketSubject }}">
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
                            <div class="font-semibold leading-snug text-[#344b5b]">{{ $t->user?->name ?? 'Pelapor Umum' }}</div>
                            <div class="mt-1 flex items-center gap-1.5 text-[10px] text-[#8a98a3]">
                                <i class="fas fa-building text-slate-400 text-[10px]"></i>
                                <span>{{ $t->perangkatDaerah?->name ?? 'Pemerintah Provinsi' }}</span>
                            </div>
                        </td>

                        <!-- Disposisi Bidang & Teknisi -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-[#526879]">
                                <i class="fas fa-sitemap text-slate-400 text-[10px]"></i>
                                <span>{{ $t->bidang?->name ?? 'Belum terdisposisi' }}</span>
                            </div>
                            <div class="mt-1.5 flex items-center gap-1.5 text-[10px]">
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
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[9px] font-bold uppercase tracking-wide {{ $statusBadge }}">
                                {{ $t->status?->name ?? 'Baru' }}
                            </span>
                        </td>

                        <!-- Target SLA: Tampilan Batang & Persenan Sesuai UI Target -->
                        <td class="px-6 py-4 align-middle">
                            <div class="w-40 max-w-full">
                                <div class="mb-1.5 flex items-center justify-between gap-2">
                                    <span class="text-[10px] font-bold {{ $slaTextColor }}">
                                        {{ $slaTitle }}
                                    </span>
                                    <span class="font-mono text-[10px] font-semibold text-[#7c8d99]">
                                        {{ $slaPercent }}%
                                    </span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#e8eef2]">
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
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-[#dce6ec] bg-white text-[#397b9d] shadow-sm transition hover:border-[#9bbdce] hover:bg-[#f0f7fa] cursor-pointer"
                                        title="Ubah & Disposisi Tiket">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <form action="{{ route('daftar.ticket.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tiket [{{ $ticketCode }}]? Seluruh data tugas dan histori terkait akan dihapus.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-white text-rose-500 shadow-sm transition hover:bg-rose-50 cursor-pointer" 
                                            title="Hapus Tiket">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center text-xs italic text-[#8a98a3]">
                            Tidak ditemukan data tiket yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <!-- Paginasi -->
        @if($tickets->hasPages())
            <div class="border-t border-[#edf1f4] bg-[#fafcfd] px-5 py-4">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
    </div>
</div>

<!-- ================= MODAL 1: BUAT TIKET BARU ================= -->
<div id="modalTambahTiket" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#152b3d]/50 p-4 backdrop-blur-sm">
    <div class="flex max-h-[92vh] w-full max-w-xl flex-col overflow-hidden rounded-3xl border border-white/70 bg-white shadow-[0_24px_80px_rgba(15,35,50,0.24)]">
        <div class="flex items-center justify-between bg-[#1d405c] px-6 py-5 text-white">
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

        <form action="{{ route('daftar.ticket.store') }}" method="POST" class="space-y-4 overflow-y-auto p-6">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Permohonan / Gangguan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" required placeholder="Contoh: Gangguan Koneksi Jaringan Fiber Optic"
                       class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Dinas / OPD Pemohon <span class="text-rose-500">*</span>
                    </label>
                    <select name="perangkat_daerah_id" required
                            class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                            class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                        class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                          class="w-full rounded-xl border border-[#dce4e9] bg-white p-3.5 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahTiket')" class="rounded-xl border border-[#dce4e9] px-4 py-2.5 text-xs font-bold text-[#617582] transition hover:bg-[#f4f7f9] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#256887] cursor-pointer">
                    Terbitkan Tiket
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH STATUS & PENUGASAN TIKET ================= -->
<div id="modalEditTiket" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#152b3d]/50 p-4 backdrop-blur-sm">
    <div class="flex max-h-[92vh] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-white/70 bg-white shadow-[0_24px_80px_rgba(15,35,50,0.24)]">
        <div class="flex items-center justify-between bg-[#1d405c] px-6 py-5 text-white">
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

        <form id="formEditTiket" method="POST" action="" class="space-y-4 overflow-y-auto p-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Status Tiket <span class="text-rose-500">*</span>
                </label>
                <select name="ticket_status_id" id="editStatusId" required
                        class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                            class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                            class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
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
                        class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-[#344b5b] transition focus:border-[#397b9d] focus:outline-none focus:ring-2 focus:ring-[#397b9d]/15">
                    <option value="">Belum Ditugaskan</option>
                    @foreach($teknisiList as $tek)
                        <option value="{{ $tek->id }}">{{ $tek->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditTiket')" class="rounded-xl border border-[#dce4e9] px-4 py-2.5 text-xs font-bold text-[#617582] transition hover:bg-[#f4f7f9] cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#256887] cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalTambahTiket').classList.remove('hidden');
        document.getElementById('modalTambahTiket').classList.add('flex');
    }

    function bukaModalEdit(id, code, title, statusId, opdId, bidangId, technicianId) {
        document.getElementById('editTiketSubjudul').innerText = 'Tiket: ' + code + ' - ' + title;
        document.getElementById('editStatusId').value = statusId;
        document.getElementById('editOpdId').value = opdId;
        document.getElementById('editBidangId').value = bidangId;
        document.getElementById('editTechnicianId').value = technicianId || '';

        document.getElementById('formEditTiket').action = '{{ url("/superadmin/daftar-ticket") }}/' + id;
        document.getElementById('modalEditTiket').classList.remove('hidden');
        document.getElementById('modalEditTiket').classList.add('flex');
    }

    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
</script>
@endsection