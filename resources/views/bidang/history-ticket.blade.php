@extends('components.b-master')

@section('title', 'History Ticket')
@section('subtitle', 'Riwayat tiket yang telah selesai, dibatalkan, atau diteruskan ke bidang lain.')

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
                <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">History Ticket</h1>
                <p class="mt-1.5 text-xs text-slate-500 sm:text-sm">Riwayat tiket yang telah selesai, dibatalkan, atau diteruskan ke bidang lain.</p>
            </div>
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-[#dce5eb] bg-white text-[#397b9d] shadow-sm" title="Riwayat tiket"><i class="far fa-bell text-base"></i></div>
        </div>

        <!-- Search & Filter -->
        <form action="{{ url()->current() }}" method="GET" class="mb-5 rounded-2xl border border-[#dce5eb] bg-white p-3.5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-4">
            <div class="grid grid-cols-1 items-center gap-3 sm:grid-cols-[minmax(0,1fr)_220px]">
                <div class="relative min-w-0">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau judul tiket..." class="w-full rounded-xl border border-slate-200 bg-[#f8fafc] py-3 pl-10 pr-4 text-xs text-slate-700 transition placeholder:text-slate-400 focus:border-[#347d9f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#347d9f]/15">
                </div>
                <div class="relative">
                    <select name="event" onchange="this.form.submit()" class="w-full appearance-none rounded-xl border border-slate-200 bg-[#f8fafc] px-4 py-3 pr-10 text-xs text-slate-700 transition focus:border-[#347d9f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#347d9f]/15">
                        <option value="" {{ request('event') == '' ? 'selected' : '' }}>Semua kejadian</option>
                        <option value="solved" {{ request('event') == 'solved' ? 'selected' : '' }}>Selesai (Solved)</option>
                        <option value="cancelled" {{ request('event') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        <option value="forwarded" {{ request('event') == 'forwarded' ? 'selected' : '' }}>Diteruskan</option>
                    </select>
                    <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-slate-400"></i>
                </div>
            </div>
        </form>

        <!-- History table -->
        <section class="overflow-hidden rounded-2xl border border-[#dce5eb] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 p-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#eaf3f8] text-[#347d9f]"><i class="fas fa-clock-rotate-left"></i></span>
                    <div>
                        <h2 class="text-sm font-bold text-[#1d405c]">Riwayat Tiket</h2>
                        <p class="mt-0.5 text-[11px] text-slate-400">Daftar aktivitas tiket berdasarkan kejadian.</p>
                    </div>
                </div>
                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ count($historyTickets ?? []) }} Riwayat</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-left text-xs">
                    <thead class="border-b border-slate-200 bg-[#f7f9fb] text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">
                        <tr>
                            <th class="w-52 px-5 py-3.5">Tiket</th>
                            <th class="px-5 py-3.5">Judul</th>
                            <th class="w-64 px-5 py-3.5">Kejadian</th>
                            <th class="w-52 px-5 py-3.5">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($historyTickets ?? [] as $item)
                            @php
                                $eventText = (string) ($item->event ?? '');
                                $eventLower = strtolower($eventText);
                                $eventBadge = str_contains($eventLower, 'selesai') || str_contains($eventLower, 'solved')
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                    : (str_contains($eventLower, 'batal') || str_contains($eventLower, 'cancel')
                                        ? 'border-slate-200 bg-slate-100 text-slate-600'
                                        : 'border-blue-200 bg-blue-50 text-[#245a7a]');
                            @endphp
                            <tr class="transition hover:bg-[#f7fafc]">
                                <td class="whitespace-nowrap px-5 py-4 font-mono font-bold text-[#347d9f]">{{ $item->ticket_number }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $item->subject }}</td>
                                <td class="px-5 py-4"><span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $eventBadge }}">{{ $item->event }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 font-mono text-[10px] text-slate-400">{{ $item->time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-lg text-slate-300"><i class="far fa-clock"></i></span>
                                    <p class="text-xs font-semibold text-slate-500">Belum ada riwayat tiket yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
