@extends('components.b-master')

@section('title', 'Triage & Reassign')
@section('subtitle', 'Informasi mengenai Triage & Reassign SIGELATIK')

@section('content')
<div class="min-h-screen bg-[#f4f7fa] px-4 py-5 font-sans sm:px-6 lg:px-7 space-y-5">

    <!-- Header halaman -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-[#397b9d]"></span>
                <span>HELPDESK BIDANG</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>TRIAGE & VERIFIKASI</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Tiket Baru &amp; Reassign Antar Teknisi Bidang</h1>
            <p class="mt-1 max-w-3xl text-xs leading-relaxed text-slate-500">
                Validasi kategori permohonan, tentukan sub-layanan, atau jeda SLA jika tiket masuk di luar jam kerja efektif (menjelang 16.00).
            </p>
        </div>

        <!-- Lonceng Notifikasi Tiket Baru -->
        <div class="relative shrink-0">
            <button type="button" onclick="toggleNotifDropdown()" class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-[#dce5ec] bg-white text-slate-600 shadow-sm transition hover:border-[#a9c2d0] hover:text-[#397b9d]">
                <i class="far fa-bell text-sm"></i>
                @if(count($waitingTickets) > 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white font-bold text-[10px] rounded-full flex items-center justify-center animate-pulse">
                        {{ count($waitingTickets) }}
                    </span>
                @endif
            </button>

            <!-- Dropdown Notifikasi -->
            <div id="dropdownNotif" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-3 z-50">
                <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800">Tiket Baru Masuk</span>
                    <span class="text-[10px] bg-blue-50 text-blue-700 font-semibold px-2 py-0.5 rounded-full">
                        {{ count($waitingTickets) }} antrean
                    </span>
                </div>
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                    @forelse($newTickets as $nt)
                        <div class="p-3 hover:bg-slate-50 text-xs transition">
                            <div class="font-bold text-[#1a3668]">{{ $nt->ticket_number }}</div>
                            <div class="text-slate-600 truncate mt-0.5">{{ $nt->subject }}</div>
                            <div class="text-[10px] text-slate-400 mt-1">{{ \Carbon\Carbon::parse($nt->created_at)->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Tidak ada tiket baru.</div>
                    @endforelse
                </div>
            </div>
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

    <!-- 1. TABEL: MENUNGGU TRIAGE (SLA 1 RESPON) -->
    <div class="mb-7">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2.5">
                <h2 class="text-sm font-extrabold text-[#243d50]">Menunggu Triage</h2>
                <span class="inline-flex items-center rounded-full border border-[#dceaf1] bg-[#eff6f9] px-2.5 py-1 text-[10px] font-bold text-[#397b9d]">
                    {{ count($waitingTickets) }} Tiket
                </span>
            </div>
            <span class="text-xs text-slate-400">Standar SLA Respon Bidang: 60 Menit</span>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#dce5ec] bg-white shadow-[0_3px_14px_rgba(24,54,82,0.045)] overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 w-36">TIKET</th>
                        <th class="px-4 py-3.5">PERMOHONAN & KATEGORI USER</th>
                        <th class="px-4 py-3.5 w-48">MASUK</th>
                        <th class="px-4 py-3.5 w-56">SLA RESPON (TRIAGE)</th>
                        <th class="px-4 py-3.5 w-60 text-center">AKSI BIDANG</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($waitingTickets as $ticket)
                        <tr class="hover:bg-slate-50/60 transition {{ $ticket->is_paused ? 'bg-amber-50/20' : '' }}">
                            <td class="px-4 py-3.5 font-mono font-bold text-blue-600 align-middle">
                                <a href="#" class="hover:underline">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-bold text-slate-800 leading-snug">{{ $ticket->subject }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] text-slate-400">{{ $ticket->agency_name }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Pilihan User: {{ $ticket->category_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-500 font-medium align-middle">
                                {{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3.5 align-middle">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold text-{{ $ticket->sla_color }}-600 flex items-center gap-1.5">
                                        @if($ticket->sla_status === 'running')
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        @elseif($ticket->sla_status === 'critical')
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                        @elseif($ticket->sla_status === 'paused')
                                            <i class="fas fa-pause-circle text-[11px]"></i>
                                        @endif
                                        {{ $ticket->sla_label }}
                                    </span>
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $ticket->sla_info }}</span>
                                </div>
                                <div class="w-44 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-{{ $ticket->sla_color }}-600 h-1.5 rounded-full" style="width: {{ $ticket->sla_progress }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-middle text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button" 
                                            onclick="bukaModalTriage({{ $ticket->id }}, '{{ $ticket->ticket_number }}', '{{ addslashes($ticket->subject) }}', '{{ addslashes($ticket->agency_name) }}', '{{ addslashes($ticket->category_name) }}', {{ $ticket->service?->category_id ?? 'null' }}, {{ $ticket->service_id ?? 'null' }})"
                                            class="bg-[#327fa2] hover:bg-[#286b89] text-white px-3.5 py-2 rounded-lg text-[11px] font-bold transition shadow-sm">
                                        Proses Triage
                                    </button>
                                    @if($ticket->is_paused)
                                        <button type="button" class="px-2.5 py-2 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-semibold cursor-not-allowed" title="SLA 1 sudah dalam kondisi dijeda">
                                            <i class="fas fa-check text-xs text-amber-600"></i> Dijeda
                                        </button>
                                    @else
                                        <button type="button" 
                                                onclick="bukaModalPendingTriage({{ $ticket->id }}, '{{ $ticket->ticket_number }}')"
                                                title="Jeda SLA 1 (Di luar jam kerja / Butuh konfirmasi)"
                                                class="h-8 w-8 border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg text-xs font-bold transition">
                                            <i class="fas fa-pause text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-slate-400 italic">
                                Belum ada tiket baru yang menunggu proses triage.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. TABEL: REASSIGN ANTAR TEKNISI BIDANG -->
    <div>
        <div class="flex flex-col gap-1.5 mb-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-sm font-extrabold text-[#243d50]">Reassign Antar Teknisi Bidang</h2>
            <span class="text-xs text-slate-400">Pindahkan penugasan jika teknisi berhalangan atau ajukan penambahan SLA</span>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#dce5ec] bg-white shadow-[0_3px_14px_rgba(24,54,82,0.045)] overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 w-36">TIKET</th>
                        <th class="px-4 py-3.5 w-64">TEKNISI SAAT INI</th>
                        <th class="px-4 py-3.5 w-60">SLA PENANGANAN</th>
                        <th class="px-4 py-3.5 w-36 text-center">TAMBAH SLA</th>
                        <th class="px-4 py-3.5 text-right">AKSI REASSIGN</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($reassignTickets as $ticket)
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Nomor Tiket -->
                            <td class="px-4 py-3.5 font-mono font-bold text-blue-600 align-middle">
                                <a href="#" class="hover:underline">{{ $ticket->ticket_number }}</a>
                            </td>

                            <!-- Teknisi Saat Ini -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-bold text-slate-800 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-[#1a3668] text-white text-[10px] flex items-center justify-center font-bold shrink-0">
                                        {{ strtoupper(substr($ticket->current_tech_name ?? 'T', 0, 1)) }}
                                    </div>
                                    <span class="truncate">{{ $ticket->current_tech_name ?? 'Belum ada teknisi' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                                    {{ $ticket->service?->name ?? 'Layanan Tidak Diketahui' }}
                                </div>
                            </td>

                            <!-- SLA Penanganan (4 Kondisi Dinamis) -->
                            <td class="px-4 py-3.5 align-middle">
                                @if($ticket->sla_state === 'not_started')
                                    <!-- 1. Belum Berjalan (Menunggu Teknisi) -->
                                    <div class="w-48">
                                        <div class="flex items-center justify-between mb-1.5 text-xs">
                                            <span class="font-bold text-slate-400 flex items-center gap-1.5">
                                                <i class="far fa-clock text-[11px]"></i>
                                                Belum Berjalan (0%)
                                            </span>
                                            <span class="text-slate-400 font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold shrink-0">
                                                target {{ $ticket->target_sla_label ?? '4 jam' }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-slate-300 h-1.5 rounded-full" style="width: 0%"></div>
                                        </div>
                                    </div>
                                @elseif($ticket->sla_state === 'paused')
                                    <!-- 2. Dijeda / Pending -->
                                    <div class="w-48">
                                        <div class="flex items-center justify-between mb-1.5 text-xs">
                                            <span class="font-bold text-amber-600 flex items-center gap-1.5">
                                                <i class="fas fa-pause-circle text-[11px]"></i>
                                                Dijeda ({{ $ticket->sla_progress }}%)
                                            </span>
                                            <span class="text-amber-700 font-mono text-[10px] bg-amber-50 px-2 py-0.5 rounded font-semibold shrink-0 border border-amber-200">
                                                target {{ $ticket->target_sla_label ?? '4 jam' }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-amber-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $ticket->sla_progress }}%"></div>
                                        </div>
                                    </div>
                                @elseif($ticket->sla_state === 'overdue')
                                    <!-- 3. Terlampaui (100%) -->
                                    <div class="w-48">
                                        <div class="flex items-center justify-between mb-1.5 text-xs">
                                            <span class="font-bold text-rose-600 flex items-center gap-1.5">
                                                <i class="fas fa-exclamation-circle text-[11px]"></i>
                                                Terlampaui (100%)
                                            </span>
                                            <span class="text-slate-500 font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold shrink-0">
                                                target {{ $ticket->target_sla_label ?? '4 jam' }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-rose-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-rose-600 h-1.5 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                @else
                                    <!-- 4. Berjalan Normal -->
                                    <div class="w-48">
                                        <div class="flex items-center justify-between mb-1.5 text-xs">
                                            <span class="font-bold text-[#1a3668] flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                                Berjalan ({{ $ticket->sla_progress }}%)
                                            </span>
                                            <span class="text-slate-500 font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold shrink-0">
                                                target {{ $ticket->target_sla_label ?? '4 jam' }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#1a3668] h-1.5 rounded-full transition-all duration-500" 
                                                 style="width: {{ $ticket->sla_progress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Tombol Modal Tambah SLA -->
                            <td class="px-4 py-3.5 align-middle text-center">
                                <button type="button" 
                                        onclick="bukaModalTambahSla({{ $ticket->id }}, '{{ $ticket->ticket_number }}', '{{ $ticket->target_sla_label ?? '240 menit' }}')"
                                        class="border border-[#c8dce7] text-[#397b9d] hover:bg-[#327fa2] hover:text-white bg-white px-3 py-2 rounded-lg text-[10px] font-bold transition whitespace-nowrap cursor-pointer inline-flex items-center gap-1.5">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    <span>Tambah SLA</span>
                                </button>
                            </td>

                            <!-- Form Aksi Reassign -->
                            <td class="px-4 py-3.5 align-middle text-right">
                                <form action="{{ route('bidang.triage.reassign', $ticket->id) }}" method="POST" class="inline-flex items-center justify-end gap-2.5">
                                    @csrf
                                    <select name="technician_id" required class="w-44 bg-white border border-slate-200 rounded-lg px-2.5 py-2 text-[11px] text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                                        <option value="" disabled selected>Pilih teknisi pengganti...</option>
                                        @foreach($technicians as $tech)
                                            <option value="{{ $tech->id }}" {{ $tech->id == ($ticket->current_tech_id ?? null) ? 'disabled class=text-slate-300' : '' }}>
                                                {{ $tech->name }} {{ $tech->id == ($ticket->current_tech_id ?? null) ? '(Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="border border-[#c8dce7] text-[#397b9d] hover:bg-[#327fa2] hover:text-white bg-white px-3 py-2 rounded-lg text-[11px] font-bold transition whitespace-nowrap">
                                        Reassign
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-slate-400 italic">
                                Belum ada tiket aktif yang perlu dialihkan teknisinya.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL 1: PROSES TRIAGE & DISPOSISI ================= -->
<div id="modalTriage" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-xl overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 bg-white text-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-[#397b9d]"></i>
                    <span>Triage & Validasi Tiket</span>
                    <span id="triageTicketNumber" class="font-mono text-[#397b9d]"></span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Selesaikan triage untuk menghentikan SLA Respon 1 dan meneruskan tiket ke teknisi.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTriage')" class="text-slate-400 hover:text-slate-700 p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formTriage" method="POST" action="" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            @csrf
            
            <!-- Ringkasan Permohonan User -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Permohonan User:</div>
                <div id="triageSubject" class="text-xs font-bold text-slate-800"></div>
                <div class="text-[11px] text-slate-500 flex items-center gap-2">
                    <span id="triageAgency"></span>
                    <span class="text-slate-300">•</span>
                    <span>Pilihan awal: <b id="triageInitialCategory" class="text-slate-700"></b></span>
                </div>
            </div>

            <!-- 1. Kategori Induk -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    1. Verifikasi / Perbaiki Kategori
                </label>
                <select name="category_id" id="triageSelectCategory" required onchange="filterSubLayanan()"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Ubah kategori jika pemohon salah memilih kategori layanan.</p>
            </div>

            <!-- 2. Sub-Layanan Spesifik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    2. Tetapkan Sub-Layanan Spesifik (SOP Bidang)
                </label>
                <select name="service_id" id="triageSelectService" required onchange="updateSlaPreview()"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="" disabled selected>Pilih sub-layanan...</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}" data-category="{{ $svc->category_id }}" data-sla="{{ $svc->sla_minutes ?? 60 }}">
                            {{ $svc->name }} (Target: {{ $svc->sla_minutes ?? 60 }} Menit)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. Target Durasi SLA 2 -->
            <div class="pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        3. Target SLA Penanganan (Menit)
                    </label>
                    <span class="text-[10px] text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                        SLA 2 Teknisi
                    </span>
                </div>
                <div class="relative">
                    <input type="number" name="sla_minutes" id="triageSlaMinutes" min="5" step="5" value="60" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] [&::-webkit-inner-spin-button]:opacity-100 [&::-webkit-outer-spin-button]:opacity-100">
                    <span class="absolute right-10 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">Menit</span>
                </div>

            </div>

            <!-- 4. Pilihan Teknisi -->
            <div class="pt-2 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    4. Tugaskan ke Teknisi (Opsional)
                </label>
                <div class="space-y-2 max-h-32 overflow-y-auto pr-1">
                    @forelse($technicians as $tech)
                        <label class="flex items-center gap-2.5 p-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 text-xs">
                            <input type="checkbox" name="technician_ids[]" value="{{ $tech->id }}" class="w-4 h-4 rounded text-[#1a3668] accent-[#1a3668]">
                            <span class="font-medium text-slate-700">{{ $tech->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada akun pengguna dengan role teknisi.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTriage')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Selesaikan Triage & Teruskan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: JEDA / PENDING SLA 1 ================= -->
<div id="modalPendingTriage" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="bg-amber-600 text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-pause-circle text-amber-200 text-lg"></i>
                <div>
                    <h3 class="text-sm font-bold">Jeda SLA Respon (Pending Triage)</h3>
                    <p id="pendingTicketNumber" class="text-[11px] text-amber-100 font-mono mt-0.5"></p>
                </div>
            </div>
            <button type="button" onclick="tutupModal('modalPendingTriage')" class="text-slate-400 hover:text-slate-700 p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formPendingTriage" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alasan Penjedaan SLA Respon
                </label>
                <select name="pending_reason" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                    <option value="outside_office_hours" selected>Di luar jam kerja operasional (Menjelang / Lewat 16:00)</option>
                    <option value="waiting_user_attachment">Menunggu kelengkapan dokumen / bukti error dari OPD</option>
                    <option value="cross_department_coordination">Memerlukan koordinasi lintas bidang sebelum klasifikasi</option>
                    <option value="other">Alasan lainnya</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Timer SLA 1 akan dibekukan sampai bidang memproses tiket ini.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Tambahan untuk Pemohon (Opsional)
                </label>
                <textarea name="pending_note" rows="3" placeholder="Contoh: Laporan diterima pukul 15.52. Verifikasi dan penugasan teknisi dilanjutkan esok hari kerja mulai pukul 08.00 WITA."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalPendingTriage')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Bekukan SLA Respon
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: TAMBAH SLA PENANGANAN TIKET ================= -->
<div id="modalTambahSla" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="px-5 py-4 border-b border-slate-100 bg-white text-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-clock text-[#397b9d]"></i>
                    <span>Tambah Durasi SLA Tiket</span>
                </h3>
                <p id="tambahSlaTicketNumber" class="text-[11px] text-[#397b9d] font-mono mt-0.5"></p>
            </div>
            <button type="button" onclick="tutupModal('modalTambahSla')" class="text-slate-400 hover:text-slate-700 p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formTambahSla" method="POST" action="" class="p-6 space-y-4">
            @csrf
            
            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-xs flex items-center justify-between">
                <span class="text-slate-500 font-medium">Target SLA Saat Ini:</span>
                <span id="tambahSlaCurrentTarget" class="font-bold text-[#1a3668] font-mono"></span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Durasi Tambahan SLA (Menit) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           name="additional_minutes" 
                           id="inputAdditionalMinutes" 
                           min="5" 
                           step="5" 
                           value="60" 
                           required
                           class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-16 py-2.5 text-xs text-slate-800 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">Menit</span>
                </div>
                <!-- Tombol Preset Cepat -->
                <div class="flex items-center gap-2 mt-2">
                    <button type="button" onclick="document.getElementById('inputAdditionalMinutes').value = 30" class="px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">+30 Mnt</button>
                    <button type="button" onclick="document.getElementById('inputAdditionalMinutes').value = 60" class="px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">+1 Jam</button>
                    <button type="button" onclick="document.getElementById('inputAdditionalMinutes').value = 120" class="px-2.5 py-1 text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">+2 Jam</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alasan / Catatan Penambahan SLA (Opsional)
                </label>
                <textarea name="reason" 
                          rows="2.5" 
                          placeholder="Contoh: Menunggu penggantian unit switch atau koordinasi teknis di lokasi..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahSla')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan Penambahan SLA
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleNotifDropdown() {
        const dd = document.getElementById('dropdownNotif');
        dd.classList.toggle('hidden');
    }

    function bukaModalTriage(ticketId, ticketNumber, subject, agency, category, initialCatId, initialServiceId) {
        document.getElementById('triageTicketNumber').innerText = '(' + ticketNumber + ')';
        document.getElementById('triageSubject').innerText = subject;
        document.getElementById('triageAgency').innerText = agency;
        document.getElementById('triageInitialCategory').innerText = category;
        document.getElementById('formTriage').action = '/bidang/triage/' + ticketId + '/process';

        if (initialCatId) {
            document.getElementById('triageSelectCategory').value = initialCatId;
            filterSubLayanan();
            if (initialServiceId) {
                document.getElementById('triageSelectService').value = initialServiceId;
                updateSlaPreview();
            }
        }

        document.getElementById('modalTriage').classList.remove('hidden');
    }

    function bukaModalPendingTriage(ticketId, ticketNumber) {
        document.getElementById('pendingTicketNumber').innerText = 'Tiket: ' + ticketNumber;
        document.getElementById('formPendingTriage').action = '/bidang/triage/' + ticketId + '/pending';
        document.getElementById('modalPendingTriage').classList.remove('hidden');
    }

    function bukaModalTambahSla(ticketId, ticketNumber, currentTarget) {
        document.getElementById('tambahSlaTicketNumber').innerText = '(' + ticketNumber + ')';
        document.getElementById('tambahSlaCurrentTarget').innerText = currentTarget;
        document.getElementById('formTambahSla').action = '/bidang/triage/' + ticketId + '/tambahsla';
        document.getElementById('modalTambahSla').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function updateSlaPreview() {
        const select = document.getElementById('triageSelectService');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            const sla = selectedOption.getAttribute('data-sla');
            if (sla) {
                document.getElementById('triageSlaMinutes').value = sla;
            }
        }
    }

    function filterSubLayanan() {
        const categoryId = document.getElementById('triageSelectCategory').value;
        const serviceSelect = document.getElementById('triageSelectService');
        const options = serviceSelect.querySelectorAll('option[data-category]');

        let hasSelected = false;
        options.forEach(opt => {
            if (opt.getAttribute('data-category') === categoryId) {
                opt.style.display = 'block';
                if (!hasSelected) {
                    opt.selected = true;
                    hasSelected = true;
                }
            } else {
                opt.style.display = 'none';
            }
        });

        updateSlaPreview();
    }
</script>
@endsection