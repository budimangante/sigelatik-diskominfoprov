@extends('components.b-master')

@section('title', 'Gangguan Layanan')
@section('subtitle', 'Pusat Status dan Monitoring Gangguan Layanan TIK')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans space-y-6">

    <!-- 1. Header & Tombol Laporkan Insiden -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1">
                <span class="text-[#1a3668]">STATUS PAGE</span>
                <span class="text-slate-300">•</span>
                <span>MONITORING INSIDEN & DOWNTIME</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Pusat Gangguan Layanan
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pemantauan status operasional layanan publik, server, jaringan, dan eskalasi perbaikan teknis.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalLaporkanGangguan()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer shrink-0">
            <i class="fas fa-bullhorn text-sm"></i>
            <span>Siarkan Gangguan Baru</span>
        </button>
    </div>

    <!-- 2. System Operational Status Banner -->
    <div class="p-5 rounded-2xl border {{ $gangguanData['system_status']['color'] === 'emerald' ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $gangguanData['system_status']['color'] === 'emerald' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center text-xl shrink-0 shadow-xs">
                <i class="fas {{ $gangguanData['system_status']['color'] === 'emerald' ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider {{ $gangguanData['system_status']['color'] === 'emerald' ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $gangguanData['system_status']['badge'] }}
                </span>
                <h3 class="text-lg font-black text-slate-800">
                    {{ $gangguanData['system_status']['text'] }}
                </h3>
            </div>
        </div>

        <div class="flex items-center gap-6 border-t sm:border-t-0 sm:border-l border-slate-200/60 pt-3 sm:pt-0 sm:pl-6 text-xs">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Uptime Bulan Ini</p>
                <p class="text-base font-black text-[#1a3668] mt-0.5">{{ $gangguanData['uptime_percentage'] }}</p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase">Rata-rata Resolusi (MTTR)</p>
                <p class="text-base font-black text-slate-800 mt-0.5">{{ $gangguanData['avg_mttr'] }}</p>
            </div>
        </div>
    </div>

    <!-- 3. Gangguan yang Sedang Aktif (Active Incidents) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#1a3668] uppercase tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                <span>Gangguan Sedang Berlangsung ({{ count($gangguanData['active_incidents']) }})</span>
            </h2>
            <span class="text-[11px] text-slate-400">Diperbarui real-time</span>
        </div>

        @forelse($gangguanData['active_incidents'] as $incident)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            {{ $incident['severity'] }}
                        </span>
                        <h3 class="text-base font-extrabold text-slate-800">
                            {{ $incident['title'] }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fas fa-clock text-slate-400"></i>
                        <span>Dimulai: {{ $incident['started_at'] }} ({{ $incident['duration'] }})</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    {{ $incident['description'] }}
                </p>

                <!-- Status Detail Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Layanan Terdampak</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $incident['service_name'] }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Target OPD Terdampak</span>
                        <p class="font-bold text-slate-700 mt-0.5">{{ $incident['impacted_opd'] }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Estimasi Pemulihan (ETA)</span>
                        <p class="font-bold text-emerald-600 mt-0.5 flex items-center gap-1">
                            <i class="fas fa-wrench text-[10px]"></i>
                            <span>{{ $incident['eta'] }}</span>
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-xs shadow-2xs italic">
                Tidak ada gangguan layanan yang sedang berlangsung saat ini.
            </div>
        @endforelse
    </div>

    <!-- 4. Riwayat Insiden Sebelumnya (Past Incidents) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-history text-[#1a3668]"></i>
                <span>Riwayat Pemulihan Gangguan (7 Hari Terakhir)</span>
            </h3>
            <span class="text-[10px] text-slate-400">Log Resolusi</span>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($gangguanData['resolved_incidents'] as $past)
                <div class="p-5 hover:bg-slate-50/60 transition flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h4 class="text-xs font-bold text-slate-800">{{ $past['title'] }}</h4>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-mono">{{ $past['service_name'] }}</span>
                        </div>
                        <p class="text-xs text-slate-500 pl-4">{{ $past['resolution'] }}</p>
                    </div>

                    <div class="text-right shrink-0 pl-4">
                        <span class="text-[11px] font-bold text-slate-600 block">{{ $past['date'] }}</span>
                        <span class="text-[10px] text-slate-400">Downtime: {{ $past['downtime'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Modal: Laporkan / Siarkan Gangguan Baru -->
<div id="modalLaporkanGangguan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-bullhorn text-rose-300"></i>
                <span>Siarkan Gangguan Layanan</span>
            </h3>
            <button type="button" onclick="tutupModal('modalLaporkanGangguan')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Insiden Gangguan</label>
                <input type="text" name="title" required placeholder="Contoh: Gangguan Akses Portal SIMDA"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tingkat Dampak</label>
                    <select name="severity" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                        <option value="Kritis">Kritis (Layanan Lumpuh)</option>
                        <option value="Tinggi">Tinggi (Sebagian Fitur Rusak)</option>
                        <option value="Sedang">Sedang (Performa Lambat)</option>
                        <option value="Pemeliharaan">Pemeliharaan Terjadwal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimasi Pemulihan (ETA)</label>
                    <input type="text" name="eta" placeholder="Contoh: 2 Jam (14:00 WITA)"
                           class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi & Dampak ke OPD</label>
                <textarea name="description" rows="3" required placeholder="Jelaskan kendala teknis dan perangkat daerah mana saja yang terdampak..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:border-[#1a3668] resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalLaporkanGangguan')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Siarkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalLaporkanGangguan() {
        document.getElementById('modalLaporkanGangguan').classList.remove('hidden');
    }
    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection