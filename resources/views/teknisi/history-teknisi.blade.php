@extends('components.b-master')

@section('content')
<div class="p-5 md:p-7 bg-[#f3f5f7] min-h-screen font-sans">
    
    <!-- Header Section -->
    <div class="mb-5">
        <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
            <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
            <span>TEKNISI</span><span class="text-[#b8c7d0]">•</span>
            <span class="text-slate-600 font-bold uppercase">{{ auth()->user()->name ?? 'TEKNISI' }}</span>
        </div>
        <h1 class="mb-1 text-3xl text-[#1d405c] sm:text-[25px]">
            <span class="font-['StretchPro'] tracking-wider uppercase">History Ticket</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e9f5ef] text-[#25845d] border border-[#cde8da] ml-2 align-middle">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span id="activeTicketCountBadge">{{ $tickets->count() }} Tiket</span>
            </span>
        </h1>
        <p class="text-[11px] md:text-xs text-slate-500 mt-1">Pilih tiket untuk melihat riwayat penanganan, arsip chat, pencapaian SLA, dan log eskalasi.</p>
    </div>

    <!-- Filter & Pencarian Live -->
    <div class="mb-5 bg-white border border-[#dce4eb] rounded-2xl p-3.5 shadow-[0_3px_12px_rgba(27,64,92,0.04)]">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input type="text"
                   id="liveSearchHistory"
                   placeholder="Ketik nomor tiket, keluhan, nama pelapor, atau instansi (mis. 0142, Srikandi)..."
                   autocomplete="off"
                   class="w-full bg-[#f7f9fb] border border-[#dce4eb] rounded-xl pl-10 pr-10 py-3 text-[11px] md:text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] transition">
            <button type="button"
                    id="btnClearHistorySearch"
                    onclick="resetLiveHistoryFilter()"
                    class="hidden absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] gap-2.5 mt-2.5">
            <div class="relative min-w-0">
                <select id="liveStatusFilterHistory" class="w-full bg-white border border-[#dce4eb] rounded-xl px-3.5 py-2.5 text-[11px] md:text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] appearance-none cursor-pointer">
                    <option value="">Semua Status Selesai</option>
                    <option value="4">Solved (Selesai)</option>
                    <option value="5">Canceled (Dibatalkan)</option>
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <div class="relative min-w-0">
                <select id="liveOrgFilterHistory" class="w-full bg-white border border-[#dce4eb] rounded-xl px-3.5 py-2.5 text-[11px] md:text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#3d83a4]/15 focus:border-[#397b9d] appearance-none cursor-pointer">
                    <option value="">Semua Instansi / Dinas</option>
                    @php
                        $uniqueHistoryOrgs = $tickets->pluck('user.organization')->filter()->unique();
                    @endphp
                    @foreach ($uniqueHistoryOrgs as $orgItem)
                        <option value="{{ strtolower($orgItem) }}">{{ $orgItem }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>

            <button type="button"
                    onclick="resetLiveHistoryFilter()"
                    class="px-4 py-2.5 bg-transparent hover:bg-slate-50 text-slate-500 hover:text-[#286f94] font-semibold rounded-xl text-[11px] transition flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="fas fa-redo-alt text-[10px]"></i>
                <span>Reset Filter</span>
            </button>
        </div>
    </div>

    <!-- List Kartu Tiket History -->
    <div id="historyCardsContainer" class="grid grid-cols-1 xl:grid-cols-2 gap-3.5">
        @if ($tickets->count() > 0)
            @foreach ($tickets as $item)
                @php
                    $itemCode = $item->ticket_number ?? ('HD-2610-00' . $item->id);
                    $reporter =$item->user->name ?? 'Pelapor';
                    $org =$item->user->organization ?? 'Dinas Terkait';
                    $subject = $item->subject ?? $item->title ?? 'Keluhan Pelapor';
                    $statusName =$item->status->name ?? 'SOLVED';

                    $statusLower = strtolower($statusName);
                    if (str_contains($statusLower, 'cancel') || str_contains($statusLower, 'batal')) {
                        $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';$dotClass = 'bg-rose-500';
                    } else {
                        $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';$dotClass = 'bg-emerald-500';
                    }

                    $searchIndex = strtolower($itemCode . ' ' .$subject . ' ' . $reporter . ' ' .$org);
                @endphp
                <div class="history-card bg-white rounded-2xl shadow-[0_3px_14px_rgba(27,64,92,0.05)] border border-[#dce4eb] p-4 md:p-5 hover:border-[#a9c6d8] hover:shadow-[0_7px_20px_rgba(27,64,92,0.08)] transition-all duration-200 min-w-0"
                     data-search="{{ $searchIndex }}"
                     data-status-id="{{ $item->ticket_status_id }}"
                     data-org="{{ strtolower($org) }}">
                    <div class="flex justify-between items-start">
                        <div class="pr-6">
                            <span class="text-xs font-mono font-semibold text-slate-400 block mb-1">{{ $itemCode }}</span>
                            <a href="{{ route('teknisi.history.show', $item) }}" class="text-sm md:text-[15px] font-bold text-[#253b4c] hover:text-[#286f94] transition block leading-snug">
                                {{ $subject }}
                            </a>
                            <p class="text-[10px] text-slate-500 mt-1">
                                <span>{{ $org }}</span>
                                <span class="mx-1">•</span>
                                <span>{{ $reporter }}</span>
                                @if($item->solved_at)
                                    <span class="text-slate-400 ml-1.5">• Selesai: {{ \Carbon\Carbon::parse($item->solved_at)->translatedFormat('d M Y, H:i') }}</span>
                                @elseif($item->cancelled_at)
                                    <span class="text-rose-400 ml-1.5">• Dibatalkan: {{ \Carbon\Carbon::parse($item->cancelled_at)->translatedFormat('d M Y, H:i') }}</span>
                                @endif
                            </p>
                        </div>

                        <!-- Badge Status -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }} uppercase tracking-wider shrink-0 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                            {{ strtoupper($statusName) }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- State Hasil Live Search Tidak Ditemukan -->
        <div id="noMatchHistoryState" class="hidden xl:col-span-2 bg-white rounded-2xl border border-dashed border-[#cfdbe4] p-10 text-center">
            <div class="w-14 h-14 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3 text-xl">
                <i class="fas fa-search-minus"></i>
            </div>
            <h4 class="text-sm font-semibold text-slate-700">Tidak ada riwayat yang cocok</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Tidak ditemukan tiket riwayat dengan kata kunci atau filter status yang Anda pilih.</p>
            <button type="button" onclick="resetLiveHistoryFilter()" class="mt-4 inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline font-semibold">
                <i class="fas fa-undo text-[10px]"></i> Bersihkan filter pencarian
            </button>
        </div>

        @if ($tickets->count() === 0)
            <!-- State Kosong Default -->
            <div class="xl:col-span-2 bg-white rounded-2xl border border-dashed border-[#cfdbe4] p-10 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="fas fa-history"></i>
                </div>
                <h4 class="text-sm font-semibold text-slate-700">Belum ada riwayat tiket</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Tiket yang telah Anda selesaikan atau batalkan akan diarsipkan secara otomatis di halaman ini.</p>
            </div>
        @endif
    </div>

</div>

<!-- JAVASCRIPT: LIVE FILTER RIWAYAT -->
<script>
    const searchInput = document.getElementById('liveSearchHistory');
    const statusSelect = document.getElementById('liveStatusFilterHistory');
    const orgSelect = document.getElementById('liveOrgFilterHistory');
    const btnClearSearch = document.getElementById('btnClearHistorySearch');
    const cards = document.querySelectorAll('.history-card');
    const noMatchState = document.getElementById('noMatchHistoryState');
    const counterBadge = document.getElementById('historyCountBadge');

    function applyHistoryFilter() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusSelect.value;
        const selectedOrg = orgSelect.value.toLowerCase().trim();

        if (query.length > 0) {
            btnClearSearch.classList.remove('hidden');
        } else {
            btnClearSearch.classList.add('hidden');
        }

        let matchedCount = 0;

        cards.forEach(card => {
            const cardSearch = card.getAttribute('data-search') || '';
            const cardStatus = card.getAttribute('data-status-id') || '';
            const cardOrg = card.getAttribute('data-org') || '';

            const matchQuery = query === '' || cardSearch.includes(query);
            const matchStatus = selectedStatus === '' || cardStatus === selectedStatus;
            const matchOrg = selectedOrg === '' || cardOrg.includes(selectedOrg);

            if (matchQuery && matchStatus && matchOrg) {
                card.classList.remove('hidden');
                matchedCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        if (noMatchState) {
            if (matchedCount === 0 && cards.length > 0) {
                noMatchState.classList.remove('hidden');
            } else {
                noMatchState.classList.add('hidden');
            }
        }

        if (counterBadge) {
            counterBadge.textContent = `${matchedCount} Tiket Selesai`;
        }
    }

    function resetLiveHistoryFilter() {
        searchInput.value = '';
        statusSelect.value = '';
        orgSelect.value = '';
        applyHistoryFilter();
        searchInput.focus();
    }

    searchInput.addEventListener('input', applyHistoryFilter);
    statusSelect.addEventListener('change', applyHistoryFilter);
    orgSelect.addEventListener('change', applyHistoryFilter);
</script>
@endsection