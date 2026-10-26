@extends('components.b-master')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    
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
        <a href="{{ route('teknisi.index') }}" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mb-2 font-medium">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Task
        </a>
    </div>

    <!-- ================= BANNER SLA GEDE ================= -->
    <div class="bg-[#1a3668] text-white px-6 py-5 rounded-2xl shadow-sm mb-6 flex flex-col justify-center">
        <span class="text-xs uppercase tracking-wider text-blue-200 font-semibold mb-1">
            SISA WAKTU SLA
        </span>
        <div id="slaTimer" class="text-4xl md:text-5xl font-mono font-bold tracking-wide">
            Memuat...
        </div>
    </div>

    <!-- ================= KARTU INFO TIKET LENGKAP ================= -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-mono font-semibold text-blue-600">
                {{ $ticket->ticket_number ?? ('HD-2610-00' . $ticket->id) }}
            </span>
            <span id="badgeTicketStatus" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                {{ $ticket->status->name ?? 'PROCESS' }}
            </span>
        </div>

        <h1 class="text-xl md:text-2xl font-bold text-slate-900 mb-6">
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
                <span class="text-slate-400 block mb-1">Dibuat</span>
                <span class="font-bold text-slate-800 text-sm">
                    {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                </span>
            </div>
        </div>
    </div>

    <!-- GRID 2 KOLOM: FORM PENANGANAN (KIRI) + SIDEBAR CHAT (KANAN) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- ================= KOLOM KIRI (2/3): FORM TINDAKAN TEKNISI ================= -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                
                <!-- Deskripsi Keluhan Pelapor -->
                <div class="mb-5 pb-5 border-b border-gray-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Masalah Pelapor</label>
                    <div class="w-full bg-slate-50 border border-gray-200 rounded-xl p-3.5 text-xs text-gray-700 leading-relaxed">
                        {{ $ticket->description ?? 'Tidak ada catatan deskripsi keluhan.' }}
                    </div>
                </div>

                <!-- Form Tindakan & Laporan -->
                <form id="formTeknisi" onsubmit="submitFormTeknisi(event)" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Kartu Ubah Status Dinamis -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <label class="block text-sm font-bold text-slate-800 mb-3">Ubah status</label>
                        <div class="relative">
                            <select id="statusSelect" name="ticket_status_id" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] appearance-none cursor-pointer">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->id }}" 
                                            data-name="{{ strtolower($st->name) }}"
                                            {{ $ticket->ticket_status_id == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Catatan Penanganan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Catatan penanganan</label>
                        <textarea id="catatanPenanganan" name="notes" rows="4" 
                                  placeholder="Tuliskan tindakan yang dilakukan..." 
                                  class="w-full border border-gray-200 rounded-xl p-3.5 text-xs text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition-all"></textarea>
                    </div>

                    <!-- Checkbox Teruskan ke Bidang -->
                    <div class="flex justify-end">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="checkTeruskanBidang" name="teruskan_ke_bidang" value="1" 
                                   onchange="handleTeruskanBidang(this)"
                                   class="w-4 h-4 rounded text-[#1e3a8a] border-gray-300 focus:ring-blue-500/20 cursor-pointer">
                            <span class="text-xs font-semibold text-slate-700">Teruskan Ke Bidang ?</span>
                        </label>
                    </div>

                    <!-- Dokumen Laporan Pengerjaan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Dokumen laporan pengerjaan</label>
                        <div class="border border-dashed border-gray-300 rounded-xl p-2 bg-slate-50/50 flex items-center gap-3">
                            <label class="bg-[#1e3a8a] hover:bg-[#152a6a] text-white px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition shadow-sm shrink-0">
                                Browse...
                                <input type="file" id="dokumenLaporan" name="document" class="hidden" onchange="updateFileName(this)">
                            </label>
                            <span id="fileNameDisplay" class="text-xs text-gray-500 truncate">No file selected.</span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">PDF, JPG, PNG, atau DOCX. Maksimal 10 MB.</p>
                    </div>

                    <!-- Tombol Simpan Laporan (Outline Style) -->
                    <button type="submit" id="btnSubmitLaporan" 
                            class="w-full py-3 rounded-xl border border-[#1e3a8a] text-[#1e3a8a] hover:bg-blue-50 text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                        <span>Simpan Laporan</span>
                    </button>
                </form>

            </div>
        </div>

        <!-- ================= KOLOM KANAN (1/3): CHAT REALTIME (UTUH) ================= -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 h-[620px] flex flex-col overflow-hidden">
                
                <!-- Header Chat Box -->
                <div class="bg-[#1e3a8a] text-white p-3.5 flex items-center justify-between shadow-sm">
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

                <!-- Feed Pesan -->
                <div id="detailChatContainer" class="flex-1 p-3.5 overflow-y-auto space-y-3 bg-[#f8fafc]">
                    <div class="text-center py-6 text-xs text-gray-400 font-medium">Memuat obrolan...</div>
                </div>

                <!-- Form Input Chat: Aman dari reload native browser -->
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
        </div>

    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    const ticketId = {{ (int) $ticket->id }};
    const currentUserId = {{ auth()->id() ?? 'null' }};
    let toastTimeout;

    // ================= FITUR LEMBAR TINDAKAN TEKNIS =================
    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        display.textContent = input.files.length > 0 ? input.files[0].name : 'No file selected.';
    }

    function handleTeruskanBidang(checkbox) {
        const statusSelect = document.getElementById('statusSelect');
        if (checkbox.checked) {
            // Cari option yang mengandung nama solved / selesai
            const solvedOption = Array.from(statusSelect.options).find(opt => 
                opt.text.toLowerCase().includes('solved') || opt.text.toLowerCase().includes('selesai')
            );
            if (solvedOption) {
                statusSelect.value = solvedOption.value;
            }
            statusSelect.classList.add('bg-slate-100', 'pointer-events-none');
        } else {
            // Kembalikan ke pilihan sebelumnya (misalnya Process)
            const processOption = Array.from(statusSelect.options).find(opt => 
                opt.text.toLowerCase().includes('process') || opt.text.toLowerCase().includes('proses')
            );
            if (processOption) {
                statusSelect.value = processOption.value;
            }
            statusSelect.classList.remove('bg-slate-100', 'pointer-events-none');
        }
    }

    // Fungsi Kirim Form Penanganan Teknisi
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
                // Tampilkan Toast Notifikasi
                showToast(data.title, data.message);
                updateStatusBadge(data.status_name);

                // JIKA TIKET DINYATAKAN SELESAI (SOLVED / TERUSKAN KE BIDANG)
                if (data.is_solved) {
                    btn.innerHTML = '<i class="fas fa-check mr-1"></i> Dialihkan ke Riwayat...';
                    // Beri jeda 1.5 detik agar teknisi membaca notifikasi sebelum halaman berpindah
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                    return;
                }

                // JIKA HANYA UPDATE STATUS BIASA (Process, Pending, dll)
                document.getElementById('catatanPenanganan').value = '';
                document.getElementById('dokumenLaporan').value = '';
                document.getElementById('fileNameDisplay').textContent = 'No file selected.';
                document.getElementById('checkTeruskanBidang').checked = false;
                document.getElementById('statusSelect').classList.remove('bg-slate-100', 'pointer-events-none');
                document.getElementById('statusSelect').value = data.status_id;

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

    // ================= FITUR CHAT REALTIME (ASLI & UTUH) =================
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
                const sel = document.getElementById('statusSelect');
                if (sel) sel.value = event.status_id;
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

            if (!res.ok) {
                throw new Error(`HTTP Error: ${res.status}`);
            }

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

            if (!res.ok) {
                throw new Error(`HTTP Error: ${res.status}`);
            }

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
                    ? 'bg-[#1e3a8a] text-white rounded-tr-none' 
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

    // ================= TIMER SLA =================
    const createdAt = new Date("{{ $ticket->created_at->toIso8601String() }}").getTime();
    const slaTarget = new Date(createdAt + (4 * 60 * 60 * 1000)).getTime(); 

    function updateSLATimer() {
        const now = new Date().getTime();
        const distance = slaTarget - now;
        const timerElement = document.getElementById("slaTimer");

        if (!timerElement) return;

        if (distance <= 0) {
            timerElement.innerHTML = "<span class='text-red-300'>00:00:00 (SLA Habis)</span>";
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const pad = (n) => n.toString().padStart(2, '0');
        timerElement.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    }

    updateSLATimer();
    setInterval(updateSLATimer, 1000);
</script>
@endsection