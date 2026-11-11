@extends('components.b-master')

@section('title', 'Forward Ticket')
@section('subtitle', 'Tinjau permintaan forward dari teknisi, lalu teruskan tiket ke bidang yang tepat.')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] p-4 font-sans sm:p-6 lg:p-7">
    <div class="mx-auto w-full max-w-[1900px]">
        <!-- Header -->
        <div class="mb-6 flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>Helpdesk Bidang</span><span class="text-[#b8c7d0]">•</span><span>Layanan Aplikasi</span>
                </div>
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Forward Ticket</h1>
                <p class="mt-1.5 text-xs text-slate-500 sm:text-sm">Tinjau permintaan forward dari teknisi, lalu teruskan tiket ke bidang yang tepat.</p>
            </div>
            <div class="relative mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-[#dce5eb] bg-white text-[#397b9d] shadow-sm" title="Tiket menunggu forward">
                <i class="far fa-bell text-base"></i>
                @if(count($pendingTickets ?? []) > 0)
                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white shadow-sm">{{ count($pendingTickets) }}</span>
                @endif
            </div>
        </div>

        <!-- Feedback -->
        @if(session('success'))
            <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800"><i class="fas fa-check-circle text-emerald-600"></i>{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 space-y-1 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-rose-500"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Form Forward + Daftar Tiket Diteruskan -->
        <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-12">
            <!-- Form -->
            <section class="rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6 xl:col-span-4">
                <div class="mb-5 border-b border-slate-100 pb-4">
                    <h2 class="flex items-center gap-2 text-sm font-bold text-[#1d405c]"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#eaf3f8] text-[#347d9f]"><i class="fas fa-share"></i></span> Teruskan Permintaan Tiket</h2>
                    <p class="mt-1 text-[11px] text-slate-400">Pilih tiket dari permintaan teknisi dan tentukan bidang tujuan.</p>
                </div>

                <form action="{{ route('bidang.forward.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Pilihan Tiket -->
                    <div>
                        <label for="ticketSelect" class="mb-2 block text-xs font-bold text-slate-700">Tiket</label>
                        <div class="relative">
                            <select id="ticketSelect" name="ticket_id" required onchange="handleTicketChange(this)" class="w-full appearance-none rounded-xl border border-slate-200 bg-[#f8fafc] px-3.5 py-3 pr-10 text-xs text-slate-700 transition focus:border-[#347d9f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#347d9f]/15">
                                <option value="" disabled selected>Pilih tiket permintaan teknisi...</option>
                                @forelse($pendingTickets as $pending)
                                    @php
                                        $latestHistory = $pending->statusHistories?->last();
                                        $techNote = $latestHistory?->note ?? $pending->description ?? 'Tidak ada catatan teknisi.';
                                    @endphp
                                    <option value="{{ $pending->id }}" data-technician-note="{{ $techNote }}">{{ $pending->ticket_number }} - {{ Str::limit($pending->subject, 35) }}</option>
                                @empty
                                    <option value="" disabled>Tidak ada tiket yang memerlukan forward</option>
                                @endforelse
                            </select>
                            <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                        </div>
                    </div>

                    <!-- Catatan teknisi -->
                    <div id="techNoteContainer" class="hidden">
                        <label class="mb-2 flex items-center gap-1.5 text-xs font-bold text-[#245a7a]"><i class="fas fa-info-circle text-[#347d9f]"></i> Alasan Forward dari Teknisi</label>
                        <div id="techNoteDisplay" class="rounded-xl border border-blue-100 bg-blue-50/70 p-3 text-xs italic leading-relaxed text-slate-700"></div>
                        <input type="hidden" name="technician_note" id="techNoteInput" value="">
                    </div>

                    <!-- Pilihan Bidang Tujuan -->
                    <div>
                        <label for="targetDivision" class="mb-2 block text-xs font-bold text-slate-700">Teruskan ke bidang</label>
                        <div class="relative">
                            <select id="targetDivision" name="target_division" required class="w-full appearance-none rounded-xl border border-slate-200 bg-[#f8fafc] px-3.5 py-3 pr-10 text-xs text-slate-700 transition focus:border-[#347d9f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#347d9f]/15">
                                <option value="" disabled selected>Pilih bidang...</option>
                                <option value="Bidang TIK & Infrastruktur Jaringan">Bidang TIK & Infrastruktur Jaringan</option>
                                <option value="Bidang Aplikasi Informatika (Aptika)">Bidang Aplikasi Informatika (Aptika)</option>
                                <option value="Bidang Keamanan Informasi & Persandian">Bidang Keamanan Informasi & Persandian</option>
                                <option value="Bidang Statistik Sektoral">Bidang Statistik Sektoral</option>
                            </select>
                            <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                        </div>
                    </div>

                    <!-- Catatan tambahan -->
                    <div>
                        <label for="forwardNotes" class="mb-2 block text-xs font-bold text-slate-700">Catatan Disposisi Tambahan <span class="font-medium text-slate-400">(Opsional)</span></label>
                        <textarea id="forwardNotes" name="notes" rows="4" placeholder="Tambahkan instruksi khusus untuk bidang tujuan jika diperlukan..." class="w-full resize-y rounded-xl border border-slate-200 bg-[#f8fafc] p-3 text-xs text-slate-700 transition placeholder:text-slate-400 focus:border-[#347d9f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#347d9f]/15"></textarea>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#28799f] py-3 text-xs font-bold tracking-wide text-white shadow-sm transition hover:bg-[#216887] hover:shadow-md">
                        <i class="fas fa-share"></i> Forward Ticket
                    </button>
                </form>
            </section>

            <!-- History forwarded -->
            <section class="min-h-[380px] overflow-hidden rounded-2xl border border-[#dce5eb] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)] xl:col-span-8">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#eaf3f8] text-[#347d9f]"><i class="fas fa-shuffle"></i></span>
                        <div>
                            <h2 class="text-sm font-bold text-[#1d405c]">Tiket yang Diteruskan</h2>
                            <p class="mt-0.5 text-[11px] text-slate-400">Riwayat disposisi ke bidang lain.</p>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] border-collapse text-left text-xs">
                        <thead class="border-b border-slate-200 bg-[#f7f9fb] text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">
                            <tr>
                                <th class="px-5 py-3.5">Tiket</th>
                                <th class="px-5 py-3.5">Diteruskan Ke</th>
                                <th class="px-5 py-3.5">Catatan</th>
                                <th class="px-5 py-3.5 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($forwardedTickets as $item)
                                <tr class="transition hover:bg-[#f7fafc]">
                                    <td class="px-5 py-4">
                                        <span class="block font-mono font-bold text-[#347d9f]">{{ $item->ticket_number }}</span>
                                        <span class="mt-1 block max-w-[220px] truncate text-[11px] text-slate-500">{{ $item->subject }}</span>
                                    </td>
                                    <td class="px-5 py-4"><span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-[#245a7a]">{{ $item->target_division }}</span></td>
                                    <td class="max-w-[260px] px-5 py-4 text-slate-600"><div class="line-clamp-2">{{ $item->notes ?? $item->technician_note ?? '-' }}</div></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right font-mono text-[10px] text-slate-400">{{ $item->time ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-24 text-center text-xs font-medium text-slate-400"><span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-lg text-slate-300"><i class="fas fa-inbox"></i></span>Belum ada tiket yang diteruskan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
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
