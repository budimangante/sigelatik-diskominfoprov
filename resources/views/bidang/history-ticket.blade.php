@extends('components.b-master')

@section('title', 'History Ticket')

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
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">History ticket</h1>
            <p class="text-sm text-gray-500 mt-1">
                Riwayat tiket yang telah selesai, dibatalkan, atau diteruskan ke bidang lain.
            </p>
        </div>

        <!-- Notifikasi -->
        <div class="relative">
            <button type="button" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <i class="far fa-bell text-sm"></i>
            </button>
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center shadow-sm">
                2
            </span>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <form action="{{ url()->current() }}" method="GET" class="mb-6 flex flex-col sm:flex-row gap-4">
        <!-- Input Pencarian -->
        <div class="flex-1">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Cari nomor atau judul tiket..." 
                   class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition shadow-sm">
        </div>

        <!-- Dropdown Filter Kejadian -->
        <div class="relative w-full sm:w-64">
            <select name="event" 
                    onchange="this.form.submit()"
                    class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1e3a8a] transition appearance-none cursor-pointer shadow-sm">
                <option value="" {{ request('event') == '' ? 'selected' : '' }}>Semua kejadian</option>
                <option value="solved" {{ request('event') == 'solved' ? 'selected' : '' }}>Selesai (Solved)</option>
                <option value="cancelled" {{ request('event') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                <option value="forwarded" {{ request('event') == 'forwarded' ? 'selected' : '' }}>Diteruskan</option>
            </select>
            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
    </form>

    <!-- Tabel Riwayat Tiket -->
    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-white">
                        <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-44">Tiket</th>
                        <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-52">Kejadian</th>
                        <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-48">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($historyTickets ?? [] as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-6 font-mono font-medium text-blue-600 hover:underline">
                                <a href="#">{{ $item->ticket_number }}</a>
                            </td>
                            <td class="py-4 px-6 text-slate-900 font-bold">
                                {{ $item->subject }}
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ $item->event }}
                            </td>
                            <td class="py-4 px-6 text-gray-500 font-mono text-[11px]">
                                {{ $item->time }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-24 text-center text-sm text-gray-400 font-medium">
                                Belum ada riwayat tiket yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection