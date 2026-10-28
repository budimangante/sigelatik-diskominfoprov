@extends('components.b-master')

@section('title', 'Chat Teknisi')
@section('subtitle', 'Komunikasi langsung dengan teknisi penanganan tiket Anda')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
            <span class="text-[#1a3668]">LAYANAN PENGGUNA</span>
            <span class="text-slate-300">•</span>
            <span>KONSULTASI TEKNIS</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-[#1a3668] tracking-tight">
            Chat Penanganan Tiket
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Kirimkan pertanyaan, informasi tambahan, atau koordinasi langsung kepada teknisi penanggung jawab.
        </p>
    </div>

    <!-- Container Utama Split-View Chat -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm flex flex-col md:flex-row overflow-hidden h-[700px]">
        
        <!-- ================= PANEL KIRI: DAFTAR TIKET PENGGUNA ================= -->
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-100 flex flex-col bg-slate-50/40 shrink-0">
            
            <!-- Pencarian Tiket Live -->
            <div class="p-4 border-b border-slate-100 bg-white">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" 
                           id="searchTicketInput" 
                           oninput="filterTicketCards(this.value)"
                           placeholder="Cari nomor tiket atau keluhan..." 
                           class="w-full bg-slate-100/80 border border-slate-200/70 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                </div>
            </div>

            <!-- List Tiket -->
            <div id="ticketCardsList" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($tickets as $t)
                    @php
                        $isActive = ($activeTicket && $activeTicket->id === $t->id);
                        $tNumber = $t->ticket_number ?? ('TCK-' . $t->id);
                        $techs = $t->assigned_technicians;
                        $hasTech = count($techs) > 0;
                        $techLabel = $hasTech ? $techs->pluck('name')->implode(', ') : 'Belum Ditugaskan';

                        $statusRaw = strtolower($t->status->name ?? 'new');
                        if (str_contains($statusRaw, 'selesai') || str_contains($statusRaw, 'solved')) {
                            $badgeCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        } elseif (str_contains($statusRaw, 'pending') || str_contains($statusRaw, 'jeda')) {
                            $badgeCls = 'bg-amber-50 text-amber-700 border-amber-200';
                        } else {
                            $badgeCls = 'bg-blue-50 text-[#1a3668] border-blue-200';
                        }
                    @endphp

                    <a href="{{ route('user.chat', ['ticket_id' => $t->id]) }}" 
                       class="ticket-list-item block p-4 transition relative hover:bg-slate-50 {{ $isActive ? 'bg-blue-50/50 border-l-4 border-l-[#1a3668]' : '' }}"
                       data-search="{{ strtolower($tNumber . ' ' . $t->subject . ' ' . $techLabel) }}">
                        
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[11px] font-mono font-bold text-[#1a3668]">
                                {{ $tNumber }}
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-bold border uppercase tracking-wider {{ $badgeCls }}">
                                {{ $t->status->name ?? 'BARU' }}
                            </span>
                        </div>

                        <h4 class="text-xs font-bold text-slate-800 line-clamp-1 mb-1">
                            {{ $t->subject }}
                        </h4>

                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2">
                            <span class="flex items-center gap-1.5 truncate max-w-[180px]">
                                <i class="fas fa-user-cog text-[10px] {{ $hasTech ? 'text-blue-600' : 'text-slate-400' }}"></i>
                                <span class="{{ $hasTech ? 'font-semibold text-slate-600' : 'italic' }}">{{ $techLabel }}</span>
                            </span>

                            @if($t->unread_count > 0)
                                <span class="bg-rose-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-full shrink-0 shadow-xs">
                                    {{ $t->unread_count }}
                                </span>
                            @elseif($t->latest_message)
                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($t->latest_message->created_at)->format('H:i') }}
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <i class="far fa-comments text-2xl mb-2 text-slate-300 block"></i>
                        Belum ada tiket keluhan yang Anda ajukan.
                    </div>
                @endforelse
            </div>

        </div>

        <!-- ================= PANEL KANAN: WORKSPACE OBROLAN ================= -->
        <div class="flex-1 flex flex-col bg-white">
            
            @if($activeTicket)
                <!-- Header Chat Tiket Aktif -->
                <div class="p-4 border-b border-slate-100 bg-white flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1a3668] border border-blue-100 flex items-center justify-center font-bold text-base shrink-0">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-blue-600">
                                    {{ $activeTicket->ticket_number ?? ('TCK-' . $activeTicket->id) }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs font-bold text-slate-800 line-clamp-1 max-w-[320px]">
                                    {{ $activeTicket->subject }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Ditangani oleh: 
                                <span class="font-semibold text-slate-700">
                                    {{ count($activeTicket->assigned_technicians) > 0 ? $activeTicket->assigned_technicians->pluck('name')->implode(', ') : 'Menunggu Penugasan Teknisi' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider bg-slate-50 text-slate-600 border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $activeTicket->status->name ?? 'BARU' }}
                        </span>
                    </div>
                </div>

                <!-- Feed Pesan -->
                <div id="userChatFeed" class="flex-1 p-5 overflow-y-auto space-y-3.5 bg-[#f8fafc]/70">
                    @forelse($messages as $msg)
                        @php
                            $isMe = ($msg->user_id === auth()->id());
                        @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <span class="text-[10px] text-slate-400 mb-0.5 px-1 font-medium">
                                {{ $isMe ? 'Anda' : $msg->sender_name }}
                            </span>
                            <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-2xs {{ $isMe ? 'bg-[#1a3668] text-white rounded-tr-none' : 'bg-white text-slate-800 border border-slate-200/90 rounded-tl-none' }}">
                                {{ $msg->body }}
                            </div>
                            <span class="text-[9px] text-slate-400 mt-0.5 px-1 font-mono">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}
                            </span>
                        </div>
                    @empty
                        <div id="emptyChatState" class="text-center py-20 text-xs text-slate-400">
                            <i class="far fa-paper-plane text-3xl mb-3 text-slate-300 block"></i>
                            Belum ada percakapan pada tiket ini.<br>Ketikkan pesan di bawah untuk memulai koordinasi dengan tim teknisi.
                        </div>
                    @endforelse
                </div>

                <!-- Form Kirim Pesan -->
                <form id="chatSendForm" onsubmit="sendUserMessage(event)" class="p-3.5 bg-white border-t border-slate-100 flex items-center gap-2">
                    <input type="text" 
                           id="userChatMessageInput" 
                           placeholder="Tuliskan pesan atau koordinasi untuk teknisi..." 
                           autocomplete="off" 
                           class="flex-1 bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                    
                    <button type="submit" 
                            id="btnSubmitChat"
                            class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-3 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2 shrink-0">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span class="hidden sm:inline">Kirim</span>
                    </button>
                </form>

            @else
                <!-- State Kosong Jika Belum Ada Tiket yang Dipilih -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-[#f8fafc]/40">
                    <div class="w-16 h-16 rounded-full bg-blue-50 text-[#1a3668] flex items-center justify-center text-2xl mb-4 border border-blue-100">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Pilih Tiket untuk Memulai Chat</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm">
                        Silakan pilih tiket Anda di panel sebelah kiri untuk melihat percakapan atau memberikan tanggapan kepada teknisi terkait.
                    </p>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- ================= JAVASCRIPT LOGIC ================= -->
<script>
    const activeTicketId = {{ $activeTicket ? (int)$activeTicket->id : 'null' }};
    const currentUserId = {{ auth()->id() ?? 'null' }};

    // Auto-scroll ke pesan terbawah saat halaman dibuka
    function scrollChatToBottom() {
        const feed = document.getElementById('userChatFeed');
        if (feed) {
            feed.scrollTop = feed.scrollHeight;
        }
    }
    scrollChatToBottom();

    // Filter live list tiket di sidebar kiri
    function filterTicketCards(query) {
        const text = query.toLowerCase().trim();
        const items = document.querySelectorAll('.ticket-list-item');

        items.forEach(item => {
            const searchData = item.getAttribute('data-search') || '';
            if (searchData.includes(text)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Mengirim pesan chat
    async function sendUserMessage(e) {
        e.preventDefault();
        const input = document.getElementById('userChatMessageInput');
        const btn = document.getElementById('btnSubmitChat');
        const text = input.value.trim();

        if (!text || !activeTicketId) return;

        input.value = '';
        btn.disabled = true;

        try {
            const res = await fetch(`/tickets/${activeTicketId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ body: text })
            });

            const data = await res.json();

            if (data.success && data.message) {
                appendChatBubble(data.message);
                scrollChatToBottom();
            }
        } catch (err) {
            console.error('Gagal mengirim pesan chat:', err);
        } finally {
            btn.disabled = false;
            input.focus();
        }
    }

    // Menambahkan bubble pesan baru ke layar secara instan
    function appendChatBubble(msg) {
        const feed = document.getElementById('userChatFeed');
        const emptyState = document.getElementById('emptyChatState');
        if (emptyState) emptyState.remove();

        const isMe = (msg.user_id === currentUserId || msg.is_me);
        const bubbleWrap = document.createElement('div');
        bubbleWrap.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'}`;

        bubbleWrap.innerHTML = `
            <span class="text-[10px] text-slate-400 mb-0.5 px-1 font-medium">${isMe ? 'Anda' : (msg.sender_name || msg.user_name)}</span>
            <div class="max-w-[78%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-2xs ${isMe ? 'bg-[#1a3668] text-white rounded-tr-none' : 'bg-white text-slate-800 border border-slate-200/90 rounded-tl-none'}">
                ${msg.body}
            </div>
            <span class="text-[9px] text-slate-400 mt-0.5 px-1 font-mono">${msg.created_at || 'Baru saja'}</span>
        `;

        feed.appendChild(bubbleWrap);
    }

    // Mendengarkan siaran pesan realtime melalui Laravel Echo (jika aktif)
    if (window.Echo && activeTicketId) {
        window.Echo.private(`tickets.${activeTicketId}.chat`)
            .listen('.message.sent', (event) => {
                if (event.user_id !== currentUserId) {
                    appendChatBubble({
                        user_id: event.user_id,
                        sender_name: event.user_name,
                        body: event.body,
                        created_at: event.created_at,
                        is_me: false
                    });
                    scrollChatToBottom();
                }
            });
    }
</script>
@endsection