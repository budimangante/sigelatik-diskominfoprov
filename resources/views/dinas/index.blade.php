@extends('components.b-master')

@section('title', 'Overview Lintas Bidang')
@section('subtitle', 'Seluruh permohonan perangkat daerah dalam satu pandangan')

@section('content')
<div class="min-h-screen space-y-5 bg-[#f3f6f8] p-4 font-sans sm:p-5 lg:p-6">

    <!-- Header Overview -->
    <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div class="min-w-0">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>Helpdesk Utama</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>Dinas Kominfo</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Overview Lintas Bidang</h1>
            <p class="mt-1 text-sm text-slate-500">Seluruh permohonan perangkat daerah dalam satu pandangan analitik.</p>
        </div>

        <!-- Komparasi SLA dari data controller -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="min-w-[145px] rounded-2xl border border-blue-200 bg-blue-50/80 px-4 py-3 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wide text-blue-700">Target SLA Bidang</p>
                <p class="mt-1 text-2xl font-extrabold leading-none text-[#1d405c]">
                    {{ $overviewData['avg_sla_bidang'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Jam</span>
                </p>
            </div>
            <div class="min-w-[145px] rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Realisasi Teknisi</p>
                <p class="mt-1 text-2xl font-extrabold leading-none text-emerald-700">
                    {{ $overviewData['avg_sla_teknisi'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Jam</span>
                </p>
            </div>
        </div>
    </section>

    <!-- Ringkasan Permohonan -->
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
        <!-- Total Permohonan -->
        <article class="min-h-[164px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)] sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Total Permohonan</p>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#2f789e] text-lg text-white shadow-sm"><i class="fas fa-ticket-alt"></i></span>
            </div>
            <p class="mt-1 text-[40px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($overviewData['total_permohonan'] ?? 0) }}</p>
            <span class="mt-4 inline-flex rounded-full bg-[#edf4f8] px-3 py-1.5 text-[11px] font-semibold text-[#397b9d]">Akumulasi bulan ini</span>
        </article>

        <!-- Menunggu Triage -->
        <article class="min-h-[164px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)] sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Menunggu Triage</p>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#5599b8] text-lg text-white shadow-sm"><i class="fas fa-inbox"></i></span>
            </div>
            <p class="mt-1 text-[40px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($overviewData['menunggu_triage'] ?? 0) }}</p>
            <span class="mt-4 inline-flex rounded-full bg-[#edf4f8] px-3 py-1.5 text-[11px] font-semibold text-[#397b9d]">Belum diarahkan ke teknisi</span>
        </article>

        <!-- Dalam Proses -->
        <article class="min-h-[164px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)] sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Dalam Proses</p>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#1d405c] text-lg text-white shadow-sm"><i class="fas fa-gears"></i></span>
            </div>
            <p class="mt-1 text-[40px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($overviewData['dalam_proses'] ?? 0) }}</p>
            <span class="mt-4 inline-flex rounded-full bg-[#edf1f7] px-3 py-1.5 text-[11px] font-semibold text-[#1d405c]">Sedang ditangani teknisi</span>
        </article>

        <!-- SLA Terlampaui -->
        <article class="min-h-[164px] rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)] sm:p-6">
            <div class="flex items-start justify-between gap-3">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">SLA Terlampaui</p>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#b52e3b] text-lg text-white shadow-sm"><i class="fas fa-triangle-exclamation"></i></span>
            </div>
            <p class="mt-1 text-[40px] font-extrabold leading-none tracking-tight text-[#293b49]">{{ number_format($overviewData['sla_terlampaui'] ?? 0) }}</p>
            <span class="mt-4 inline-flex rounded-full bg-[#faecee] px-3 py-1.5 text-[11px] font-semibold text-[#a82f3c]">Melewati batas waktu</span>
        </article>
    </section>

    <!-- Distribusi per Bidang dan Tiket Terbaru -->
    <section class="grid grid-cols-1 gap-5 2xl:grid-cols-[0.9fr_1.35fr]">
        <!-- Distribusi Tiket per Bidang -->
        <article class="flex min-w-0 flex-col rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] sm:p-6">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-layer-group"></i></span>
                    <h2 class="text-sm font-extrabold text-[#293b49] sm:text-base">Distribusi Tiket per Bidang</h2>
                </div>
                <span class="shrink-0 text-[11px] font-semibold text-slate-400">Tujuan Disposisi</span>
            </div>

            <div class="my-5 flex-1 space-y-5">
                @forelse($overviewData['bidang_progress'] ?? [] as $item)
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3 text-xs">
                            <span class="min-w-0 truncate font-bold text-[#34495a]">{{ $item['name'] }}</span>
                            <span class="shrink-0 font-mono font-semibold text-slate-500">{{ number_format($item['count']) }} Tiket</span>
                        </div>
                        <div class="h-2.5 w-full overflow-hidden rounded-full bg-[#eaf0f4]">
                            <div class="h-full rounded-full bg-[#2f789e] transition-all duration-700" style="width: {{ min(100, max(0, (float) ($item['percentage'] ?? 0))) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="flex min-h-[180px] items-center justify-center text-center text-xs text-slate-400">Belum ada distribusi tiket per bidang.</div>
                @endforelse
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4 text-[11px] text-slate-500">
                <span>Total Tiket Selesai: <strong class="font-extrabold text-[#293b49]">{{ number_format($overviewData['tiket_selesai'] ?? 0) }}</strong></span>
                <span class="font-medium text-slate-400">Pembaruan otomatis data aktif</span>
            </div>
        </article>

        <!-- Permohonan Tiket Terbaru -->
        <article class="flex min-w-0 flex-col overflow-hidden rounded-[22px] border border-[#dce4e9] bg-white p-5 shadow-[0_4px_16px_rgba(25,55,75,0.045)] sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eaf2f7] text-[#397b9d]"><i class="fas fa-clock-rotate-left"></i></span>
                    <h2 class="text-sm font-extrabold text-[#293b49] sm:text-base">Permohonan Tiket Terbaru</h2>
                </div>
                <span class="shrink-0 text-[11px] font-semibold text-slate-400">Urutan laporan terbaru</span>
            </div>

            <div class="-mx-5 min-w-0 flex-1 overflow-x-auto sm:-mx-6">
                <table class="w-full min-w-[690px] text-left text-xs">
                    <thead class="border-y border-slate-100 bg-[#f7f9fb] text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3.5">Kode Tiket</th>
                            <th class="px-4 py-3.5">Disposisi Bidang</th>
                            <th class="px-4 py-3.5">Teknisi / Penanganan</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($overviewData['latest_tickets'] ?? [] as $ticket)
                            <tr class="transition hover:bg-[#f8fafc]">
                                <td class="px-4 py-4 font-mono font-bold text-[#397b9d]">{{ $ticket['ticket_number'] }}</td>
                                <td class="px-4 py-4 font-semibold text-[#34495a]">{{ $ticket['bidang'] }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $ticket['technician'] }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-1 text-[10px] font-bold {{ $ticket['badge']['border'] }} {{ $ticket['badge']['text'] }} {{ $ticket['badge']['bg'] }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $ticket['badge']['dot'] }}"></span>
                                        {{ $ticket['badge']['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-xs italic text-slate-400">Belum ada permohonan tiket yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 border-t border-slate-100 pt-3 text-right">
                <span class="text-[10px] font-medium text-slate-400">Menampilkan hingga 6 permohonan terakhir</span>
            </div>
        </article>
    </section>
</div>
@endsection
