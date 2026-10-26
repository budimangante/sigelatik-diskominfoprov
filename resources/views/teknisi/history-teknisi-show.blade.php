@extends('components.b-master')

@section('content')
<div class="p-6 md:p-8 bg-[#f8fafc] min-h-screen font-sans">
    
    <!-- Tombol Kembali -->
    <div class="mb-5">
        <a href="{{ route('teknisi.history') }}" class="inline-flex items-center gap-1.5 text-xs md:text-sm font-semibold text-[#1a3668] hover:underline">
            <i class="fas fa-chevron-left text-[11px]"></i>
            <span>Kembali ke daftar history</span>
        </a>
    </div>

    @php
        $ticketCode = $ticket->ticket_number ?? ('HD-2610-00' . $ticket->id);
        $reporterName =$ticket->user->name ?? 'Pelapor';
        $reporterOrg =$ticket->user->organization ?? 'Dinas Terkait';
        $statusName =$ticket->status->name ?? 'PROCESS';
        $statusLower = strtolower($statusName);

        if (str_contains($statusLower, 'solved') || str_contains($statusLower, 'selesai')) {
            $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';$dotBg = 'bg-emerald-500';
        } elseif (str_contains($statusLower, 'cancel') || str_contains($statusLower, 'batal')) {
            $badgeBg = 'bg-rose-50 text-rose-700 border-rose-200';$dotBg = 'bg-rose-500';
        } else {
            $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';$dotBg = 'bg-blue-600';
        }
    @endphp

    <!-- Card Header Tiket -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <span class="text-xs font-mono font-semibold text-slate-400 block mb-1">{{ $ticketCode }}</span>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 leading-snug">
                    {{ $ticket->subject ?? $ticket->title ?? 'Keluhan Tiket' }}
                </h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">
                    {{ $reporterOrg }} · {{$reporterName }}
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
    <div class="flex items-center gap-2.5 mb-6 overflow-x-auto pb-1">
        <button type="button" 
                onclick="switchTab('ticket')" 
                id="btn-tab-ticket" 
                class="tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition shadow-sm bg-[#1a3668] text-white">
            History Ticket
        </button>

        <button type="button" 
                onclick="switchTab('chat')" 
                id="btn-tab-chat" 
                class="tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            History Chat
        </button>

        <button type="button" 
                onclick="switchTab('sla')" 
                id="btn-tab-sla" 
                class="tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            History SLA
        </button>

        <button type="button" 
                onclick="switchTab('forward')" 
                id="btn-tab-forward" 
                class="tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
            History Forward
        </button>
    </div>

    <!-- ================= PANEL 1: HISTORY TICKET ================= -->
    <div id="panel-ticket" class="tab-panel bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
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

            <!-- Event 2: Diteruskan ke Helpdesk Bidang -->
            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $createdAt->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">Diteruskan ke Helpdesk Bidang</h4>
                <p class="text-xs text-slate-600 mt-1">
                    Kategori dan SLA ditetapkan otomatis.
                </p>
            </div>

            <!-- Event Dinamis: Status Histories -->
            @if($ticket->statusHistories &&$ticket->statusHistories->count() > 0)
                @foreach($ticket->statusHistories as $history)
                    <div class="relative">
                        <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                        <span class="text-xs font-medium text-slate-400 block mb-0.5">
                            {{ $ticket->updated_at ? \Carbon\Carbon::parse($ticket->updated_at)->translatedFormat('d M Y, H:i') :$createdAt->translatedFormat('d M Y, H:i') }}
                        </span>
                        <h4 class="text-sm font-bold text-slate-900 leading-snug">
                            Ditugaskan ke {{ $history->user->name ?? (auth()->user()->name ?? 'Teknisi') }}
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
                        {{ $createdAt->translatedFormat('d M Y, H:i') }}
                    </span>
                    <h4 class="text-sm font-bold text-slate-900 leading-snug">
                        Ditugaskan ke {{ auth()->user()->name ?? 'Bima Prakoso' }}
                    </h4>
                    <p class="text-xs text-slate-600 mt-1">
                        Status berubah menjadi {{ $statusName }}.
                    </p>
                </div>
            @endif

        </div>
    </div>

    <!-- ================= PANEL 2: HISTORY CHAT ================= -->
    <div id="panel-chat" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        <div class="space-y-4 max-w-2xl">
            @if($ticket->messages &&$ticket->messages->count() > 0)
                @foreach($ticket->messages as $msg)
                    @php
                        $isMe = ($msg->user_id === auth()->id());$msgTime = $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('H:i') : '';
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
                <!-- Tampilan Pesan Contoh Sesuai Screenshot -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 shadow-sm">
                    <span class="text-xs font-semibold text-slate-600 block mb-1">
                        {{ $reporterName }}
                    </span>
                    <p class="text-xs md:text-sm text-slate-800 leading-relaxed">
                        Pak, apakah akun email saya sudah bisa dicoba login lagi?
                    </p>
                    <div class="text-[11px] text-slate-400 text-right mt-1 font-mono">
                        09:05
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- ================= PANEL 3: HISTORY SLA ================= -->
    <div id="panel-sla" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        
        <!-- Header SLA -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <span class="text-xs text-slate-500 block">Target SLA</span>
                <span class="text-base font-extrabold text-slate-900">{{ $slaHours }} jam</span>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-500 block">Status SLA</span>
                <span class="text-base font-extrabold {{ $isSlaOverdue ? 'text-rose-600' : 'text-slate-900' }}">
                    {{ $isSlaOverdue ? 'Terlewati' : ($ticket->solved_at ? 'Selesai' : 'Berjalan') }}
                </span>
            </div>
        </div>

        <!-- Progress Bar SLA -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold text-[#1a3668]">
                    {{ $ticket->solved_at ? 'Tuntas' : 'Berjalan' }}
                </span>
                <span class="text-[11px] text-slate-400">target {{ $slaHours }} jam</span>
            </div>
            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-[#1a3668] rounded-full transition-all duration-500" 
                     style="width: {{ $slaProgressPercent }}%;"></div>
            </div>
        </div>

        <!-- Timeline SLA -->
        <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-2">
            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $createdAt->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">Timer SLA dimulai</h4>
                <p class="text-xs text-slate-600 mt-1">Target penyelesaian {{ $slaHours }} jam.</p>
            </div>

            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $createdAt->translatedFormat('d M Y, H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">
                    Progres {{ $slaProgressPercent }}% dari target
                </h4>
            </div>
        </div>
    </div>

    <!-- ================= PANEL 4: HISTORY FORWARD ================= -->
    <div id="panel-forward" class="tab-panel hidden bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-2">
            <div class="relative">
                <div class="absolute -left-[31px] top-1 w-3.5 h-3.5 rounded-full bg-[#1a3668] border-2 border-white shadow-sm"></div>
                <span class="text-xs font-medium text-slate-400 block mb-0.5">
                    {{ $createdAt->translatedFormat('d M Y') }} · {{$createdAt->format('H:i') }}
                </span>
                <h4 class="text-sm font-bold text-slate-900 leading-snug">
                    Pengajuan forward ke Helpdesk Bidang
                </h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Alasan: Masalah ada pada server email pusat, di luar kewenangan bidang jaringan.
                </p>

                <div class="mt-4">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">
                        Menunggu keputusan Helpdesk Bidang
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- JAVASCRIPT: TAB SWITCHER -->
<script>
    function switchTab(tabName) {
        // Sembunyikan semua panel
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // Reset semua tombol tab ke style inaktif
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition bg-white border border-slate-200 text-slate-700 hover:bg-slate-50';
        });

        // Tampilkan panel yang dipilih
        const activePanel = document.getElementById(`panel-${tabName}`);
        if (activePanel) {
            activePanel.classList.remove('hidden');
        }

        // Berikan style aktif (Navy) ke tombol yang dipilih
        const activeBtn = document.getElementById(`btn-tab-${tabName}`);
        if (activeBtn) {
            activeBtn.className = 'tab-btn px-5 py-2.5 rounded-xl text-xs md:text-sm font-semibold transition shadow-sm bg-[#1a3668] text-white';
        }
    }
</script>
@endsection