@extends('components.b-master')

@php
    // 1. Ambil data pengerjaan dari tickets_task
    $task = $task ?? DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();

    // 2. Ambil target durasi SLA Penanganan (snapshot aktivitas / SLA layanan)
    $taskMinutes = 0;
    if ($task) {
        $taskMinutes = (int) DB::table('ticket_task_activities')
            ->where('ticket_task_id', $task->id)
            ->sum('target_minutes_snapshot');
    }

    $targetMinutes = $taskMinutes > 0 ? $taskMinutes : (int) ($ticket->service?->sla_minutes ?? 240);

    // Safeguard agar $slaHours dan $targetLabel tidak pernah undefined
    $slaHours = $slaHours ?? round($targetMinutes / 60, 1);

    if (!isset($targetLabel)) {
        if ($targetMinutes < 60) {
            $targetLabel = "{$targetMinutes} Menit";
        } elseif ($targetMinutes % 60 === 0) {
            $targetLabel = ($targetMinutes / 60) . " Jam";
        } else {
            $targetLabel = floor($targetMinutes / 60) . " Jam " . ($targetMinutes % 60) . " Menit";
        }
    }

    $isStarted = isset($isStarted) ? $isStarted : !empty($task?->started_at);
    
    // Status ID dinamis
    $statusProsesId  = $statusProsesId ?? (DB::table('ticket_statuses')->where('name', 'like', '%proses%')->value('id') ?? 2);
    $statusPendingId = $statusPendingId ?? (DB::table('ticket_statuses')->where('name', 'like', '%pending%')->value('id') ?? 3);
    $statusSelesaiId = $statusSelesaiId ?? (DB::table('ticket_statuses')->where('name', 'like', '%selesai%')->value('id') ?? 4);

    $isPaused = isset($isPaused) ? $isPaused : ((int) $ticket->ticket_status_id === (int) $statusPendingId);

    // 3. Perhitungan timer riil SLA penanganan
    $remainingSeconds = $remainingSeconds ?? 0;
    $isOverdue = $isOverdue ?? false;

    if ($isStarted && !isset($dueAt)) {
        $startTime = \Carbon\Carbon::parse($task->started_at);
        $dueAt = $startTime->copy()->addMinutes($targetMinutes);

        if ($isPaused) {
            $freezeTime = $ticket->updated_at ? \Carbon\Carbon::parse($ticket->updated_at) : now();
            $elapsedMinutes = max(0, (int) $startTime->diffInMinutes($freezeTime));
            $remainingSeconds = max(0, ($targetMinutes - $elapsedMinutes) * 60);
            $isOverdue = $elapsedMinutes >= $targetMinutes;
        } else {
            $now = now();
            $diffSecs = (int) $now->diffInSeconds($dueAt, false);
            $isOverdue = $diffSecs <= 0;
            $remainingSeconds = max(0, $diffSecs);
        }
    }
@endphp

@section('content')
<div class="p-5 md:p-7 bg-[#f3f5f7] min-h-screen font-sans">
    
    <!-- ================= TOAST NOTIFIKASI POJOK KANAN ================= -->
    <div id="toastNotification" class="fixed top-5 right-5 z-50 transform transition-all duration-300 translate-y-[-20px] opacity-0 pointer-events-none">
        <div class="bg-white border border-slate-200 shadow-xl rounded-2xl p-4 flex items-center gap-3.5 min-w-[320px]">
            <div id="toastIconBg" class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i id="toastIcon" class="fas fa-check text-base"></i>
            </div>
            <div class="flex-1">
                <h5 id="toastTitle" class="text-xs font-bold text-slate-800">Status Diperbarui</h5>
                <p id="toastMessage" class="text-[11px] text-slate-500 mt-0.5">Tiket telah dialihkan ke Solved.</p>
            </div>
            <button type="button" onclick="hideToast()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Header Tiket & Navigasi Balik -->
    <div class="mb-4">
        <a href="{{ route('teknisi.index') }}" class="text-[11px] font-bold text-[#397b9d] hover:text-[#245f7d] hover:underline flex items-center gap-2 mb-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Task
        </a>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- ================= BANNER SLA DINAMIS ================= -->
    <div id="bannerSlaBox" class="{{ $isPaused ? 'bg-amber-600' : ($isStarted && $isOverdue ? 'bg-rose-700' : 'bg-[#2d789e]') }} text-white px-5 md:px-6 py-4 md:py-5 rounded-2xl shadow-[0_5px_18px_rgba(45,120,158,0.18)] mb-5 flex flex-col justify-center transition-colors">
        <div class="flex items-center justify-between">
            <span class="text-xs uppercase tracking-wider {{ $isPaused ? 'text-amber-100' : 'text-blue-200' }} font-semibold mb-1">
                SISA WAKTU SLA PENANGANAN
            </span>
            <span class="text-xs px-2.5 py-0.5 rounded-full {{ $isPaused ? 'bg-amber-700/60 text-white' : 'bg-white/10 text-blue-200' }} font-mono">
                Target: {{ $targetLabel }}
            </span>
        </div>
        <div id="slaTimer" class="text-3xl md:text-[42px] font-mono font-bold tracking-wide mt-1">
            Memuat...
        </div>
    </div>

    <!-- ================= KARTU INFO TIKET LENGKAP ================= -->
    <div class="bg-white rounded-2xl border border-[#dce4eb] p-5 md:p-6 shadow-[0_3px_14px_rgba(27,64,92,0.05)] mb-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-mono font-semibold text-blue-600">
                {{ $ticket->ticket_number ?? ('HD-2610-00' . $ticket->id) }}
            </span>
            <span id="badgeTicketStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                {{ $ticket->status->name ?? 'PROCESS' }}
            </span>
        </div>

        <h1 class="text-lg md:text-xl font-extrabold text-[#253b4c] mb-5">
            {{ $ticket->subject }}
        </h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4 border-t border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Pelapor</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $ticket->user->name ?? 'Anonim' }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Instansi</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $ticket->user->organization ?? 'Dinas Terkait' }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Layanan</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $ticket->service->name ?? 'Umum / Teknis' }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Masuk</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                </span>
            </div>
        </div>
    </div>

    <!-- GRID 2 KOLOM: FORM PENANGANAN (KIRI) + SIDEBAR CHAT (KANAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-5 items-start">
        
        <!-- ================= KOLOM KIRI (2/3): FORM TINDAKAN TEKNISI ================= -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.05)] border border-[#dce4eb] p-5 md:p-6">
                
                <!-- Deskripsi Keluhan Pelapor -->
                <div class="mb-5 pb-5 border-b border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Masalah Pelapor</label>
                    <div class="w-full bg-slate-50 border border-gray-200 rounded-xl p-3.5 text-xs text-gray-700 leading-relaxed">
                        {{ $ticket->description ?? 'Tidak ada catatan deskripsi keluhan.' }}
                    </div>
                </div>

                @if(!$isStarted)
                    <!-- KONDISI 1: Belum Mulai Dikerjakan -->
                    <div class="bg-[#f1f7fa] border border-dashed border-[#b9d1df] rounded-2xl p-7 text-center">
                        <div class="w-14 h-14 rounded-full bg-[#1a3668]/10 text-[#1a3668] flex items-center justify-center mx-auto mb-3.5 text-xl font-bold">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Tiket Siap Ditangani</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto mb-5 leading-relaxed">
                            Form tindakan teknisi dan timer SLA penanganan (Target: {{ $slaHours }} Jam) akan otomatis aktif setelah Anda menekan tombol di bawah.
                        </p>

                        <form action="{{ route('teknisi.tickets.start', $ticket->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="bg-[#2d789e] hover:bg-[#245f7d] text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow-sm inline-flex items-center gap-2">
                                <i class="fas fa-play text-xs"></i>
                                <span>Mulai Kerjakan Tiket</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- KONDISI 2: Sudah Mulai (Form Tindakan Terbuka) -->
                    <form id="formTeknisi" onsubmit="submitFormTeknisi(event)" class="space-y-5" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Pilihan Status Kerja: Diproses, Pending, Selesai -->
                        <div class="bg-white rounded-2xl border border-[#dce4eb] p-5 shadow-sm">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                                Pilih Status / Tindakan Pengerjaan
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Opsi 1: Diproses -->
                                <label class="border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none {{ $ticket->ticket_status_id == $statusProsesId ? 'border-blue-600 bg-blue-50/20' : 'border-slate-200 hover:border-slate-300' }}" id="cardStatus2">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-blue-700">Diproses</span>
                                        <input type="radio" name="ticket_status_id" value="{{ $statusProsesId }}" {{ $ticket->ticket_status_id == $statusProsesId ? 'checked' : '' }} onchange="onStatusRadioChange('{{ $statusProsesId }}')" class="text-[#1a3668] focus:ring-0">
                                    </div>
                                    <p class="text-[11px] text-slate-400">Pekerjaan aktif berjalan hari ini</p>
                                </label>

                                <!-- Opsi 2: Pending (Dijeda) -->
                                <label class="border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none {{ $ticket->ticket_status_id == $statusPendingId ? 'border-amber-600 bg-amber-50/20' : 'border-slate-200 hover:border-slate-300' }}" id="cardStatus3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-amber-600 flex items-center gap-1">
                                            <i class="fas fa-pause-circle text-[11px]"></i> Pending (Jeda)
                                        </span>
                                        <input type="radio" name="ticket_status_id" value="{{ $statusPendingId }}" {{ $ticket->ticket_status_id == $statusPendingId ? 'checked' : '' }} onchange="onStatusRadioChange('{{ $statusPendingId }}')" class="text-amber-600 focus:ring-0">
                                    </div>
                                    <p class="text-[11px] text-slate-400">Jam pulang / Lanjut besok (SLA Beku)</p>
                                </label>

                                <!-- Opsi 3: Selesai -->
                                <label class="border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none {{ $ticket->ticket_status_id == $statusSelesaiId ? 'border-emerald-600 bg-emerald-50/20' : 'border-slate-200 hover:border-slate-300' }}" id="cardStatus4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                            <i class="fas fa-check-circle text-[11px]"></i> Selesai
                                        </span>
                                        <input type="radio" name="ticket_status_id" value="{{ $statusSelesaiId }}" {{ $ticket->ticket_status_id == $statusSelesaiId ? 'checked' : '' }} onchange="onStatusRadioChange('{{ $statusSelesaiId }}')" class="text-emerald-600 focus:ring-0">
                                    </div>
                                    <p class="text-[11px] text-slate-400">Penanganan tuntas & tutup tiket</p>
                                </label>
                            </div>

                            <!-- Input Khusus Alasan Jeda -->
                            <div id="boxAlasanPending" class="{{ $ticket->ticket_status_id == $statusPendingId ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100">
                                <label class="block text-xs font-bold text-amber-700 uppercase tracking-wider mb-1.5">
                                    Alasan Penjedaan Tiket (Pending)
                                </label>
                                <select name="pending_reason" class="w-full bg-amber-50/40 border border-amber-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600">
                                    <option value="Menjelang jam pulang operasional kerja (Dilanjutkan esok hari)" selected>Menjelang jam pulang operasional kerja (Dilanjutkan esok hari)</option>
                                    <option value="Menunggu ketersediaan suku cadang / perangkat pengganti">Menunggu ketersediaan suku cadang / perangkat pengganti</option>
                                    <option value="Menunggu konfirmasi atau kehadiran PIC OPD di ruangan">Menunggu konfirmasi atau kehadiran PIC OPD di ruangan</option>
                                    <option value="Koordinasi lanjutan dengan pihak ketiga / vendor jaringan">Koordinasi lanjutan dengan pihak ketiga / vendor jaringan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Catatan Penanganan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan penanganan</label>
                            <textarea id="catatanPenanganan" name="notes" rows="3" 
                                      placeholder="Tuliskan tindakan yang dilakukan atau alasan kendala..." 
                                      class="w-full border border-gray-200 rounded-xl p-3.5 text-xs text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition-all"></textarea>
                        </div>

                        <!-- Checkbox Teruskan ke Bidang -->
                        <div class="flex justify-end">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" id="checkTeruskanBidang" name="teruskan_ke_bidang" value="1" 
                                       onchange="handleTeruskanBidang(this)"
                                       class="w-4 h-4 rounded text-[#1e3a8a] border-gray-300 focus:ring-blue-500/20 cursor-pointer">
                                <span class="text-xs font-semibold text-slate-700">Teruskan Ke Bidang & Selesaikan ?</span>
                            </label>
                        </div>

                        <!-- Dokumen Laporan Pengerjaan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dokumen laporan / Bukti pengerjaan (Opsional)</label>
                            <div class="border border-dashed border-gray-300 rounded-xl p-2 bg-slate-50/50 flex items-center gap-3">
                                <label class="bg-[#1e3a8a] hover:bg-[#152a6a] text-white px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition shadow-sm shrink-0">
                                    Browse...
                                    <input type="file" id="dokumenLaporan" name="document" class="hidden" onchange="updateFileName(this)">
                                </label>
                                <span id="fileNameDisplay" class="text-xs text-gray-500 truncate">No file selected.</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">PDF, JPG, PNG, atau DOCX. Maksimal 10 MB.</p>
                        </div>

                        <!-- Tombol Simpan Laporan Dinamis -->
                        <button type="submit" id="btnSubmitLaporan" 
                                class="w-full py-3 rounded-xl bg-[#2d789e] hover:bg-[#245f7d] text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                            <span id="btnSubmitText">
                                @if($ticket->ticket_status_id == $statusPendingId)
                                    Simpan & Jeda Waktu (Pending)
                                @elseif($ticket->ticket_status_id == $statusSelesaiId)
                                    Selesaikan Tiket & Masukkan Riwayat
                                @else
                                    Simpan Tindakan (Diproses)
                                @endif
                            </span>
                        </button>
                    </form>
                @endif

            </div>
        </div>

        <!-- Chat realtime tersedia sebagai panel mengambang; kontainer tetap ada agar pemuatan pesan berjalan seperti semula. -->
        <div class="fixed bottom-6 right-6 z-40">
            <div id="detailChatWidget" class="hidden absolute bottom-16 right-0 w-[min(360px,calc(100vw-2rem))] bg-white rounded-2xl shadow-2xl border border-[#dce4eb] h-[min(560px,calc(100vh-7rem))] flex-col overflow-hidden">
                <div class="bg-[#1d405c] text-white p-3.5 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white">
                            <i class="fas fa-comments text-sm text-blue-200"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold leading-tight">{{ $ticket->user->name ?? 'Pelapor' }}</h4>
                            <p class="text-[10px] text-blue-200 font-light">
                                {{ $ticket->user?->organization ? $ticket->user->organization . ' • ' : '' }}Pelapor • Online
                            </p>
                        </div>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-400/20"></span>
                </div>

                <div id="detailChatContainer" class="flex-1 p-3.5 overflow-y-auto space-y-3 bg-[#f8fafc]">
                    <div class="text-center py-6 text-xs text-gray-400 font-medium">Memuat obrolan...</div>
                </div>

                <form id="detailChatForm" 
                      action="javascript:void(0);" 
                      onsubmit="event.preventDefault(); sendDetailMessage(event); return false;" 
                      class="p-2.5 bg-white border-t border-slate-200 flex gap-2 items-center">
                    <input type="text" 
                           id="detailChatInput" 
                           placeholder="Ketik pesan untuk pelapor..." 
                           autocomplete="off"
                           class="flex-1 bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a]">
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-[#152a6a] text-white w-9 h-9 rounded-xl flex items-center justify-center transition shadow-sm shrink-0">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
            <button type="button" onclick="toggleDetailChat()" aria-label="Buka atau tutup chat pelapor"
                    class="w-12 h-12 rounded-full bg-[#2d789e] hover:bg-[#245f7d] text-white shadow-lg flex items-center justify-center transition relative border-2 border-white">
                <i class="fas fa-comments text-lg"></i>
            </button>
        </div>

    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    function toggleDetailChat() {
        const widget = document.getElementById('detailChatWidget');
        widget.classList.toggle('hidden');
        if (!widget.classList.contains('hidden')) {
            widget.classList.add('flex');
            scrollDetailChatToBottom();
        } else {
            widget.classList.remove('flex');
        }
    }

    const ticketId = {{ (int) $ticket->id }};
    const currentUserId = {{ auth()->id() ?? 'null' }};
    let toastTimeout;

    // ================= TIMER SLA PENANGANAN TEKNISI =================
    const isStarted = {{ $isStarted ? 'true' : 'false' }};
    const isPaused = {{ $isPaused ? 'true' : 'false' }};
    let remainingSeconds = {{ (int) $remainingSeconds }};
    let isOverdue = {{ $isOverdue ? 'true' : 'false' }};

    function updateSLATimer() {
        const timerElement = document.getElementById("slaTimer");
        if (!timerElement) return;

        // KONDISI 1: Belum Dimulai
        if (!isStarted) {
            timerElement.innerHTML = `<span class="text-slate-300">--:--:--</span> <span class="text-xs font-medium text-blue-200 block mt-1">(Belum Berjalan • Target {{ $targetLabel }})</span>`;
            return;
        }

        // KONDISI 2: Dijeda / Pending
        if (isPaused) {
            const h = Math.floor(remainingSeconds / 3600);
            const m = Math.floor((remainingSeconds % 3600) / 60);
            const s = remainingSeconds % 60;
            const pad = (n) => n.toString().padStart(2, '0');
            timerElement.innerHTML = `<span class="text-amber-300">${pad(h)}:${pad(m)}:${pad(s)}</span> <span class="text-xs font-semibold text-amber-200 block mt-1">(Dijeda / Pending)</span>`;
            return;
        }

        // KONDISI 3: Terlewati
        if (remainingSeconds <= 0 || isOverdue) {
            timerElement.innerHTML = `<span class="text-rose-400">00:00:00</span> <span class="text-xs font-bold text-rose-300 block mt-1">(SLA Terlewati)</span>`;
            return;
        }

        // KONDISI 4: Berjalan Normal
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;
        const pad = (n) => n.toString().padStart(2, '0');

        timerElement.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
        remainingSeconds--;
    }

    updateSLATimer();
    if (isStarted && !isPaused && !isOverdue) {
        setInterval(updateSLATimer, 1000);
    }

    // ================= HANDLER FORM STATUS RADIO =================
    const ID_PROSES = "{{ $statusProsesId }}";
    const ID_PENDING = "{{ $statusPendingId }}";
    const ID_SELESAI = "{{ $statusSelesaiId }}";

    function onStatusRadioChange(statusVal) {
        const boxPending = document.getElementById('boxAlasanPending');
        const btnText = document.getElementById('btnSubmitText');
        const checkBidang = document.getElementById('checkTeruskanBidang');
        
        const card2 = document.getElementById('cardStatus2');
        const card3 = document.getElementById('cardStatus3');
        const card4 = document.getElementById('cardStatus4');

        if (card2) card2.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-slate-200 hover:border-slate-300';
        if (card3) card3.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-slate-200 hover:border-slate-300';
        if (card4) card4.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-slate-200 hover:border-slate-300';

        if (String(statusVal) === String(ID_PENDING)) {
            if (boxPending) boxPending.classList.remove('hidden');
            if (btnText) btnText.innerText = 'Simpan & Jeda Waktu (Pending)';
            if (checkBidang) checkBidang.checked = false;
            if (card3) card3.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-amber-600 bg-amber-50/20';
        } else if (String(statusVal) === String(ID_SELESAI)) {
            if (boxPending) boxPending.classList.add('hidden');
            if (btnText) btnText.innerText = 'Selesaikan Tiket & Masukkan Riwayat';
            if (card4) card4.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-emerald-600 bg-emerald-50/20';
        } else {
            if (boxPending) boxPending.classList.add('hidden');
            if (btnText) btnText.innerText = 'Simpan Tindakan (Diproses)';
            if (checkBidang) checkBidang.checked = false;
            if (card2) card2.className = 'border-2 rounded-xl p-3.5 flex flex-col justify-between cursor-pointer transition select-none border-blue-600 bg-blue-50/20';
        }
    }

    function handleTeruskanBidang(checkbox) {
        if (checkbox.checked) {
            const radioSelesai = document.querySelector(`input[name="ticket_status_id"][value="${ID_SELESAI}"]`);
            if (radioSelesai) {
                radioSelesai.checked = true;
                onStatusRadioChange(ID_SELESAI);
            }
        }
    }

    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        display.textContent = input.files.length > 0 ? input.files[0].name : 'No file selected.';
    }

    async function submitFormTeknisi(e) {
        e.preventDefault();
        const form = document.getElementById('formTeknisi');
        const btn = document.getElementById('btnSubmitLaporan');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

        try {
            const res = await fetch(`/teknisi/tickets/${ticketId}/progress`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                showToast(data.title, data.message);
                updateStatusBadge(data.status_name);

                if (data.is_solved) {
                    btn.innerHTML = '<i class="fas fa-check mr-1"></i> Dialihkan ke Riwayat...';
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1200);
                    return;
                }

                setTimeout(() => location.reload(), 1000);

            } else {
                showToast('Gagal', data.message || 'Terjadi kesalahan saat menyimpan.', true);
            }
        } catch (err) {
            console.error(err);
            showToast('Error', 'Gagal terhubung ke server.', true);
        } finally {
            if (!btn.innerHTML.includes('Dialihkan')) {
                btn.disabled = false;
                btn.innerHTML = '<span>Simpan Laporan</span>';
            }
        }
    }

    function showToast(title, message, isError = false) {
        clearTimeout(toastTimeout);
        const toast = document.getElementById('toastNotification');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const iconBg = document.getElementById('toastIconBg');
        const icon = document.getElementById('toastIcon');

        toastTitle.textContent = title;
        toastMessage.textContent = message;

        if (isError) {
            iconBg.className = 'w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0';
            icon.className = 'fas fa-exclamation-triangle text-base';
        } else {
            iconBg.className = 'w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0';
            icon.className = 'fas fa-check text-base';
        }

        toast.classList.remove('translate-y-[-20px]', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');

        toastTimeout = setTimeout(() => hideToast(), 4500);
    }

    function hideToast() {
        const toast = document.getElementById('toastNotification');
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-[-20px]', 'opacity-0', 'pointer-events-none');
    }

    function updateStatusBadge(statusName) {
        const badge = document.getElementById('badgeTicketStatus');
        if (badge) {
            badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> ${statusName.toUpperCase()}`;
        }
    }

    // ================= CHAT REALTIME =================
    loadDetailMessages();

    if (window.Echo) {
        window.Echo.private(`tickets.${ticketId}.chat`)
            .listen('.message.sent', (event) => {
                appendDetailBubble({
                    id: event.id,
                    user_name: event.user_name,
                    body: event.body,
                    created_at: event.created_at,
                    is_me: event.user_id === currentUserId
                });
                scrollDetailChatToBottom();
            })
            .listen('.ticket.status.updated', (event) => {
                showToast('Pembaruan Status', `Status tiket diubah menjadi: ${event.status_name}`);
                updateStatusBadge(event.status_name);
            });
    }

    async function loadDetailMessages() {
        const container = document.getElementById('detailChatContainer');
        container.innerHTML = '<div class="text-center py-6 text-xs text-gray-400 font-medium">Memuat obrolan...</div>';

        try {
            const res = await fetch(`/tickets/${ticketId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

            const data = await res.json();
            const messages = Array.isArray(data) ? data : (data.messages || []);

            container.innerHTML = '';
            if (messages.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-xs text-gray-400">Belum ada percakapan pada tiket ini.</div>';
                return;
            }

            messages.forEach(msg => appendDetailBubble(msg));
            scrollDetailChatToBottom();

        } catch (err) {
            console.error('Error loadDetailMessages:', err);
            container.innerHTML = `
                <div class="text-center py-6 text-xs text-red-500">
                    Gagal memuat pesan. 
                    <button type="button" onclick="loadDetailMessages()" class="underline ml-1 text-blue-600 font-semibold">Coba lagi</button>
                </div>
            `;
        }
    }

    async function sendDetailMessage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const input = document.getElementById('detailChatInput');
        const text = input.value.trim();

        if (!text) return false;
        input.value = '';

        try {
            const res = await fetch(`/tickets/${ticketId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ body: text })
            });

            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

            const data = await res.json();
            if (data.success && data.message) {
                appendDetailBubble(data.message);
                scrollDetailChatToBottom();
            }
        } catch (err) {
            console.error('Gagal mengirim pesan:', err);
        }

        return false;
    }

    function appendDetailBubble(msg) {
        const container = document.getElementById('detailChatContainer');
        
        if (container.children.length === 1 && (container.textContent.includes('Belum ada') || container.textContent.includes('Memuat'))) {
            container.innerHTML = '';
        }

        const bubble = document.createElement('div');
        bubble.className = `flex flex-col ${msg.is_me ? 'items-end' : 'items-start'}`;

        bubble.innerHTML = `
            <span class="text-[10px] text-gray-400 mb-0.5 px-1 font-medium">${msg.is_me ? 'Anda' : msg.user_name}</span>
            <div class="max-w-[85%] rounded-2xl px-3.5 py-2 text-xs leading-relaxed shadow-sm ${
                msg.is_me 
                    ? 'bg-[#2d789e] text-white rounded-tr-none' 
                    : 'bg-white text-slate-800 border border-slate-200 rounded-tl-none'
            }">
                ${msg.body}
            </div>
            <span class="text-[9px] text-gray-400 mt-0.5 px-1">${msg.created_at}</span>
        `;

        container.appendChild(bubble);
    }

    function scrollDetailChatToBottom() {
        const container = document.getElementById('detailChatContainer');
        container.scrollTop = container.scrollHeight;
    }
</script>
@endsection