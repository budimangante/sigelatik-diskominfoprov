@extends('components.b-master')

@section('title', 'Pusat Notifikasi')
@section('subtitle', 'Daftar pemberitahuan tiket, status SLA, dan pengumuman sistem')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">SUPER ADMIN</span>
                <span class="text-slate-300">•</span>
                <span>INBOX & MONITORING SISTEM</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Pusat Notifikasi
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau peringatan tiket kritis, disposisi bidang, serta arsip siaran ke seluruh unit kerja.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            @if($unreadCount > 0)
                <form action="{{ route('notifikasi.bacaSemua') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                        <i class="fas fa-check-double text-blue-600 text-xs"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif

            <button type="button" 
                    onclick="bukaModalBroadcast()"
                    class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
                <i class="fas fa-bullhorn text-xs"></i>
                <span>Siarkan Pengumuman</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- Tab Filter -->
    <div class="flex items-center gap-2 mb-6 border-b border-slate-200 pb-3 flex-wrap">
        <!-- 1. Inbox Super Admin -->
        <a href="{{ route('notifikasi', ['filter' => 'all']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filter === 'all' ? 'bg-[#1a3668] text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Notifikasi Masuk</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $totalCount }}
            </span>
        </a>

        <!-- 2. Belum Dibaca -->
        <a href="{{ route('notifikasi', ['filter' => 'unread']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filter === 'unread' ? 'bg-[#1a3668] text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500 text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>

        <!-- 3. Monitoring Seluruh Sistem (Global Log) -->
        <a href="{{ route('notifikasi', ['filter' => 'global']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $filter === 'global' ? 'bg-[#1a3668] text-white shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            <i class="fas fa-globe text-xs"></i>
            <span>Log Seluruh Notifikasi Sistem</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'global' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $globalCount }}
            </span>
        </a>
    </div>

    <!-- Daftar List Notifikasi -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            @php
                $data = $notif->data;
                $isUnread = is_null($notif->read_at);
                $category = $data['category'] ?? 'system';

                [$icon, $iconColor, $bgColor] = match($category) {
                    'ticket'    => ['fa-ticket-alt', 'text-blue-600', 'bg-blue-50 border-blue-100'],
                    'sla'       => ['fa-exclamation-triangle', 'text-amber-600', 'bg-amber-50 border-amber-100'],
                    'broadcast' => ['fa-bullhorn', 'text-purple-600', 'bg-purple-50 border-purple-100'],
                    default     => ['fa-info-circle', 'text-emerald-600', 'bg-emerald-50 border-emerald-100'],
                };
            @endphp

            <div class="bg-white rounded-2xl border transition-all duration-150 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'border-blue-200 bg-blue-50/20 shadow-2xs' : 'border-slate-200/90 shadow-2xs' }}">
                <div class="flex items-start gap-4">
                    <!-- Icon Bulat -->
                    <div class="w-10 h-10 rounded-xl {{ $bgColor }} {{ $iconColor }} border flex items-center justify-center text-sm shrink-0 mt-0.5">
                        <i class="fas {{ $icon }}"></i>
                    </div>

                    <!-- Informasi Pesan -->
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-xs font-bold text-slate-800 {{ $isUnread ? 'font-extrabold' : '' }}">
                                {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                            </h4>

                            @if($isUnread)
                                <span class="w-2 h-2 rounded-full bg-blue-600 inline-block" title="Belum dibaca"></span>
                            @endif

                            <!-- Target Label (Khusus Teknisi / OPD / Semua) -->
                            @if(!empty($data['target_label']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ $data['target_label'] }}
                                </span>
                            @endif

                            <!-- Penerima (Jika Sedang Melihat Tab Log Global) -->
                            @if($filter === 'global' && $notif->notifiable)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    Penerima: {{ $notif->notifiable->name }}
                                </span>
                            @endif

                            <span class="text-[11px] text-slate-400 font-mono">• {{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $data['message'] ?? '-' }}
                        </p>
                        @if(!empty($data['sender']))
                            <div class="text-[10px] text-slate-400 font-medium mt-1">
                                Disiarkan oleh: <span class="font-bold text-slate-600">{{ $data['sender'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                    @if($isUnread && $filter !== 'global')
                        <form action="{{ route('notifikasi.baca', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition cursor-pointer"
                                    title="Tandai Sudah Dibaca">
                                <i class="fas fa-check text-[11px]"></i>
                                <span class="ml-1">Tandai Dibaca</span>
                            </button>
                        </form>
                    @endif

                    @if(!empty($data['url']) && $data['url'] !== route('notifikasi'))
                        <a href="{{ $data['url'] }}" 
                           class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition"
                           title="Buka Tautan Terkait">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    @endif

                    <form action="{{ route('notifikasi.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer"
                                title="Hapus Notifikasi">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-2xs">
                <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="far fa-bell-slash"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak ada notifikasi</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    {{ $filter === 'unread' ? 'Semua notifikasi baru Anda telah selesai dibaca.' : 'Belum ada riwayat pemberitahuan tiket atau aktivitas masuk.' }}
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

<!-- ================= MODAL: SIARKAN PENGUMUMAN SISTEM ================= -->
<div id="modalBroadcast" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-bullhorn text-blue-200"></i>
                    <span>Siarkan Pengumuman Sistem</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Kirim pesan notifikasi serentak ke akun pengguna di SIGELATIK.</p>
            </div>
            <button type="button" onclick="tutupModal('modalBroadcast')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('notifikasi.broadcast') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Target Penerima <span class="text-rose-500">*</span>
                </label>
                <select name="target_role" 
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="all">Semua Pengguna Terdaftar</option>
                    <option value="teknisi">Khusus Teknisi Helpdesk</option>
                    <option value="asn">Khusus Pegawai ASN / OPD</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Salinan pengumuman akan otomatis tersimpan di riwayat Super Admin.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" required placeholder="Contoh: Pemeliharaan Server Sistem (Maintenance)"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pesan Pemberitahuan <span class="text-rose-500">*</span>
                </label>
                <textarea name="message" rows="4" required placeholder="Tuliskan pesan yang akan disiarkan..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalBroadcast')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalBroadcast() {
        document.getElementById('modalBroadcast').classList.remove('hidden');
    }

    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection