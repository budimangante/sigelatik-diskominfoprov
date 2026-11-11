@extends('components.b-master')

@section('title', 'Overview Tiket Bidang')
@section('subtitle', 'Monitoring kinerja SLA dan penugasan tiket Helpdesk Bidang')

@php
    // Tetap menggunakan collection tiket dari controller.
    $totalAktif = $tickets->count();
@endphp

@section('content')
<div class="min-h-screen bg-[#f3f6f8] p-4 font-sans sm:p-6 lg:p-7">
    <div class="mx-auto w-full max-w-[1900px] space-y-6">

        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>Helpdesk Bidang</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>Overview & Monitoring</span>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Overview Kinerja Tiket</h1>
                    <span class="inline-flex items-center rounded-full border border-[#c9dce7] bg-[#edf5f9] px-3 py-1 text-[11px] font-bold text-[#347d9f]">
                        {{ $totalAktif }} Tiket Aktif
                    </span>
                </div>
                <p class="mt-1.5 max-w-3xl text-xs leading-relaxed text-slate-500 sm:text-sm">
                    Pantau distribusi penanganan, kepatuhan batas waktu SLA, serta penugasan teknisi lapangan secara real-time.
                </p>
            </div>

            <a href="{{ route('bidang.triage') }}"
               class="relative inline-flex w-fit shrink-0 items-center gap-2.5 rounded-xl bg-[#28799f] px-4 py-3 text-xs font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#216887] hover:shadow-md">
                <i class="fas fa-clipboard-check text-sm text-blue-100"></i>
                <span>Buka Triage Tiket</span>
                @if($ticketMasukCount > 0)
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white">{{ $ticketMasukCount }}</span>
                @endif
            </a>
        </div>

        <!-- Alert sukses -->
        @if(session('success'))
            <div class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-sm text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="rounded-lg p-1 text-emerald-600 transition hover:bg-emerald-100 hover:text-emerald-800" aria-label="Tutup notifikasi">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- KPI -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- Tiket Masuk -->
            <section class="flex min-h-[142px] items-start justify-between rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_12px_26px_rgba(25,55,75,0.09)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-slate-400">Tiket Masuk</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#263746]">{{ $ticketMasukCount }}</h2>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-700">
                        @if($ticketMasukCount > 0)
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span> Menunggu triage
                        @else
                            Antrean bersih
                        @endif
                    </span>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#d3e7f062] text-lg text-[#26556b]"><i class="fas fa-inbox"></i></span>
            </section>

            <!-- Sedang Diproses -->
            <section class="flex min-h-[142px] items-start justify-between rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_12px_26px_rgba(25,55,75,0.09)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-slate-400">Sedang Diproses</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#263746]">{{ $inProcessCount }}</h2>
                    <span class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-[#347d9f]">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#26556b]"></span> Timer SLA berjalan
                    </span>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#d3e7f062] text-lg text-[#26556b]"><i class="fas fa-gear"></i></span>
            </section>

            <!-- Bekukan SLA -->
            <section class="flex min-h-[142px] items-start justify-between rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_12px_26px_rgba(25,55,75,0.09)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-slate-400">Bekukan SLA</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#263746]">{{ $pendingCount }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-700">SLA dijeda / dilanjut besok</span>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#d3e7f062] text-lg text-[#26556b]"><i class="fas fa-pause-circle"></i></span>
            </section>

            <!-- Forward Ticket -->
            <section class="flex min-h-[142px] items-start justify-between rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_12px_26px_rgba(25,55,75,0.09)]">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-slate-400">Forward Ticket</p>
                    <h2 class="mt-2 text-3xl font-extrabold leading-none tracking-tight text-[#263746]">{{ $forwardCount }}</h2>
                    <span class="mt-3 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-[#347d9f]">Disposisi lintas bidang</span>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#d3e7f062] text-lg text-[#26556b]"><i class="fas fa-shuffle"></i></span>
            </section>
        </div>

        <!-- Beban Teknisi -->
        <section class="rounded-2xl border border-[#dce5eb] bg-white p-5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-6">
            <div class="mb-4 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="flex items-center gap-2 text-sm font-bold text-[#1d405c]"><i class="fas fa-users-gear text-[#347d9f]"></i> Ketersediaan & Beban Teknisi Bidang</h2>
                    <p class="mt-1 text-[11px] text-slate-400">Jumlah penugasan tiket aktif per teknisi saat ini.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">{{ count($technicians) }} Teknisi Siap</span>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($technicians as $tech)
                    @php
                        $activeTasks = $tickets->filter(function($t) use ($tech) {
                            return in_array($tech->id, $t->assigned_tech_ids ?? []);
                        })->count();
                    @endphp
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-[#e2eaf0] bg-[#f7f9fb] p-3.5 transition hover:border-[#c7dce8] hover:bg-[#f1f6f9]">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#347d9f] text-xs font-extrabold text-white shadow-sm">{{ strtoupper(substr($tech->name, 0, 2)) }}</div>
                            <div class="min-w-0">
                                <h4 class="truncate text-xs font-bold text-slate-800">{{ $tech->name }}</h4>
                                <span class="text-[10px] text-slate-400">Teknisi Helpdesk</span>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $activeTasks > 0 ? 'border-blue-200 bg-blue-50 text-[#1d6080]' : 'border-slate-200 bg-slate-100 text-slate-500' }}">{{ $activeTasks }} Tiket</span>
                    </div>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-200 py-8 text-center text-xs italic text-slate-400">Belum ada data teknisi yang terdaftar.</div>
                @endforelse
            </div>
        </section>

        <!-- Antrean Tiket Aktif -->
        <section class="overflow-hidden rounded-2xl border border-[#dce5eb] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-bold text-[#1d405c]">Antrean Tiket Aktif</h2>
                    <p class="mt-1 text-[11px] text-slate-400">Monitoring batas durasi SLA dan penanganan teknisi.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[10px] font-bold text-slate-600">Total: {{ $tickets->count() }} Permohonan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-xs">
                    <thead class="border-b border-slate-200 bg-[#f7f9fb] text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Tiket</th>
                            <th class="px-5 py-4">Permohonan & OPD</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Target & Progress SLA</th>
                            <th class="px-5 py-4">Teknisi Bertugas</th>
                            <th class="px-5 py-4">Estimasi Tenggat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tickets as $ticket)
                            @php
                                $assignedTechs = $ticket->assigned_technicians ?? [];
                                $isAssigned = !empty($assignedTechs);
                                $isPending = ($ticket->ticket_status_id == 3);
                            @endphp
                            <tr class="transition hover:bg-[#f7fafc] {{ $isPending ? 'bg-amber-50/20' : '' }}">
                                <td class="px-5 py-4 align-middle font-mono font-bold text-[#347d9f]"><a href="{{ route('bidang.triage') }}" class="hover:underline">{{ $ticket->ticket_number }}</a></td>
                                <td class="px-5 py-4 align-middle">
                                    <div class="font-bold leading-snug text-slate-800">{{ $ticket->subject }}</div>
                                    <div class="mt-1 text-[10px] text-slate-400">{{ $ticket->user->agency ?? $ticket->user->organization ?? 'Perangkat Daerah Terkait' }}</div>
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    @if($ticket->ticket_status_id == 2)
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span> DIPROSES</span>
                                    @elseif($ticket->ticket_status_id == 3)
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700"><i class="fas fa-pause-circle text-[10px]"></i> DIJEDA</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-bold text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> {{ $ticket->status->name ?? 'AKTIF' }}</span>
                                    @endif
                                </td>
                                <td class="min-w-[230px] px-5 py-4 align-middle">
                                    <div class="mb-1.5 flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-1 text-[11px] font-bold text-{{ $ticket->sla_color }}-600">
                                            @if($isPending)<i class="fas fa-pause-circle text-amber-500"></i>@endif
                                            {{ $ticket->sla_label }}
                                        </span>
                                        <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-[10px] font-semibold text-slate-500">Target {{ $ticket->target_sla_label }}</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-1.5 rounded-full bg-{{ $ticket->sla_color }}-500 transition-all duration-500" style="width: {{ $ticket->sla_progress }}%"></div>
                                    </div>
                                    <div class="mt-1 font-mono text-[10px] text-slate-400">{{ $ticket->sla_info }}</div>
                                    @if($isPending && !empty($ticket->pending_note))
                                        <div class="mt-0.5 max-w-[200px] truncate text-[10px] text-amber-700" title="{{ $ticket->pending_note }}">Catatan: {{ $ticket->pending_note }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    @if($isAssigned)
                                        <div class="flex items-center gap-1.5 font-semibold text-slate-700"><i class="fas fa-user-check text-[11px] text-[#347d9f]"></i><span>{{ implode(', ', $assignedTechs) }}</span></div>
                                    @else
                                        <span class="flex items-center gap-1 text-[11px] font-medium italic text-rose-500"><i class="fas fa-exclamation-circle text-[10px]"></i> Belum ditugaskan</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    @if($isPending)
                                        <div class="flex items-center gap-1 text-xs font-semibold text-amber-600"><i class="fas fa-snowflake text-[10px]"></i> Waktu Dibekukan</div>
                                        <div class="text-[10px] text-slate-400">Dilanjutkan saat aktif kembali</div>
                                    @elseif(!empty($ticket->task_started_at))
                                        @php
                                            $dueTime = \Carbon\Carbon::parse($ticket->task_started_at)->addMinutes($ticket->target_sla_minutes);
                                            $isOverdue = now()->greaterThan($dueTime);
                                        @endphp
                                        <div class="font-mono text-xs font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-700' }}">{{ $dueTime->translatedFormat('d M, H:i') }} WITA</div>
                                        <div class="text-[10px] {{ $isOverdue ? 'font-semibold text-rose-500' : 'text-slate-400' }}">{{ $isOverdue ? 'Lewat batas waktu' : 'Maksimal selesai' }}</div>
                                    @else
                                        <span class="text-xs italic text-slate-400">Menunggu teknisi mulai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center text-xs italic text-slate-400">Belum ada permohonan tiket aktif pada penanganan bidang ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection