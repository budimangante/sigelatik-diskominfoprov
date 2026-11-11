@extends('components.b-master')

@section('title', 'Pusat Notifikasi')
@section('subtitle', 'Daftar pemberitahuan tiket, status SLA, dan pengumuman sistem')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] px-4 py-6 font-sans sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1900px]">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-[#397b9d] shadow-sm">
                <span class="text-[#1a3668]">SUPER ADMIN</span>
                <span class="text-slate-300">•</span>
                <span>INBOX & MONITORING SISTEM</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Pusat Notifikasi
            </h1>
            <p class="mt-1 text-xs text-[#71808c] sm:text-sm">
                Pantau peringatan tiket kritis, disposisi bidang, serta arsip siaran ke seluruh unit kerja.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 sm:pt-10">
            <button type="button" 
                    onclick="bukaModalBroadcast()"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2f789e] px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-[#2f789e]/20 transition hover:bg-[#245f80] cursor-pointer">
                <i class="fas fa-bullhorn text-xs"></i>
                <span>Siarkan Pengumuman</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-5 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-xs font-semibold text-emerald-800 shadow-sm">
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
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-[#d8e1e7] pb-4">
        <div class="flex flex-wrap items-center gap-2">
        <!-- 1. Inbox Super Admin -->
        <a href="{{ route('notifikasi', ['filter' => 'all']) }}" 
           class="inline-flex items-center gap-2 rounded-full border px-4 py-2.5 text-xs font-bold transition {{ $filter === 'all' ? 'border-[#2f789e] bg-[#2f789e] text-white shadow-md shadow-[#2f789e]/15' : 'border-[#dce4e9] bg-white text-[#526775] hover:bg-[#f7fafc]' }}">
            <span>Notifikasi Masuk</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $totalCount }}
            </span>
        </a>

        <!-- 2. Belum Dibaca -->
        <a href="{{ route('notifikasi', ['filter' => 'unread']) }}" 
           class="inline-flex items-center gap-2 rounded-full border px-4 py-2.5 text-xs font-bold transition {{ $filter === 'unread' ? 'border-[#2f789e] bg-[#2f789e] text-white shadow-md shadow-[#2f789e]/15' : 'border-[#dce4e9] bg-white text-[#526775] hover:bg-[#f7fafc]' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500 text-white">
                    {{ $unreadCount }}
                </span>
            @endif
        </a>

        <!-- 3. Monitoring Seluruh Sistem (Global Log) -->
        <a href="{{ route('notifikasi', ['filter' => 'global']) }}" 
           class="inline-flex items-center gap-2 rounded-full border px-4 py-2.5 text-xs font-bold transition {{ $filter === 'global' ? 'border-[#2f789e] bg-[#2f789e] text-white shadow-md shadow-[#2f789e]/15' : 'border-[#dce4e9] bg-white text-[#526775] hover:bg-[#f7fafc]' }}">
            <i class="fas fa-globe text-xs"></i>
            <span>Log Seluruh Notifikasi Sistem</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $filter === 'global' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $globalCount }}
            </span>
        </a>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('notifikasi.bacaSemua') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-2 py-1.5 text-[11px] font-semibold text-[#71808c] transition hover:text-[#2f789e] cursor-pointer">
                    <i class="fas fa-check-double text-[#397b9d]"></i>
                    <span>Tandai semua dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Daftar List Notifikasi -->
    <div class="space-y-3.5">
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

            <article class="group flex flex-col justify-between gap-4 rounded-2xl border bg-white p-4 shadow-[0_3px_12px_rgba(25,55,75,0.045)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(25,55,75,0.08)] sm:flex-row sm:items-center sm:p-5 {{ $isUnread ? 'border-[#c7ddeb] ring-1 ring-[#e9f3f8]' : 'border-[#dce4e9]' }}">
                <div class="flex min-w-0 items-start gap-3.5 sm:gap-4">
                    <!-- Icon Bulat -->
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $bgColor }} {{ $iconColor }} border text-sm shadow-sm mt-0.5">
                        <i class="fas {{ $icon }}"></i>
                    </div>

                    <!-- Informasi Pesan -->
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5">
                            <h4 class="text-[13px] font-bold text-[#293b49] {{ $isUnread ? 'font-extrabold' : '' }}">
                                {{ $data['title'] ?? 'Pemberitahuan Sistem' }}
                            </h4>

                            @if($isUnread)
                                <span class="inline-block h-2 w-2 rounded-full bg-[#2f789e] ring-2 ring-[#e7f1f7]" title="Belum dibaca"></span>
                            @endif

                            <!-- Target Label (Khusus Teknisi / OPD / Semua) -->
                            @if(!empty($data['target_label']))
                                <span class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-2.5 py-0.5 text-[10px] font-bold text-purple-700">
                                    {{ $data['target_label'] }}
                                </span>
                            @endif

                            <!-- Penerima (Jika Sedang Melihat Tab Log Global) -->
                            @if($filter === 'global' && $notif->notifiable)
                                <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-700">
                                    Penerima: {{ $notif->notifiable->name }}
                                </span>
                            @endif

                            <span class="text-[10px] font-medium text-[#8a98a2]">{{ $notif->created_at?->format('d M Y, H:i') }}</span>
                        </div>
                        <p class="mt-1 text-xs leading-relaxed text-[#667782] sm:text-[13px]">
                            {{ $data['message'] ?? '-' }}
                        </p>
                        @if(!empty($data['sender']))
                            <div class="mt-1.5 text-[10px] font-medium text-[#84919a]">
                                Disiarkan oleh: <span class="font-bold text-slate-600">{{ $data['sender'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex shrink-0 items-center gap-2 self-end sm:self-center">
                    @if($isUnread && $filter !== 'global')
                        <form action="{{ route('notifikasi.baca', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#d7e5ed] bg-white text-[#397b9d] shadow-sm transition hover:border-[#a9c9da] hover:bg-[#edf5f9] cursor-pointer"
                                    title="Tandai Sudah Dibaca" aria-label="Tandai Sudah Dibaca">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </form>
                    @endif

                    @if(!empty($data['url']) && $data['url'] !== route('notifikasi'))
                        <a href="{{ $data['url'] }}" 
                           class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-[#dce4e9] bg-white px-3 text-xs font-bold text-[#526775] shadow-sm transition hover:bg-[#f4f8fa]"
                           title="Buka Tautan Terkait">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    @endif

                    <form action="{{ route('notifikasi.destroy', $notif->id) }}" method="POST" onsubmit="return confirm('Hapus notifikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-white text-rose-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-700 cursor-pointer"
                                title="Hapus Notifikasi">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-[#dce4e9] bg-white px-6 py-14 text-center shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#edf3f6] text-xl text-[#8a9aa5]">
                    <i class="far fa-bell-slash"></i>
                </div>
                <h3 class="text-sm font-bold text-[#293b49]">Tidak ada notifikasi</h3>
                <p class="mx-auto mt-1 max-w-sm text-xs text-[#84919a]">
                    {{ $filter === 'unread' ? 'Semua notifikasi baru Anda telah selesai dibaca.' : 'Belum ada riwayat pemberitahuan tiket atau aktivitas masuk.' }}
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6 rounded-xl border border-[#dce4e9] bg-white px-4 py-3">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
</div>

<!-- ================= MODAL: SIARKAN PENGUMUMAN SISTEM ================= -->
<div id="modalBroadcast" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md overflow-hidden rounded-2xl border border-[#dce4e9] bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-[#edf1f4] px-5 py-4">
            <div>
                <h3 class="flex items-center gap-2 text-sm font-bold text-[#293b49]">
                    <i class="fas fa-bullhorn text-[#397b9d]"></i>
                    <span>Tambah Pengumuman</span>
                </h3>
                <p class="mt-0.5 text-[11px] text-[#84919a]">Kirim pesan pemberitahuan kepada pengguna SIGELATIK.</p>
            </div>
            <button type="button" onclick="tutupModal('modalBroadcast')" class="rounded-lg p-2 text-[#84919a] transition hover:bg-slate-100 hover:text-slate-700 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('notifikasi.broadcast') }}" method="POST" class="space-y-4 px-5 py-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Target Penerima <span class="text-rose-500">*</span>
                </label>
                <select name="target_role" 
                        class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-2.5 text-xs text-[#293b49] outline-none transition focus:border-[#397b9d] focus:ring-2 focus:ring-[#397b9d]/15">
                    <option value="all">Semua Pengguna Terdaftar</option>
                    <option value="teknisi">Khusus Teknisi Helpdesk</option>
                    <option value="asn">Khusus Pegawai ASN / OPD</option>
                </select>
                <p class="mt-1 text-[10px] text-[#84919a]">Salinan pengumuman akan otomatis tersimpan di riwayat Super Admin.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" required placeholder="Contoh: Pemeliharaan Server Sistem (Maintenance)"
                       class="w-full rounded-xl border border-[#dce4e9] bg-white px-3.5 py-2.5 text-xs text-[#293b49] outline-none transition focus:border-[#397b9d] focus:ring-2 focus:ring-[#397b9d]/15">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pesan Pemberitahuan <span class="text-rose-500">*</span>
                </label>
                <textarea name="message" rows="4" required placeholder="Tuliskan pesan yang akan disiarkan..."
                          class="w-full rounded-xl border border-[#dce4e9] bg-white p-3.5 text-xs text-[#293b49] outline-none transition focus:border-[#397b9d] focus:ring-2 focus:ring-[#397b9d]/15"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-[#edf1f4] pt-4">
                <button type="button" onclick="tutupModal('modalBroadcast')" class="rounded-xl border border-[#dce4e9] px-4 py-2.5 text-xs font-bold text-[#647782] transition hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#245f80] cursor-pointer">
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalBroadcast() {
        document.getElementById('modalBroadcast').classList.remove('hidden');
        document.getElementById('modalBroadcast').classList.add('flex');
    }

    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
</script>
@endsection