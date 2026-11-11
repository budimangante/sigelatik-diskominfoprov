@extends('components.b-master')


@php
    // Inisialisasi fallback aman agar tidak memicu Undefined Variable
    $createdAt = isset($createdAt) ? $createdAt : ($ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at) : now());
    $finishTime = isset($finishTime) ? $finishTime : ($ticket->solved_at ? \Carbon\Carbon::parse($ticket->solved_at) : ($ticket->cancelled_at ? \Carbon\Carbon::parse($ticket->cancelled_at) : now()));
    
    // Perhitungan SLA fallback jika tidak di-pass dari controller
    $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
    $taskMinutes = 0;
    if ($task) {
        $taskMinutes = (int) DB::table('ticket_task_activities')
            ->where('ticket_task_id', $task->id)
            ->sum('target_minutes_snapshot');
    }
    $totalMinutes = $taskMinutes > 0 ? $taskMinutes : (int) ($ticket->service?->sla_minutes ?? 240);
    $slaHours = isset($slaHours) ? $slaHours : round($totalMinutes / 60, 1);
    
    $startTime = isset($startTime) ? $startTime : ($task?->started_at ? \Carbon\Carbon::parse($task->started_at) : $createdAt);
    $elapsedMinutes = max(0, $startTime->diffInMinutes($finishTime));
    $slaProgressPercent = isset($slaProgressPercent) ? $slaProgressPercent : min(100, round(($elapsedMinutes / max(1, $totalMinutes)) * 100));
    $isSlaOverdue = isset($isSlaOverdue) ? $isSlaOverdue : ($elapsedMinutes >= $totalMinutes);

    $ticketCode = $ticket->ticket_number ?? ('HD-2610-00' . $ticket->id);
    $reporterName = $ticket->user->name ?? 'Pelapor';
    $reporterOrg = $ticket->user->organization ?? 'Dinas Terkait';
    $statusName = $ticket->status->name ?? 'SELESAI';
    $statusLower = strtolower($statusName);

    if (str_contains($statusLower, 'solved') || str_contains($statusLower, 'selesai')) {
        $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
        $dotBg = 'bg-emerald-500';
    } elseif (str_contains($statusLower, 'cancel') || str_contains($statusLower, 'batal') || str_contains($statusLower, 'tolak')) {
        $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';
        $dotBg = 'bg-rose-500';
    } else {
        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
        $dotBg = 'bg-blue-600';
    }
@endphp

@section('content')
<div class="p-5 md:p-7 bg-[#f3f5f7] min-h-screen font-sans">
    
    <!-- Tombol Kembali -->
    <div class="mb-5">
        <a href="{{ route('teknisi.history') }}" class="inline-flex items-center gap-2 text-[11px] font-bold text-[#397b9d] hover:text-[#245f7d] hover:underline">
            <i class="fas fa-chevron-left text-[11px]"></i>
            <span>Kembali ke daftar history</span>
        </a>
    </div>

    <!-- Card Header Tiket -->
    <div class="bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.05)] border border-[#dce4eb] p-5 md:p-6 mb-5">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <span class="text-[10px] font-mono font-bold text-[#397b9d] block mb-1">{{ $ticketCode }}</span>
                <h1 class="text-lg md:text-xl font-extrabold text-[#253b4c] leading-snug">
                    {{ $ticket->subject ?? $ticket->title ?? 'Keluhan Tiket' }}
                </h1>
                <p class="text-[11px] md:text-xs text-slate-500 mt-1">
                    {{ $reporterOrg }} · {{ $reporterName }}
                </p>
            </div>
            
            <!-- Badge Status -->
            <div class="self-start">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold border {{ $badgeBg }} uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full {{ $dotBg }}"></span>
                    {{ strtoupper($statusName) }}
                </span>
            </div>
        </div>
    </div>

    <!-- 4 Tombol Tab Navigasi -->
    <div class="flex items-center gap-2 mb-5 overflow-x-auto pb-1">
        <button type="button" 
                onclick="switchTab('ticket')" 
                id="btn-tab-ticket" 
                class="tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition shadow-sm bg-[#2d789e] text-white whitespace-nowrap">
            History Ticket
        </button>

        <button type="button" 
                onclick="switchTab('chat')" 
                id="btn-tab-chat" 
                class="tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition bg-white border border-[#dce4eb] text-slate-600 hover:bg-slate-50 whitespace-nowrap">
            History Chat
        </button>

        <button type="button" 
                onclick="switchTab('sla')" 
                id="btn-tab-sla" 
                class="tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition bg-white border border-[#dce4eb] text-slate-600 hover:bg-slate-50 whitespace-nowrap">
            History SLA
        </button>

        <button type="button" 
                onclick="switchTab('forward')" 
                id="btn-tab-forward" 
                class="tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition bg-white border border-[#dce4eb] text-slate-600 hover:bg-slate-50 whitespace-nowrap">
            History Forward
        </button>
    </div>

    <!-- ================= PANEL 1: HISTORY TICKET ================= -->
    <div id="panel-ticket" class="tab-panel bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.04)] border border-[#dce4eb] p-5 md:p-7">
        <div class="relative pl-6 border-l-2 border-slate-200 space-y-8 ml-2">
            
            <!-- Event 1: Tiket Dibuat -->
            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $createdAt->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">Tiket dibuat</h4>
                <p class="text-xs text-slate-600 mt-1">
                    Oleh {{ $reporterName }} ({{ $reporterOrg }}) · layanan {{ $ticket->service->name ?? 'Layanan Teknis' }}
                </p>
            </div>

            <!-- Event Dinamis: Status Histories -->
            @if($ticket->statusHistories && $ticket->statusHistories->count() > 0)
                @foreach($ticket->statusHistories as $history)
                    <div class="relative">
                        <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                        <span class="text-xs font-medium text-slate-400 block mb-0.5">
                            {{ $history->created_at ? \Carbon\Carbon::parse($history->created_at)->translatedFormat('d M Y, H:i') : $createdAt->translatedFormat('d M Y, H:i') }}
                        </span>
                        <h4 class="text-sm font-bold text-slate-900 leading-snug">
                            {{ $history->user->name ?? 'Helpdesk / Teknisi' }}
                        </h4>
                        <p class="text-xs text-slate-600 mt-1">
                            {{ $history->note ?? 'Pembaruan tindakan pengerjaan tiket oleh teknisi.' }}
                        </p>
                    </div>
                @endforeach
            @else
                <div class="relative">
                    <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                    <span class="text-xs font-medium text-slate-400 block mb-0.5">
                        {{ $finishTime->translatedFormat('d M Y, H:i') }}
                    </span>
                    <h4 class="text-sm font-bold text-slate-900 leading-snug">
                        Tiket Diselesaikan
                    </h4>
                    <p class="text-xs text-slate-600 mt-1">
                        Status akhir tiket: {{ $statusName }}.
                    </p>
                </div>
            @endif

        </div>
    </div>

    <!-- ================= PANEL 2: HISTORY CHAT ================= -->
    <div id="panel-chat" class="tab-panel hidden bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.04)] border border-[#dce4eb] p-5 md:p-7">
        <div class="space-y-4 max-w-2xl">
            @if($ticket->messages && $ticket->messages->count() > 0)
                @foreach($ticket->messages as $msg)
                    @php
                        $isMe = ($msg->user_id === auth()->id());
                        $msgTime = $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('H:i') : '';
                    @endphp
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                        <span class="text-xs font-semibold text-slate-600 block mb-1">
                            {{ $isMe ? 'Anda' : ($msg->user->name ?? $reporterName) }}
                        </span>
                        <p class="text-xs md:text-sm text-slate-800 leading-relaxed">
                            {{ $msg->body }}
                        </p>
                        <div class="text-[11px] text-slate-400 text-right mt-1 font-mono">
                            {{ $msgTime }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-10 text-xs text-slate-400">
                    Tidak ada riwayat percakapan chat pada tiket ini.
                </div>
            @endif
        </div>
    </div>

    <!-- ================= PANEL 3: HISTORY SLA ================= -->
    <div id="panel-sla" class="tab-panel hidden bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.04)] border border-[#dce4eb] p-5 md:p-7">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <span class="text-xs text-slate-500 block">Target SLA Penanganan</span>
                <span class="text-base font-extrabold text-slate-900">{{ $slaHours }} jam</span>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-500 block">Status SLA</span>
                <span class="text-base font-extrabold {{ $isSlaOverdue ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $isSlaOverdue ? 'Terlewati' : 'Terpenuhi Tepat Waktu' }}
                </span>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold text-[#1a3668]">
                    {{ $ticket->solved_at ? 'Tuntas' : 'Selesai' }}
                </span>
                <span class="text-[11px] text-slate-400">Target {{ $slaHours }} jam ({{ $slaProgressPercent }}%)</span>
            </div>
            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full {{ $isSlaOverdue ? 'bg-rose-500' : 'bg-[#1a3668]' }} rounded-full transition-all duration-500" 
                     style="width: {{ $slaProgressPercent }}%;"></div>
            </div>
        </div>

        <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-2">
            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $startTime->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">Timer Penanganan Dimulai</h4>
                <p class="text-xs text-slate-600 mt-1">Teknisi memulai penanganan dengan target {{ $slaHours }} jam.</p>
            </div>

            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full {{ $isSlaOverdue ? 'bg-rose-500' : 'bg-emerald-600' }} border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $finishTime->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">
                    Penanganan Selesai
                </h4>
                <p class="text-xs text-slate-600 mt-1">
                    Durasi pengerjaan: {{ $slaProgressPercent }}% dari alokasi waktu target.
                </p>
            </div>
        </div>
    </div>

    <!-- ================= PANEL 4: HISTORY FORWARD ================= -->
    <div id="panel-forward" class="tab-panel hidden bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.04)] border border-[#dce4eb] p-5 md:p-7">
        <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-2">
            @php
                $forwardHistory = $ticket->statusHistories ? $ticket->statusHistories->filter(function($h) {
                    $note = strtolower($h->note ?? '');
                    return str_contains($note, 'forward') || str_contains($note, 'teruskan');
                }) : collect();
            @endphp

            @forelse($forwardHistory as $fwd)
                <div class="relative">
                    <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                    <span class="text-xs font-medium text-slate-400 block mb-0.5">
                        {{ $fwd->created_at ? \Carbon\Carbon::parse($fwd->created_at)->translatedFormat('d M Y, H:i') : $createdAt->translatedFormat('d M Y, H:i') }}
                    </span>
                    <h4 class="text-sm font-bold text-slate-900 leading-snug">
                        Log Eskalasi / Forward
                    </h4>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        {{ $fwd->note }}
                    </p>
                </div>
            @empty
                <div class="text-center py-10 text-xs text-slate-400">
                    Tiket ini ditangani langsung secara tuntas dan tidak pernah diteruskan ke unit kerja lain.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- JAVASCRIPT: TAB SWITCHER -->
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition bg-white border border-[#dce4eb] text-slate-600 hover:bg-slate-50 whitespace-nowrap';
        });

        const activePanel = document.getElementById(`panel-${tabName}`);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        const activeBtn = document.getElementById(`btn-tab-${tabName}`);
        if (activeBtn) {
            activeBtn.className = 'tab-btn px-4 py-2 rounded-xl text-[11px] font-bold transition shadow-sm bg-[#2d789e] text-white whitespace-nowrap';
        }
    }
</script>
@endsection