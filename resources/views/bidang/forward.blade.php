@extends('components.b-master')

@section('title', 'Forward Ticket')
@section('subtitle', 'Tinjau permintaan forward dari teknisi, lalu teruskan tiket ke bidang yang tepat.')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Halaman -->
    <div class="flex items-start justify-between mb-8">
        <div>
            <p class="text-xs font-semibold text-[#1a3668] tracking-wider uppercase mb-1.5 flex items-center gap-1.5">
                <span>HELPDESK BIDANG</span>
                <span class="text-gray-300">·</span>
                <span>LAYANAN APLIKASI</span>
            </p>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">Forward ticket</h1>
            <p class="text-sm text-gray-500 mt-1">
                Tinjau permintaan forward dari teknisi, lalu teruskan tiket ke bidang yang tepat.
            </p>
        </div>

        <!-- Notifikasi -->
        <div class="relative">
            <button type="button" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <i class="far fa-bell text-sm"></i>
            </button>
            @if(count($pendingTickets ?? []) > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-sm">
                    {{ count($pendingTickets) }}
                </span>
            @endif
        </div>
    </div>

    <!-- GRID 2 KOLOM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ================= KOLOM KIRI: FORM PENGALIHAN ================= -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm p-6">
                <form action="{{ route('bidang.forward.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Pilihan Tiket -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">Tiket</label>
                        <div class="relative">
                            <select id="ticketSelect" name="ticket_id" required onchange="handleTicketChange(this)"
                                    class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-3 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih tiket permintaan teknisi...</option>
                                @forelse($pendingTickets as $pending)
                                    @php
                                        // Gunakan ->last() agar ambil riwayat terbaru, dan kolom ->note (bukan notes)
                                        $latestHistory = $pending->statusHistories?->last();
                                        $techNote = $latestHistory?->note 
                                            ?? $pending->description 
                                            ?? 'Tidak ada catatan teknisi.';
                                    @endphp
                                    <option value="{{ $pending->id }}" data-technician-note="{{ $techNote }}">
                                        {{ $pending->ticket_number }} - {{ Str::limit($pending->subject, 35) }}
                                    </option>
                                @empty
                                    <option value="" disabled>Tidak ada tiket yang memerlukan forward</option>
                                @endforelse
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- BOX ALASAN FORWARD DARI TEKNISI (Otomatis Muncul) -->
                    <div id="techNoteContainer" class="hidden">
                        <label class="block text-xs font-bold text-blue-900 mb-1 flex items-center gap-1.5">
                            <i class="fas fa-info-circle text-blue-600"></i> Alasan Forward dari Teknisi
                        </label>
                        <div id="techNoteDisplay" class="w-full bg-blue-50/60 border border-blue-100 rounded-xl p-3 text-xs text-slate-700 leading-relaxed italic"></div>
                        <input type="hidden" name="technician_note" id="techNoteInput" value="">
                    </div>

                    <!-- Pilihan Bidang Tujuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">Teruskan ke bidang</label>
                        <div class="relative">
                            <select name="target_division" required 
                                    class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-3 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih bidang...</option>
                                <option value="Bidang TIK & Infrastruktur Jaringan">Bidang TIK & Infrastruktur Jaringan</option>
                                <option value="Bidang Aplikasi Informatika (Aptika)">Bidang Aplikasi Informatika (Aptika)</option>
                                <option value="Bidang Keamanan Informasi & Persandian">Bidang Keamanan Informasi & Persandian</option>
                                <option value="Bidang Statistik Sektoral">Bidang Statistik Sektoral</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Catatan Tambahan Bidang (Opsional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">Catatan Disposisi Tambahan (Opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Tambahkan instruksi khusus untuk bidang tujuan jika diperlukan..." 
                                  class="w-full bg-white border border-gray-200 rounded-xl p-3 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition resize-none"></textarea>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" 
                            class="w-full bg-[#1e3a8a] hover:bg-[#152a6a] text-white py-3 rounded-xl text-xs font-bold transition shadow-sm tracking-wide">
                        Forward Ticket
                    </button>
                </form>
            </div>
        </div>

        <!-- ================= KOLOM KANAN: TABEL RIWAYAT FORWARD ================= -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden min-h-[380px] flex flex-col justify-between">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 bg-white">
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tiket</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Diteruskan Ke</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Catatan</th>
                                <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($forwardedTickets as $item)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-4 px-6">
                                        <span class="font-mono text-blue-600 font-semibold block">{{ $item->ticket_number }}</span>
                                        <span class="text-gray-700 font-medium line-clamp-1">{{ $item->subject }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-100">
                                            {{ $item->target_division }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 max-w-xs">
                                        <div class="line-clamp-2">
                                            {{ $item->notes ?? $item->technician_note ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-400 text-right whitespace-nowrap font-mono text-[11px]">
                                        {{ $item->time ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-32 text-center text-sm text-gray-400 font-medium">
                                        Belum ada tiket yang diteruskan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function handleTicketChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const note = selectedOption.getAttribute('data-technician-note') || '';
        const container = document.getElementById('techNoteContainer');
        const display = document.getElementById('techNoteDisplay');
        const input = document.getElementById('techNoteInput');

        if (note.trim() !== '') {
            display.textContent = `"${note}"`;
            input.value = note;
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            input.value = '';
        }
    }
</script>
@endsection