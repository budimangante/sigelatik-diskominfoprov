@extends('components.b-master') 

@section('content')
<div class="p-5 md:p-7 bg-[#f3f5f7] min-h-screen font-sans">
    <!-- Header Section -->
    <div class="mb-5">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
            <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
            <span>Helpdesk</span><span class="text-[#b8c7d0]">•</span><span>Teknisi</span>
        </div>
        <h1 class="mb-1 text-3xl text-[#1d405c] sm:text-[25px]">
            <span class="font-['StretchPro'] tracking-wider uppercase">Task Ticket</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e8f2f8] text-[#286f94] border border-[#cfe1ec] ml-2 align-middle">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span id="activeTicketCountBadge">{{ $tickets->count() }} Tiket Aktif</span>
            </span>
        </h1>
        <p class="text-[11px] md:text-xs text-slate-500 mt-1">Daftar tiket aktif yang ditugaskan kepada Anda beserta sisa waktu SLA.</p>
    </div>

    <!-- Alert Notifikasi -->
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

    <!-- Filter & Pencarian Live -->
    <div class="mb-5 bg-white border border-[#dce4eb] rounded-2xl p-3.5 shadow-[0_3px_12px_rgba(27,64,92,0.04)]">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text"
                   id="liveSearchInput"
                   placeholder="Ketik langsung nomor tiket, keluhan, pelapor, atau instansi (mis. 0142, Srikandi)..."
                   autocomplete="off"
                   class="w-full bg-[#f7f9fb] border border-[#dce4eb] rounded-xl pl-10 pr-10 py-3 text-[11px] md:text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] transition">
            <button type="button"
                    id="btnClearSearch"
                    onclick="resetLiveFilter()"
                    class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] gap-2.5 mt-2.5">
            <!-- Filter Status Real-time -->
            <div class="relative min-w-0">
                <select id="liveStatusFilter" class="w-full bg-white border border-[#dce4eb] rounded-xl px-3.5 py-2.5 text-[11px] md:text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] appearance-none cursor-pointer">
                    <option value="">Semua Status Aktif</option>
                    @if(isset($statuses))
                        @foreach ($statuses as $st)
                            <option value="{{ $st->id }}">Status: {{ $st->name }}</option>
                        @endforeach
                    @else
                        <option value="1">Status: New</option>
                        <option value="2">Status: Process</option>
                        <option value="3">Status: Pending</option>
                    @endif
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <!-- Filter Instansi / Dinas -->
            <div class="relative min-w-0">
                <select id="liveOrgFilter" class="w-full bg-white border border-[#dce4eb] rounded-xl px-3.5 py-2.5 text-[11px] md:text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] appearance-none cursor-pointer">
                    <option value="">Semua Instansi / Dinas</option>
                    @php
                        $uniqueOrgs = $tickets->pluck('user.organization')->filter()->unique();
                    @endphp
                    @foreach ($uniqueOrgs as $org)
                        <option value="{{ strtolower($org) }}">{{ $org }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <!-- Tombol Reset Filter Cepat -->
            <button type="button"
                    onclick="resetLiveFilter()"
                    class="px-4 py-2.5 bg-transparent hover:bg-slate-50 text-slate-500 hover:text-[#286f94] font-semibold rounded-xl text-[11px] transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fas fa-redo-alt text-[10px]"></i>
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- Daftar Tiket Aktif -->
    <div id="ticketCardsContainer" class="grid grid-cols-1 xl:grid-cols-2 gap-3.5">
        @if ($tickets->count() > 0)
            @foreach ($tickets as $ticket)
                @php
                    $ticketCode = $ticket->ticket_number ?? ('HD-2610-00' . $ticket->id);
                    $reporterName = $ticket->user->name ?? 'Pelapor';
                    $reporterOrg = $ticket->user->organization ?? 'Dinas Terkait';
                    $ticketSubject = $ticket->title ?? $ticket->subject ?? 'Keluhan Tiket';
                    $unread = $ticket->unread_messages_count ?? 0;

                    $statusRaw = strtolower($ticket->status->name ?? 'new');
                    if (str_contains($statusRaw, 'process') || str_contains($statusRaw, 'proses')) {
                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                        $dotBg = 'bg-amber-500';
                    } elseif (str_contains($statusRaw, 'pending')) {
                        $badgeBg = 'bg-purple-50 text-purple-700 border-purple-200';
                        $dotBg = 'bg-purple-500';
                    } else {
                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                        $dotBg = 'bg-blue-600';
                    }

                    $searchData = strtolower("{$ticketCode} {$ticketSubject} {$reporterName} {$reporterOrg}");
                @endphp

                <!-- KARTU TIKET -->
                <div class="ticket-card bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.05)] border border-[#dce4eb] p-4 md:p-5 relative hover:border-[#a9c6d8] hover:shadow-[0_7px_20px_rgba(27,64,92,0.08)] transition-all duration-200 min-w-0"
                     data-search="{{ $searchData }}"
                     data-status-id="{{ $ticket->ticket_status_id }}"
                     data-org="{{ strtolower($reporterOrg) }}">
                    
                    <div class="flex justify-between items-start mb-3">
                        <div class="pr-6">
                            <div class="text-[10px] font-mono font-bold text-[#397b9d] mb-1">{{ $ticketCode }}</div>
                            
                            <a href="{{ route('teknisi.tickets.show', $ticket) }}" class="text-sm md:text-[15px] font-bold text-[#253b4c] hover:text-[#286f94] transition-colors leading-snug block">
                                {{ $ticketSubject }}
                            </a>
                            <p class="text-[10px] text-slate-500 mt-1">{{ $reporterOrg }} • {{ $reporterName }}</p>
                        </div>
                        
                        <div class="{{ $badgeBg }} border px-3 py-1 rounded-full text-[10px] font-bold flex items-center gap-1.5 uppercase tracking-wider shrink-0 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotBg }}"></span>
                            {{ $ticket->status->name ?? 'NEW' }}
                        </div>
                    </div>

                    <!-- SLA Progress Bar (Sinkron dengan Bidang) -->
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        @if(($ticket->sla_status ?? 'not_started') === 'not_started')
                            <!-- 1. Kondisi: Belum Berjalan -->
                            <div class="flex justify-between items-center mb-1.5 text-xs">
                                <span class="font-bold text-slate-400 flex items-center gap-1.5">
                                    <i class="far fa-clock"></i> Belum berjalan
                                </span>
                                <span class="text-[11px] text-gray-400 font-mono">
                                    target {{ $ticket->target_sla_label ?? '4 jam' }} (0%)
                                </span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-slate-300" style="width: 0%;"></div>
                            </div>

                        @elseif(($ticket->sla_status ?? '') === 'paused')
                            <!-- 2. Kondisi: Dijeda (Pending) -->
                            <div class="flex justify-between items-center mb-1.5 text-xs">
                                <span class="font-bold text-amber-600 flex items-center gap-1.5">
                                    <i class="fas fa-pause-circle"></i> Dijeda ({{ $ticket->sla_progress }}%)
                                </span>
                                <span class="text-[11px] text-amber-700 font-mono font-semibold">
                                    target {{ $ticket->target_sla_label ?? '4 jam' }}
                                </span>
                            </div>
                            <div class="w-full h-2 bg-amber-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-amber-500 transition-all duration-500" style="width: {{ $ticket->sla_progress }}%;"></div>
                            </div>

                        @elseif(!empty($ticket->is_overdue) || ($ticket->sla_status ?? '') === 'overdue')
                            <!-- 3. Kondisi: SLA Terlewati -->
                            <div class="flex justify-between items-center mb-1.5 text-xs">
                                <span class="font-bold text-rose-600 flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle"></i> SLA Terlewati (100%)
                                </span>
                                <span class="text-[11px] text-rose-600 font-mono font-semibold">
                                    target {{ $ticket->target_sla_label ?? '4 jam' }}
                                </span>
                            </div>
                            <div class="w-full h-2 bg-rose-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-rose-500" style="width: 100%;"></div>
                            </div>

                        @else
                            <!-- 4. Kondisi: Sedang Berjalan -->
                            <div class="flex justify-between items-center mb-1.5 text-xs">
                                <span class="font-bold text-[#1e3a8a] flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                    Sedang Berjalan ({{ $ticket->sla_progress }}%)
                                </span>
                                <span class="text-[11px] text-gray-400 font-mono">
                                    target {{ $ticket->target_sla_label ?? '4 jam' }}
                                </span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-[#1e3a8a] transition-all duration-500" style="width: {{ $ticket->sla_progress }}%;"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Tombol Aksi di Kartu -->
                    <div class="mt-3.5 flex items-center justify-between gap-3">
                        <div>
                            @if(!$ticket->is_started)
                                <!-- Quick Action: Mulai Kerjakan Langsung dari Index -->
                                <form action="{{ route('teknisi.tickets.start', $ticket->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-[#2d789e] hover:bg-[#245f7d] text-white px-3.5 py-2 rounded-xl text-[11px] font-bold transition shadow-sm flex items-center gap-1.5">
                                        <i class="fas fa-play text-[10px]"></i>
                                        <span>Mulai Kerjakan</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('teknisi.tickets.show', $ticket) }}" 
                                   class="text-xs font-bold text-[#1a3668] hover:underline flex items-center gap-1">
                                    <span>Buka Lembar Kerja</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Tombol Chat Pelapor -->
                        <button type="button" 
                                onclick="openQuickChat({{ $ticket->id }}, '{{ $ticketCode }}', '{{ addslashes($ticketSubject) }}', '{{ addslashes($reporterName) }}')"
                                class="relative px-3 py-2 rounded-xl bg-[#edf5f9] text-[#286f94] hover:bg-[#e1eef5] text-[11px] font-bold flex items-center gap-2 transition border border-[#d7e7ef]">
                            <i class="fas fa-comment-dots text-xs"></i>
                            <span>Chat Pelapor</span>
                            
                            @if($unread > 0)
                                <span id="badge-ticket-{{ $ticket->id }}" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                    {{ $unread }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- State Hasil Live Search Tidak Ditemukan -->
        <div id="noMatchFoundState" class="hidden xl:col-span-2 bg-white rounded-2xl border border-dashed border-[#cfdbe4] p-10 text-center">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fas fa-search-minus"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-700">Tidak ada tiket yang sesuai</h3>
            <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Tidak ditemukan tiket aktif dengan kata kunci atau filter yang Anda pilih.</p>
            <button type="button" onclick="resetLiveFilter()" class="mt-4 inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline font-semibold">
                <i class="fas fa-undo text-[10px]"></i> Bersihkan filter pencarian
            </button>
        </div>

        @if ($tickets->count() === 0)
            <div class="xl:col-span-2 bg-white rounded-2xl border border-dashed border-[#cfdbe4] p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-blue-50 text-[#1e3a8a] flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700">Tidak ada tiket aktif saat ini</h3>
                <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Semua tiket telah terselesaikan, masuk ke riwayat, atau belum ada tiket baru yang ditugaskan kepada Anda.</p>
            </div>
        @endif
    </div>
</div>

<!-- ================= MODAL QUICK CHAT ================= -->
<div id="quickChatModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg h-[600px] flex flex-col overflow-hidden border border-slate-100">
        
        <div class="bg-[#1d405c] text-white p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center text-white shrink-0">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <div>
                    <h4 id="chatReporterName" class="text-xs font-bold leading-tight">Memuat...</h4>
                    <p id="chatTicketCode" class="text-[10px] text-blue-200 font-mono mt-0.5 truncate max-w-[320px]"></p>
                </div>
            </div>
            <button type="button" onclick="closeQuickChat()" class="text-white/70 hover:text-white transition w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div id="chatMessagesContainer" class="flex-1 p-4 overflow-y-auto space-y-3 bg-[#f8fafc]">
            <div class="text-center py-6 text-xs text-gray-400">Memuat obrolan...</div>
        </div>

        <form id="quickChatForm" onsubmit="sendQuickChatMessage(event)" class="p-3 bg-white border-t border-slate-200 flex items-center gap-2">
            <input type="hidden" id="activeChatTicketId" value="">
            <input type="text" 
                   id="quickChatMessageInput" 
                   placeholder="Ketik balasan untuk pelapor..." 
                   autocomplete="off"
                   class="flex-1 bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition-all">
            <button type="submit" class="bg-[#1e3a8a] hover:bg-[#152a6a] text-white w-10 h-10 rounded-xl flex items-center justify-center transition shadow-sm shrink-0">
                <i class="fas fa-paper-plane text-xs"></i>
            </button>
        </form>

    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    const liveSearchInput = document.getElementById('liveSearchInput');
    const liveStatusFilter = document.getElementById('liveStatusFilter');
    const liveOrgFilter = document.getElementById('liveOrgFilter');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const ticketCards = document.querySelectorAll('.ticket-card');
    const noMatchState = document.getElementById('noMatchFoundState');
    const countBadge = document.getElementById('activeTicketCountBadge');

    function applyLiveFilter() {
        const query = liveSearchInput.value.toLowerCase().trim();
        const selectedStatus = liveStatusFilter.value;
        const selectedOrg = liveOrgFilter.value.toLowerCase().trim();

        if (query.length > 0) {
            btnClearSearch.classList.remove('hidden');
        } else {
            btnClearSearch.classList.add('hidden');
        }

        let visibleCount = 0;

        ticketCards.forEach(card => {
            const cardSearchData = card.getAttribute('data-search') || '';
            const cardStatus = card.getAttribute('data-status-id') || '';
            const cardOrg = card.getAttribute('data-org') || '';

            const matchesQuery = query === '' || cardSearchData.includes(query);
            const matchesStatus = selectedStatus === '' || cardStatus === selectedStatus;
            const matchesOrg = selectedOrg === '' || cardOrg.includes(selectedOrg);

            if (matchesQuery && matchesStatus && matchesOrg) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (noMatchState) {
            if (visibleCount === 0 && ticketCards.length > 0) {
                noMatchState.classList.remove('hidden');
            } else {
                noMatchState.classList.add('hidden');
            }
        }

        if (countBadge) {
            countBadge.textContent = `${visibleCount} Tiket Aktif`;
        }
    }

    function resetLiveFilter() {
        liveSearchInput.value = '';
        liveStatusFilter.value = '';
        liveOrgFilter.value = '';
        applyLiveFilter();
        liveSearchInput.focus();
    }

    liveSearchInput.addEventListener('input', applyLiveFilter);
    liveStatusFilter.addEventListener('change', applyLiveFilter);
    liveOrgFilter.addEventListener('change', applyLiveFilter);

    let activeTicketId = null;
    const currentUserId = {{ auth()->id() ?? 'null' }};

    function openQuickChat(ticketId, ticketCode, ticketTitle, reporterName) {
        activeTicketId = ticketId;
        document.getElementById('activeChatTicketId').value = ticketId;
        document.getElementById('chatReporterName').textContent = reporterName;
        document.getElementById('chatTicketCode').textContent = `${ticketCode} • ${ticketTitle}`;
        
        document.getElementById('quickChatModal').classList.remove('hidden');

        const badge = document.getElementById(`badge-ticket-${ticketId}`);
        if (badge) badge.classList.add('hidden');

        fetchQuickMessages(ticketId);
        listenToTicket(ticketId);
    }

    function closeQuickChat() {
        document.getElementById('quickChatModal').classList.add('hidden');
        if (activeTicketId && window.Echo) {
            window.Echo.leave(`tickets.${activeTicketId}.chat`);
            activeTicketId = null;
        }
    }

    async function fetchQuickMessages(ticketId) {
        const container = document.getElementById('chatMessagesContainer');
        container.innerHTML = '<div class="text-center py-6 text-xs text-gray-400">Memuat riwayat chat...</div>';

        try {
            const res = await fetch(`/tickets/${ticketId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) throw new Error(`HTTP Error: ${res.status}`);

            const data = await res.json();

            if (data.success || Array.isArray(data)) {
                container.innerHTML = '';
                const messages = Array.isArray(data) ? data : (data.messages || []);
                if (messages.length === 0) {
                    container.innerHTML = '<div class="text-center py-8 text-xs text-gray-400">Belum ada obrolan di tiket ini.</div>';
                    return;
                }
                messages.forEach(msg => appendBubble(msg));
                scrollBottom();
            }
        } catch (err) {
            console.error('Error fetchQuickMessages:', err);
            container.innerHTML = '<div class="text-center py-6 text-xs text-rose-500">Gagal memuat pesan.</div>';
        }
    }

    function listenToTicket(ticketId) {
        if (!window.Echo) return;

        window.Echo.private(`tickets.${ticketId}.chat`)
            .listen('.message.sent', (event) => {
                appendBubble({
                    id: event.id,
                    user_name: event.user_name,
                    body: event.body,
                    created_at: event.created_at,
                    is_me: event.user_id === currentUserId
                });
                scrollBottom();
            });
    }

    async function sendQuickChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('quickChatMessageInput');
        const text = input.value.trim();

        if (!text || !activeTicketId) return;
        input.value = '';

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
                appendBubble(data.message);
                scrollBottom();
            }
        } catch (err) {
            console.error('Gagal kirim pesan:', err);
        }
    }

    function appendBubble(msg) {
        const container = document.getElementById('chatMessagesContainer');
        if (container.children.length === 1 && container.textContent.includes('Belum ada')) {
            container.innerHTML = '';
        }

        const bubble = document.createElement('div');
        bubble.className = `flex flex-col ${msg.is_me ? 'items-end' : 'items-start'}`;

        bubble.innerHTML = `
            <span class="text-[10px] text-gray-400 mb-0.5 px-1 font-medium">${msg.is_me ? 'Anda' : msg.user_name}</span>
            <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-xs leading-relaxed shadow-sm ${
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

    function scrollBottom() {
        const container = document.getElementById('chatMessagesContainer');
        container.scrollTop = container.scrollHeight;
    }
</script>
@endsection