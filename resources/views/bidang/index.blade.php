@extends('components.b-master')

@section('title', 'Overview Tiket Bidang')
@section('subtitle', 'Monitoring kinerja SLA dan penugasan tiket Helpdesk Bidang')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">HELPDESK BIDANG</span>
                <span class="text-slate-300">•</span>
                <span>OVERVIEW & MONITORING</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Overview Kinerja Tiket
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau distribusi penanganan, kepatuhan batas waktu SLA, serta penugasan teknisi lapangan secara real-time.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('bidang.triage') }}" 
               class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-2xs transition">
                <i class="fas fa-clipboard-check text-blue-200"></i>
                <span>Buka Triage Tiket</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @php
        $totalAktif = $tickets->count();
        $inProcessCount = $tickets->where('ticket_status_id', 2)->count();
        $pendingCount = $tickets->where('ticket_status_id', 3)->count();
        $unassignedCount = $tickets->filter(fn($t) => empty($t->assigned_technicians))->count();
    @endphp

    <!-- 1. KARTU METRIK KPI RINGKASAN -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tiket Aktif Bidang</p>
                <h3 class="text-2xl font-extrabold text-[#1a3668] mt-1">{{ $totalAktif }}</h3>
                <span class="text-[10px] text-slate-500 font-medium mt-1 inline-block">Dalam pengawasan aktif</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-[#1a3668] text-lg shrink-0">
                <i class="fas fa-ticket-alt"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</p>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $inProcessCount }}</h3>
                <span class="text-[10px] text-blue-500 font-medium mt-1 inline-block flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span> Timer SLA berjalan
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-lg shrink-0">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dijeda (Pending)</p>
                <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $pendingCount }}</h3>
                <span class="text-[10px] text-amber-600 font-medium mt-1 inline-block">Menunggu pihak OPD/vendor</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-lg shrink-0">
                <i class="fas fa-pause-circle"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Belum Ditugaskan</p>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $unassignedCount }}</h3>
                <span class="text-[10px] text-rose-500 font-medium mt-1 inline-block">Memerlukan alokasi staf</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 text-lg shrink-0">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>

    <!-- 2. DISTRIBUSI BEBAN TEKNISI BIDANG (FULL WIDTH) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs mb-8">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Ketersediaan & Beban Teknisi Bidang</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Jumlah penugasan tiket aktif per teknisi saat ini.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg">
                {{ count($technicians) }} Teknisi Siap
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
            @forelse($technicians as $tech)
                @php
                    $activeTasks = $tickets->filter(function($t) use ($tech) {
                        return in_array($tech->id, $t->assigned_tech_ids ?? []);
                    })->count();
                @endphp
                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#1a3668] text-white font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr($tech->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 leading-snug">{{ $tech->name }}</h4>
                            <span class="text-[10px] text-slate-400">Teknisi Helpdesk</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $activeTasks > 0 ? 'bg-blue-100 text-[#1a3668]' : 'bg-slate-200/70 text-slate-600' }}">
                            {{ $activeTasks }} Tiket
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-slate-400 italic">
                    Belum ada data teknisi yang terdaftar.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 3. TABEL UTAMA MONITORING TIKET BIDANG -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800">Antrean Tiket Aktif</h2>
                <p class="text-xs text-slate-400 mt-0.5">Monitoring batas durasi SLA dan penanganan teknisi.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                Total: {{ $tickets->count() }} Permohonan
            </span>
        </div>

        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 w-36">TIKET</th>
                    <th class="px-6 py-4">PERMOHONAN & OPD</th>
                    <th class="px-6 py-4 w-40">STATUS</th>
                    <th class="px-6 py-4 w-64">TARGET & PROGRESS SLA</th>
                    <th class="px-6 py-4 w-52">TEKNISI BERTUGAS</th>
                    <th class="px-6 py-4 w-48">ESTIMASI TENGGAT</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse ($tickets as $ticket)
                    @php
                        $assignedTechs = $ticket->assigned_technicians ?? [];
                        $isAssigned = !empty($assignedTechs);
                        $isProcess = $ticket->ticket_status_id == 2;
                        $isPending = $ticket->ticket_status_id == 3;
                        $hasStarted = !empty($ticket->task_started_at);

                        $targetMinutes = max(1, $ticket->target_sla_minutes ?? 240);
                        $percent = 0;

                        $slaLabel = 'Belum berjalan';
                        $slaTextClass = 'text-slate-400 font-medium';
                        $slaBarColor = 'bg-slate-200';

                        if ($hasStarted) {
                            $startTime = \Carbon\Carbon::parse($ticket->task_started_at);

                            if ($isPending) {
                                $slaLabel = 'Dijeda (Pending)';
                                $slaTextClass = 'text-amber-600 font-bold';
                                $slaBarColor = 'bg-amber-500';

                                $pauseTime = \Carbon\Carbon::parse($ticket->updated_at);
                                $elapsedMinutes = max(0, $startTime->diffInMinutes($pauseTime));
                                $percent = min(100, (int) round(($elapsedMinutes / $targetMinutes) * 100));
                            } elseif ($isProcess) {
                                $elapsedMinutes = max(0, $startTime->diffInMinutes(now()));
                                $percent = min(100, (int) round(($elapsedMinutes / $targetMinutes) * 100));

                                if ($percent >= 100) {
                                    $slaLabel = 'Terlampaui (100%)';
                                    $slaTextClass = 'text-rose-600 font-bold';
                                    $slaBarColor = 'bg-rose-500';
                                } elseif ($percent >= 80) {
                                    $slaLabel = 'Kritis (' . $percent . '%)';
                                    $slaTextClass = 'text-rose-500 font-bold';
                                    $slaBarColor = 'bg-rose-500';
                                } else {
                                    $slaLabel = 'Berjalan (' . $percent . '%)';
                                    $slaTextClass = 'text-[#1a3668] font-bold';
                                    $slaBarColor = 'bg-[#1a3668]';
                                }
                            }
                        }
                    @endphp

                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Nomor Tiket -->
                        <td class="px-6 py-4 text-blue-600 font-bold align-middle font-mono">
                            <a href="#" class="hover:underline">{{ $ticket->ticket_number }}</a>
                        </td>

                        <!-- Judul & Asal Perangkat Daerah -->
                        <td class="px-6 py-4 align-middle">
                            <div class="font-bold text-slate-800 leading-snug">{{ $ticket->subject }}</div>
                            <div class="text-[11px] text-slate-400 mt-1">
                                {{ $ticket->user->agency ?? $ticket->user->organization ?? 'Perangkat Daerah Terkait' }}
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 align-middle">
                            @if($ticket->ticket_status_id == 1)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-cyan-200 text-cyan-700 bg-cyan-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> BARU
                                </span>
                            @elseif($ticket->ticket_status_id == 2)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-blue-200 text-blue-700 bg-blue-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span> DIPROSES
                                </span>
                            @elseif($ticket->ticket_status_id == 3)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-amber-200 text-amber-700 bg-amber-50">
                                    <i class="fas fa-pause-circle text-[10px]"></i> DIJEDA
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border border-slate-200 text-slate-600 bg-slate-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> {{ $ticket->status->name ?? 'NEW' }}
                                </span>
                            @endif
                        </td>

                        <!-- SLA (Dinamis: Berjalan / Dijeda / Belum Berjalan) -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-between items-center gap-2 mb-1.5 text-xs">
                                <span class="{{ $slaTextClass }} flex items-center gap-1">
                                    @if($isPending && $hasStarted)
                                        <i class="fas fa-pause-circle text-amber-500 text-[11px]"></i>
                                    @endif
                                    {{ $slaLabel }}
                                </span>
                                <span class="text-slate-500 font-mono text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold">
                                    target {{ $ticket->target_sla_label }}
                                </span>
                            </div>
                            <div class="w-48 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $slaBarColor }} h-1.5 rounded-full transition-all duration-500" 
                                     style="width: {{ $hasStarted ? max(8, $percent) : 0 }}%"></div>
                            </div>
                        </td>

                        <!-- Kolom Teknisi -->
                        <td class="px-6 py-4 align-middle">
                            @if($isAssigned)
                                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-user-check text-blue-600 text-[11px]"></i>
                                    <span>{{ implode(', ', $assignedTechs) }}</span>
                                </div>
                            @else
                                <span class="text-rose-500 italic font-medium flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> Belum ditugaskan
                                </span>
                            @endif
                        </td>

                        <!-- Kolom Estimasi Tenggat (Due At) -->
                        <td class="px-6 py-4 align-middle">
                            @if($hasStarted)
                                @php
                                    $dueTime = \Carbon\Carbon::parse($ticket->task_started_at)->addMinutes($targetMinutes);
                                    $isOverdue = now()->greaterThan($dueTime);
                                @endphp
                                <div class="font-mono text-xs font-bold {{ $isOverdue ? 'text-rose-600' : 'text-slate-700' }}">
                                    {{ $dueTime->translatedFormat('d M, H:i') }} WITA
                                </div>
                                <div class="text-[10px] {{ $isOverdue ? 'text-rose-500 font-semibold' : 'text-slate-400' }}">
                                    {{ $isOverdue ? 'Lewat batas waktu' : 'Maksimal selesai' }}
                                </div>
                            @else
                                <span class="text-slate-400 text-xs italic">Menunggu teknisi mulai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400 text-xs italic">
                            Belum ada permohonan tiket aktif pada penanganan bidang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection