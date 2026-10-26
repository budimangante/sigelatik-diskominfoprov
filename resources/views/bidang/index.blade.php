@extends('components.b-master')

@section('title', 'Tiket Bidang')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>Helpdesk Bidang</span>
            <span class="text-gray-300">•</span>
            <span>Layanan Aplikasi</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Tiket bidang</h1>
        <p class="text-gray-500 text-sm">Monitor seluruh tiket beserta waktu SLA, lalu tugaskan atau sesuaikan target SLA penanganan.</p>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium tracking-wider">Tiket</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Judul</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Status</th>
                    <th class="px-6 py-4 font-medium tracking-wider w-64">SLA</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Teknisi</th>
                    <th class="px-6 py-4 font-medium tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-gray-100 text-gray-600">
                @forelse ($tickets as $ticket)
                    @php
                        $assignedTechs = $ticket->assigned_technicians ?? [];
                        $isAssigned = !empty($assignedTechs);
                        
                        $isProcess = $ticket->ticket_status_id == 2;
                        $isPending = $ticket->ticket_status_id == 3;
                        $hasStarted = !empty($ticket->task_started_at);

                        $targetMinutes = max(1, $ticket->target_sla_minutes ?? 240);
                        $percent = 0;

                        // Default State: Belum Berjalan (Tiket baru / teknisi belum mulai kerja)
                        $slaLabel = 'Belum berjalan';
                        $slaTextClass = 'text-gray-400 font-medium';
                        $slaBarColor = 'bg-gray-200';

                        if ($hasStarted) {
                            $startTime = \Carbon\Carbon::parse($ticket->task_started_at);

                            if ($isPending) {
                                // PENDING: Timer dibekukan pada saat tiket dialihkan ke PENDING
                                $slaLabel = 'Dijeda (Pending)';
                                $slaTextClass = 'text-amber-600 font-bold';
                                $slaBarColor = 'bg-amber-500';

                                $pauseTime = \Carbon\Carbon::parse($ticket->updated_at);
                                $elapsedMinutes = max(0, $startTime->diffInMinutes($pauseTime));
                                $percent = min(100, round(($elapsedMinutes / $targetMinutes) * 100));

                            } elseif ($isProcess) {
                                // PROCESS: Timer aktif berjalan menghitung sampai waktu sekarang
                                $slaLabel = 'Berjalan';
                                $slaTextClass = 'text-[#1a3668] font-bold';
                                $slaBarColor = 'bg-[#1a3668]';

                                $elapsedMinutes = max(0, $startTime->diffInMinutes(now()));
                                $percent = min(100, round(($elapsedMinutes / $targetMinutes) * 100));
                            }
                        }
                    @endphp

                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Nomor Tiket -->
                        <td class="px-6 py-4 text-blue-600 font-medium align-top font-mono">
                            <a href="#" class="hover:underline">{{ $ticket->ticket_number }}</a>
                        </td>

                        <!-- Judul & Asal Perangkat Daerah -->
                        <td class="px-6 py-4 align-top">
                            <div class="font-bold text-gray-800 leading-snug">{{ $ticket->subject }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $ticket->user->agency ?? $ticket->user->organization ?? 'Perangkat Daerah Terkait' }}
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 align-top">
                            @if($ticket->ticket_status_id == 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-cyan-200 text-cyan-700 bg-cyan-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> NEW
                                </span>
                            @elseif($ticket->ticket_status_id == 2)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-blue-200 text-blue-700 bg-blue-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> PROCESS
                                </span>
                            @elseif($ticket->ticket_status_id == 3)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-yellow-200 text-yellow-700 bg-yellow-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> PENDING
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-gray-200 text-gray-600 bg-gray-50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> {{ $ticket->status->name ?? 'NEW' }}
                                </span>
                            @endif
                        </td>

                        <!-- SLA (Dinamis: Berjalan / Dijeda / Belum Berjalan) -->
                        <td class="px-6 py-4 align-top">
                            <div class="flex justify-between items-center gap-2 mb-1.5 text-xs">
                                <span class="{{ $slaTextClass }} flex items-center gap-1">
                                    @if($isPending && $hasStarted)
                                        <i class="fas fa-pause-circle text-amber-500 text-[11px]"></i>
                                    @endif
                                    {{ $slaLabel }}
                                </span>
                                <span class="text-gray-500 font-mono text-[11px] bg-slate-100 px-2 py-0.5 rounded font-semibold">
                                    target {{ $ticket->target_sla_label }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $slaBarColor }} h-1.5 rounded-full transition-all duration-500" 
                                     style="width: {{ $hasStarted ? max(8, $percent) : 0 }}%"></div>
                            </div>
                        </td>

                        <!-- Kolom Teknisi -->
                        <td class="px-6 py-4 align-top">
                            @if($isAssigned)
                                <span class="text-gray-800 font-medium">{{ implode(', ', $assignedTechs) }}</span>
                            @else
                                <span class="text-gray-400 italic">Belum ditugaskan</span>
                            @endif
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-6 py-4 align-top text-center">
                            <button type="button" 
                                    onclick="bukaModal({{ $ticket->id }}, '{{ $ticket->ticket_number }}', {{ $ticket->target_sla_minutes }}, {{ json_encode($ticket->assigned_tech_ids ?? []) }})" 
                                    class="px-4 py-1.5 border border-[#1a3668] text-[#1a3668] rounded-md hover:bg-[#1a3668] hover:text-white transition-colors text-xs font-semibold">
                                {{ $isAssigned ? 'Reassign' : 'Assign' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-sm">
                            Belum ada permohonan tiket yang masuk ke bidang ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL ASSIGN TEKNISI & PENYESUAIAN SLA -->
    <div id="modalPenugasan" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden transition-opacity p-4">
        <form id="formPenugasan" method="POST" action="" class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            @csrf

            <!-- Header Modal -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold text-[#1a3668]">Penugasan Tiket <span id="modalIdTiket" class="font-mono"></span></h2>
                    <p class="text-sm text-gray-500 mt-0.5">Tentukan teknisi dan sesuaikan target durasi SLA pengerjaan.</p>
                </div>
                <button type="button" onclick="tutupModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">
                <!-- Pilihan Teknisi -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pilih Teknisi Bidang</label>
                    <div class="space-y-2.5">
                        @forelse($technicians as $tech)
                            <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-xl cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                                <input type="checkbox" 
                                       name="technician_ids[]" 
                                       value="{{ $tech->id }}" 
                                       class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer tech-checkbox">
                                <span class="text-sm font-semibold text-gray-700">{{ $tech->name }}</span>
                            </label>
                        @empty
                            <div class="p-4 text-center text-xs text-gray-400 border border-dashed rounded-xl">
                                Belum ada pengguna dengan role teknisi di bidang ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Input Target SLA Tiket (Dapat Diubah Kapan Saja) -->
                <div class="pt-2 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Target SLA Tiket Ini (Menit)
                    </label>
                    <div class="relative">
                        <input type="number" 
                               name="sla_minutes" 
                               id="modalSlaMinutes" 
                               min="5" 
                               step="5" 
                               required
                               class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] font-mono font-semibold">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold pointer-events-none">Menit</span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Diisi otomatis dari acuan SOP layanan. Anda dapat menyesuaikannya khusus untuk tiket ini meskipun teknisi belum dipilih.
                    </p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/50">
                <button type="button" onclick="tutupModal()" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition-colors text-sm font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#1a3668] text-white rounded-lg hover:bg-[#112445] transition-colors text-sm font-semibold shadow-md">
                    Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModal(ticketId, ticketNumber, targetMinutes, currentTechIds = []) {
        document.getElementById('modalIdTiket').innerText = ticketNumber;
        document.getElementById('formPenugasan').action = '/bidang/tickets/' + ticketId + '/assign';
        
        // Pasang target menit aktif tiket ke input field modal
        document.getElementById('modalSlaMinutes').value = targetMinutes || 240;

        // Centang otomatis teknisi yang sebelumnya sudah terikat
        const checkboxes = document.querySelectorAll('#modalPenugasan .tech-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = currentTechIds.includes(parseInt(cb.value));
        });

        document.getElementById('modalPenugasan').classList.remove('hidden');
    }

    function tutupModal() {
        document.getElementById('modalPenugasan').classList.add('hidden');
    }
</script>
@endsection