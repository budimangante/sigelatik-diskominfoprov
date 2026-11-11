@extends('components.b-master')

@section('title', 'Chat Teknisi')
@section('subtitle', 'Komunikasi langsung dengan teknisi penanganan tiket Anda')

@section('content')
<div class="min-h-screen bg-[#f3f6f9] p-5 font-sans md:p-8">
    <!-- Header halaman -->
    <div class="mb-5 flex items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>LAYANAN PENGGUNA</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Konsultasi Teknis</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Chat Penanganan Tiket</h1>
            <p class="mt-1 max-w-2xl text-xs leading-relaxed text-slate-500 md:text-sm">Kirimkan pertanyaan, informasi tambahan, atau koordinasi langsung kepada teknisi penanggung jawab.</p>
        </div>
    </div>

    <!-- Workspace chat: daftar tiket dan percakapan selalu terpisah -->
    <section class="grid h-[calc(100vh-220px)] min-h-[560px] grid-cols-1 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:grid-cols-[290px_minmax(0,1fr)] lg:grid-cols-[315px_minmax(0,1fr)]">
        <!-- Panel daftar tiket -->
        <aside class="flex min-h-0 flex-col border-b border-slate-200 bg-white md:border-b-0 md:border-r">
            <div class="border-b border-slate-100 p-3.5">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><i class="fas fa-search text-xs"></i></span>
                    <input type="text" id="searchTicketInput" oninput="filterTicketCards(this.value)" placeholder="Cari nomor tiket atau keluhan..." autocomplete="off" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-9 pr-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#347fa5] focus:ring-2 focus:ring-[#347fa5]/15">
                </div>
                <p class="mt-2 px-1 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Percakapan tiket Anda</p>
            </div>

            <div id="ticketCardsList" class="min-h-0 flex-1 divide-y divide-slate-100 overflow-y-auto">
                @forelse($tickets as $t)
                    @php
                        $isActive = ($activeTicket && $activeTicket->id === $t->id);
                        $tNumber = $t->ticket_number ?? ('TCK-' . $t->id);
                        $techs = $t->assigned_technicians;
                        $hasTech = count($techs) > 0;
                        $techLabel = $hasTech ? $techs->pluck('name')->implode(', ') : 'Belum Ditugaskan';
                        $statusRaw = strtolower($t->status->name ?? 'new');
                        if (str_contains($statusRaw, 'selesai') || str_contains($statusRaw, 'solved')) {
                            $badgeCls = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                        } elseif (str_contains($statusRaw, 'pending') || str_contains($statusRaw, 'jeda')) {
                            $badgeCls = 'border-amber-200 bg-amber-50 text-amber-700';
                        } elseif (str_contains($statusRaw, 'batal') || str_contains($statusRaw, 'cancel') || str_contains($statusRaw, 'tolak')) {
                            $badgeCls = 'border-rose-200 bg-rose-50 text-rose-700';
                        } else {
                            $badgeCls = 'border-blue-200 bg-blue-50 text-[#347fa5]';
                        }
                    @endphp
                    <a href="{{ route('user.chat', ['ticket_id' => $t->id]) }}" class="ticket-list-item relative block border-l-[3px] p-3.5 transition hover:bg-slate-50 {{ $isActive ? 'border-l-[#347fa5] bg-blue-50/60' : 'border-l-transparent' }}" data-search="{{ strtolower($tNumber . ' ' . $t->subject . ' ' . $techLabel) }}">
                        <div class="mb-1.5 flex items-center justify-between gap-2">
                            <span class="font-mono text-[10px] font-bold text-[#347fa5]">{{ $tNumber }}</span>
                            <span class="rounded-full border px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide {{ $badgeCls }}">{{ $t->status->name ?? 'BARU' }}</span>
                        </div>
                        <h3 class="line-clamp-2 text-xs font-bold leading-snug text-slate-800">{{ $t->subject }}</h3>
                        <div class="mt-2 flex items-center justify-between gap-2 text-[10px] text-slate-400">
                            <span class="flex min-w-0 items-center gap-1.5 truncate">
                                <i class="fas fa-user-cog shrink-0 {{ $hasTech ? 'text-[#347fa5]' : 'text-slate-400' }}"></i>
                                <span class="truncate {{ $hasTech ? 'font-semibold text-slate-600' : 'italic' }}">{{ $techLabel }}</span>
                            </span>
                            @if($t->unread_count > 0)
                                <span class="shrink-0 rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-extrabold text-white shadow-sm">{{ $t->unread_count }}</span>
                            @elseif($t->latest_message)
                                <span class="shrink-0 font-mono text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($t->latest_message->created_at)->format('H:i') }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        <i class="far fa-comments mb-2 block text-2xl text-slate-300"></i>
                        Belum ada tiket keluhan yang Anda ajukan.
                    </div>
                @endforelse
                <div id="noTicketSearchState" class="hidden p-6 text-center text-xs text-slate-400">Tidak ada tiket yang cocok dengan pencarian.</div>
            </div>
        </aside>

        <!-- Panel percakapan aktif -->
        <div class="flex min-h-0 min-w-0 flex-col bg-[#fbfcfd]">
            @if($activeTicket)
                <header class="flex items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3.5 md:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#347fa5] text-sm font-extrabold text-white shadow-sm"><i class="fas fa-headset"></i></div>
                        <div class="min-w-0">
                            <h2 class="truncate text-xs font-extrabold text-slate-800 md:text-sm">{{ $activeTicket->subject }}</h2>
                            <p class="mt-0.5 truncate text-[10px] text-slate-500"><span class="font-mono font-bold text-[#347fa5]">{{ $activeTicket->ticket_number ?? ('TCK-' . $activeTicket->id) }}</span><span class="mx-1.5 text-slate-300">·</span>Ditangani oleh: <span class="font-semibold text-slate-700">{{ count($activeTicket->assigned_technicians) > 0 ? $activeTicket->assigned_technicians->pluck('name')->implode(', ') : 'Menunggu Penugasan Teknisi' }}</span></p>
                        </div>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wide text-slate-600 md:text-[10px]">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ $activeTicket->status->name ?? 'BARU' }}
                    </span>
                </header>

                <div id="userChatFeed" aria-live="polite" class="min-h-0 flex-1 space-y-3.5 overflow-y-auto bg-[#f8fafc] p-4 md:p-5">
                    @forelse($messages as $msg)
                        @php $isMe = ((int) $msg->user_id === (int) auth()->id()); @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <span class="mb-0.5 px-1 text-[10px] font-medium text-slate-400">{{ $isMe ? 'Anda' : $msg->sender_name }}</span>
                            <div class="max-w-[88%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-sm md:max-w-[78%] {{ $isMe ? 'rounded-tr-sm bg-[#347fa5] text-white' : 'rounded-tl-sm border border-slate-200 bg-white text-slate-800' }}">{{ $msg->body }}</div>
                            <span class="mt-1 px-1 font-mono text-[9px] text-slate-400">{{ \Carbon\Carbon::parse($msg->created_at)->format('H:i') }}</span>
                        </div>
                    @empty
                        <div id="emptyChatState" class="flex h-full min-h-[180px] flex-col items-center justify-center text-center text-xs text-slate-400">
                            <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-xl text-[#347fa5]"><i class="far fa-paper-plane"></i></span>
                            <span class="font-semibold text-slate-500">Mulai percakapan dengan teknisi untuk tiket ini.</span>
                            <span class="mt-1 text-[11px]">Pesan Anda akan muncul di area ini.</span>
                        </div>
                    @endforelse
                </div>

                <div id="chatErrorMessage" role="alert" class="hidden border-t border-rose-100 bg-rose-50 px-4 py-2 text-[11px] text-rose-700"></div>
                <form id="chatSendForm" onsubmit="sendUserMessage(event)" class="flex items-center gap-2 border-t border-slate-200 bg-white p-3 md:p-3.5">
                    <input type="text" id="userChatMessageInput" placeholder="Tulis pesan..." autocomplete="off" class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-[#347fa5] focus:ring-2 focus:ring-[#347fa5]/15">
                    <button type="submit" id="btnSubmitChat" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#347fa5] px-4 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-[#286a8d] disabled:cursor-not-allowed disabled:opacity-50 md:px-5">
                        <i class="fas fa-paper-plane text-[11px]"></i><span>Kirim</span>
                    </button>
                </form>
            @else
                <div class="flex min-h-[320px] flex-1 flex-col items-center justify-center p-8 text-center">
                    <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-2xl text-[#347fa5]"><i class="far fa-comments"></i></span>
                    <h3 class="text-sm font-extrabold text-slate-800">Pilih tiket untuk memulai chat</h3>
                    <p class="mt-1 max-w-sm text-xs leading-relaxed text-slate-400">Pilih salah satu tiket di panel kiri untuk melihat percakapan atau mengirim pesan kepada teknisi.</p>
                </div>
            @endif
        </div>
    </section>
</div>

<script>
    const activeTicketId = {{ $activeTicket ? (int) $activeTicket->id : 'null' }};
    const currentUserId = {{ auth()->id() ?? 'null' }};

    function scrollChatToBottom() {
        const feed = document.getElementById('userChatFeed');
        if (feed) feed.scrollTop = feed.scrollHeight;
    }
    scrollChatToBottom();

    function filterTicketCards(query) {
        const text = (query || '').toLowerCase().trim();
        const items = document.querySelectorAll('.ticket-list-item');
        let visible = 0;
        items.forEach(item => {
            const match = (item.getAttribute('data-search') || '').includes(text);
            item.classList.toggle('hidden', !match);
            if (match) visible++;
        });
        const emptyState = document.getElementById('noTicketSearchState');
        if (emptyState) emptyState.classList.toggle('hidden', visible > 0 || items.length === 0);
    }

    async function sendUserMessage(e) {
        e.preventDefault();
        const input = document.getElementById('userChatMessageInput');
        const btn = document.getElementById('btnSubmitChat');
        const errorBox = document.getElementById('chatErrorMessage');
        const text = input?.value.trim() || '';
        if (!text || !activeTicketId || !input || !btn) return;

        const previousValue = text;
        input.value = '';
        btn.disabled = true;
        if (errorBox) { errorBox.textContent = ''; errorBox.classList.add('hidden'); }

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
            if (!res.ok || !data.success || !data.message) {
                throw new Error(data.message || 'Pesan belum berhasil dikirim. Silakan coba kembali.');
            }
            appendChatBubble(data.message);
            scrollChatToBottom();
        } catch (err) {
            input.value = previousValue;
            if (errorBox) {
                errorBox.textContent = err.message || 'Terjadi kendala saat mengirim pesan.';
                errorBox.classList.remove('hidden');
            }
            console.error('Gagal mengirim pesan chat:', err);
        } finally {
            btn.disabled = false;
            input.focus();
        }
    }

    // Buat elemen pesan melalui textContent agar isi pesan tidak dieksekusi sebagai HTML.
    function appendChatBubble(msg) {
        const feed = document.getElementById('userChatFeed');
        const emptyState = document.getElementById('emptyChatState');
        if (!feed) return;
        if (emptyState) emptyState.remove();

        const isMe = (currentUserId !== null && Number(msg.user_id) === Number(currentUserId)) || msg.is_me === true;
        const wrap = document.createElement('div');
        wrap.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'}`;

        const sender = document.createElement('span');
        sender.className = 'mb-0.5 px-1 text-[10px] font-medium text-slate-400';
        sender.textContent = isMe ? 'Anda' : (msg.sender_name || msg.user_name || 'Teknisi');

        const bubble = document.createElement('div');
        bubble.className = `max-w-[88%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed shadow-sm md:max-w-[78%] ${isMe ? 'rounded-tr-sm bg-[#347fa5] text-white' : 'rounded-tl-sm border border-slate-200 bg-white text-slate-800'}`;
        bubble.textContent = msg.body || '';

        const time = document.createElement('span');
        time.className = 'mt-1 px-1 font-mono text-[9px] text-slate-400';
        time.textContent = msg.created_at || 'Baru saja';

        wrap.append(sender, bubble, time);
        feed.appendChild(wrap);
    }

    if (window.Echo && activeTicketId) {
        window.Echo.private(`tickets.${activeTicketId}.chat`)
            .listen('.message.sent', (event) => {
                if (Number(event.user_id) !== Number(currentUserId)) {
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
