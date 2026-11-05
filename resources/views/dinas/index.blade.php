@extends('components.b-master')

@section('title', 'Overview Lintas Bidang')
@section('subtitle', 'Seluruh permohonan perangkat daerah dalam satu pandangan')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- Header Section + Quick SLA Benchmarks -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center gap-2">
                <span class="text-[#1a3668]">HELPDESK UTAMA</span>
                <span class="text-slate-300">•</span>
                <span>DINAS KOMINFO</span>
            </div>
            <h1 class="text-2xl font-black text-[#1a3668] tracking-tight">Overview Lintas Bidang</h1>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh permohonan perangkat daerah dalam satu pandangan analitik.</p>
        </div>

        <!-- 2 Komparasi SLA: Bidang vs Teknisi -->
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 bg-blue-50/70 border border-blue-200 rounded-xl text-left min-w-[125px]">
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wide">Target SLA Bidang</p>
                <p class="text-lg font-black text-[#1a3668]">{{ $overviewData['avg_sla_bidang'] }} <span class="text-xs font-semibold text-slate-500">Jam</span></p>
            </div>
            <div class="px-4 py-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl text-left min-w-[125px]">
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Realisasi Teknisi</p>
                <p class="text-lg font-black text-emerald-600">{{ $overviewData['avg_sla_teknisi'] }} <span class="text-xs font-semibold text-slate-500">Jam</span></p>
            </div>
        </div>
    </div>

    <!-- 4 Kotak Ringkasan Atas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Permohonan -->
        <div class="bg-white rounded-xl p-5 shadow-2xs border border-slate-200 border-t-4 border-t-red-500 flex flex-col justify-between">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Total Permohonan</h3>
            <p class="text-3xl font-black text-[#1a3668] my-1">{{ $overviewData['total_permohonan'] }}</p>
            <p class="text-[11px] text-red-500 font-semibold flex items-center gap-1">
                <i class="fas fa-calendar-day text-[10px]"></i> Akumulasi bulan ini
            </p>
        </div>
        
        <!-- Card 2: Menunggu Triage -->
        <div class="bg-white rounded-xl p-5 shadow-2xs border border-slate-200 border-t-4 border-t-blue-500 flex flex-col justify-between">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Menunggu Triage</h3>
            <p class="text-3xl font-black text-blue-600 my-1">{{ $overviewData['menunggu_triage'] }}</p>
            <p class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                <i class="fas fa-inbox text-[10px]"></i> Belum diarahkan ke teknisi
            </p>
        </div>

        <!-- Card 3: Dalam Proses -->
        <div class="bg-white rounded-xl p-5 shadow-2xs border border-slate-200 border-t-4 border-t-emerald-500 flex flex-col justify-between">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Dalam Proses</h3>
            <p class="text-3xl font-black text-emerald-600 my-1">{{ $overviewData['dalam_proses'] }}</p>
            <p class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                <i class="fas fa-screwdriver-wrench text-[10px]"></i> Sedang ditangani teknisi
            </p>
        </div>

        <!-- Card 4: SLA Terlampaui -->
        <div class="bg-white rounded-xl p-5 shadow-2xs border border-slate-200 border-t-4 border-t-amber-500 flex flex-col justify-between">
            <h3 class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">SLA Terlampaui</h3>
            <p class="text-3xl font-black text-amber-600 my-1">{{ $overviewData['sla_terlampaui'] }}</p>
            <p class="text-[11px] text-amber-600 font-semibold flex items-center gap-1">
                <i class="fas fa-triangle-exclamation text-[10px]"></i> Melewati batas waktu
            </p>
        </div>
    </div>

    <!-- Main Content Grid: Distribusi Tiket per Bidang vs Tiket Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Panel: Tiket per Bidang (Progress Bars) -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200 p-6 flex flex-col justify-between min-w-0">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-[#1a3668] flex items-center gap-2">
                    <i class="fas fa-layer-group"></i>
                    <span>Distribusi Tiket per Bidang</span>
                </h2>
                <span class="text-[11px] text-slate-400 font-semibold">Tujuan Disposisi</span>
            </div>
            
            <div class="space-y-5 my-4">
                @foreach($overviewData['bidang_progress'] as $item)
                    <div>
                        <div class="flex justify-between text-xs text-slate-700 mb-1.5">
                            <span class="font-bold text-slate-800">{{ $item['name'] }}</span>
                            <span class="font-mono font-bold text-slate-600">{{ $item['count'] }} Tiket</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-[#1a3668] h-2.5 rounded-full transition-all duration-500" style="width: {{ $item['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Total Tiket Selesai: <strong>{{ $overviewData['tiket_selesai'] }}</strong></span>
                <span>Pembaruan otomatis data aktif</span>
            </div>
        </div>

        <!-- Right Panel: Tiket Terbaru & Arah Disposisi -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200 p-6 flex flex-col justify-between min-w-0">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h2 class="text-sm font-bold text-[#1a3668] flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>Permohonan Tiket Terbaru</span>
                </h2>
                <span class="text-[11px] text-slate-400">Urutan laporan terbaru</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-xs text-left whitespace-nowrap">
                    <thead class="text-[10px] text-slate-400 uppercase tracking-wider bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-bold">KODE TIKET</th>
                            <th class="px-4 py-3 font-bold">DISPOSISI BIDANG</th>
                            <th class="px-4 py-3 font-bold">TEKNISI / PENANGAN</th>
                            <th class="px-4 py-3 font-bold text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($overviewData['latest_tickets'] as $ticket)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-4 py-3 font-mono font-bold text-blue-600">
                                    {{ $ticket['ticket_number'] }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">
                                    {{ $ticket['bidang'] }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $ticket['technician'] }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $ticket['badge']['border'] }} {{ $ticket['badge']['text'] }} {{ $ticket['badge']['bg'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $ticket['badge']['dot'] }}"></span>
                                        {{ $ticket['badge']['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 italic">
                                    Belum ada permohonan tiket yang masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-3 border-t border-slate-100 text-right">
                <span class="text-[10px] text-slate-400">Menampilkan hingga 6 permohonan terakhir</span>
            </div>
        </div>

    </div>

</div>
@endsection