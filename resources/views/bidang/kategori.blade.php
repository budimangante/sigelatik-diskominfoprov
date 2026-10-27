@extends('components.b-master')

@section('title', 'Manajemen Kategori & Layanan')
@section('subtitle', 'Kelola daftar kategori permohonan pemohon dan sub-layanan bidang')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Manajemen Kategori & Layanan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola daftar kategori permohonan pemohon dan sub-layanan bidang.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    onclick="bukaModalTambahKategori()"
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:text-[#1a3668] hover:border-slate-300 rounded-xl text-xs font-bold shadow-2xs transition">
                + Tambah Kategori
            </button>
            <button type="button" 
                    onclick="bukaModalTambahLayanan()"
                    class="px-4 py-2 bg-[#1a3668] hover:bg-[#122548] text-white rounded-xl text-xs font-bold shadow-2xs transition">
                + Tambah Layanan
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
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-2xs">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Daftar Kategori & Sub-Layanan -->
    <div class="space-y-6">
        @forelse($categories as $cat)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <!-- Header Kategori -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
                    <div class="flex items-center gap-3">
                        <h2 class="text-base font-bold text-slate-800">{{ $cat->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ count($cat->services) }} Layanan
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-semibold">
                        <button type="button" 
                                onclick="bukaModalTambahLayanan({{ $cat->id }})"
                                class="text-slate-600 hover:text-[#1a3668] transition">
                            + Tambah Sub-Layanan
                        </button>
                    </div>
                </div>

                <!-- Tabel Sub-Layanan -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5 w-72">NAMA SUB-LAYANAN</th>
                                <th class="px-6 py-3.5">DESKRIPSI</th>
                                <th class="px-6 py-3.5 w-48">TARGET SLA</th>
                                <th class="px-6 py-3.5 w-28">STATUS</th>
                                <th class="px-6 py-3.5 w-24 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($cat->services as $svc)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800 align-middle">
                                        {{ $svc->name }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium align-middle whitespace-normal max-w-md">
                                        {{ $svc->description ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <span class="font-mono font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-[#1a3668] border border-blue-100">
                                            {{ $svc->sla_display }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-middle">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 align-middle text-center">
                                        <button type="button" 
                                                onclick="bukaModalEditLayanan({{ $svc->id }}, '{{ addslashes($svc->name) }}', {{ $svc->sla_minutes_val }}, '{{ $svc->work_hours ?? '08:00 - 15:00 WITA' }}')"
                                                class="text-blue-600 hover:text-blue-800 font-bold hover:underline transition">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400 italic">
                                        Belum ada sub-layanan yang terdaftar pada kategori ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center text-slate-400 text-xs italic shadow-2xs">
                Belum ada kategori layanan. Silakan klik tombol <b>+ Tambah Kategori</b> di sudut kanan atas.
            </div>
        @endforelse
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH KATEGORI ================= -->
<div id="modalTambahKategori" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-folder-plus text-blue-200"></i>
                <span>Tambah Kategori Baru</span>
            </h3>
            <button type="button" onclick="tutupModal('modalTambahKategori')" class="text-white/70 hover:text-white p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('bidang.kategori.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Kategori Induk
                </label>
                <input type="text" name="name" required placeholder="Contoh: Infrastruktur & Jaringan"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahKategori')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: TAMBAH SUB-LAYANAN ================= -->
<div id="modalTambahLayanan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-200"></i>
                <span>Tambah Sub-Layanan Baru</span>
            </h3>
            <button type="button" onclick="tutupModal('modalTambahLayanan')" class="text-white/70 hover:text-white p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('bidang.service.store') }}" method="POST" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pilih Kategori Induk
                </label>
                <select name="category_id" id="tambahLayananCategoryId" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Sub-Layanan
                </label>
                <input type="text" name="name" required placeholder="Contoh: Perbaikan Port LAN Switch"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi Singkat
                </label>
                <textarea name="description" rows="2.5" placeholder="Penjelasan singkat ruang lingkup penanganan permohonan ini..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Target SLA (Menit)
                    </label>
                    <div class="relative">
                        <input type="number" name="sla_minutes" min="5" step="5" value="60" required
                               class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">Menit</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Jam Operasional Kerja
                    </label>
                    <input type="text" name="work_hours" value="08:00 - 15:00 WITA" required
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahLayanan')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Simpan Layanan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: EDIT SLA & INFORMASI LAYANAN ================= -->
<div id="modalEditLayanan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-edit text-blue-200"></i>
                <span>Edit Target SLA Layanan</span>
            </h3>
            <button type="button" onclick="tutupModal('modalEditLayanan')" class="text-white/70 hover:text-white p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditLayanan" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PATCH')
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                    Nama Sub-Layanan
                </label>
                <div id="editServiceName" class="text-xs font-bold text-slate-800 p-3 bg-slate-50 border border-slate-200 rounded-xl"></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Target Durasi SLA (Menit)
                </label>
                <div class="relative">
                    <input type="number" name="sla_minutes" id="editServiceSla" min="5" step="5" required
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">Menit</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Durasi ini akan menjadi acuan hitung mundur otomatis saat triage.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jam Operasional Layanan
                </label>
                <input type="text" name="work_hours" id="editServiceWorkHours" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditLayanan')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                    Perbarui SLA
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Buka Modal Tambah Kategori
    function bukaModalTambahKategori() {
        document.getElementById('modalTambahKategori').classList.remove('hidden');
    }

    // Buka Modal Tambah Sub-Layanan
    function bukaModalTambahLayanan(catId = null) {
        if (catId) {
            document.getElementById('tambahLayananCategoryId').value = catId;
        }
        document.getElementById('modalTambahLayanan').classList.remove('hidden');
    }

    // Buka Modal Edit Layanan
    function bukaModalEditLayanan(serviceId, name, slaMinutes, workHours) {
        document.getElementById('editServiceName').innerText = name;
        document.getElementById('editServiceSla').value = slaMinutes;
        document.getElementById('editServiceWorkHours').value = workHours;
        document.getElementById('formEditLayanan').action = '/bidang/layanan/' + serviceId + '/sla';
        document.getElementById('modalEditLayanan').classList.remove('hidden');
    }

    // Tutup Modal Helper
    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection