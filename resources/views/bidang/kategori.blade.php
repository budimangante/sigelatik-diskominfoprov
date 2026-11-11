@extends('components.b-master')

@section('title', 'Manajemen Kategori & Layanan')
@section('subtitle', 'Kelola daftar kategori permohonan pemohon dan sub-layanan bidang')

@section('content')
<div class="min-h-screen bg-[#f4f7fa] px-4 py-5 font-sans sm:px-6 lg:px-7 space-y-5">

    <!-- Header halaman -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-[#397b9d]"></span>
                <span>HELPDESK BIDANG</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>LAYANAN APLIKASI</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Manajemen Kategori &amp; Layanan</h1>
            <p class="mt-1 text-xs text-slate-500">Kelola daftar kategori permohonan pemohon dan sub-layanan bidang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="bukaModalTambahKategori()"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#d7e2e9] bg-white px-4 py-2.5 text-xs font-bold text-[#36566e] shadow-sm transition hover:border-[#9fb8c8] hover:bg-[#f8fbfd] cursor-pointer">
                <i class="fas fa-folder-plus text-[11px]"></i>
                <span>Tambah Kategori</span>
            </button>
            <button type="button" onclick="bukaModalTambahLayanan()"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#327fa2] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#286b89] cursor-pointer">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Layanan</span>
            </button>
        </div>
    </div>

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
    <div class="space-y-4">
        @forelse($categories as $cat)
            <section class="overflow-hidden rounded-2xl border border-[#dce5ec] bg-white shadow-[0_3px_14px_rgba(24,54,82,0.045)]">
                <!-- Header Kategori -->
                <div class="flex flex-col gap-3 border-b border-slate-100 bg-white px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-[#dce8ee] bg-[#f1f7fa] text-[#397b9d]"><i class="fas fa-folder text-xs"></i></span>
                        <h2 class="text-sm font-bold text-[#243d50]">{{ $cat->name }}</h2>
                        <span class="rounded-full border border-[#e1e9ee] bg-[#f5f8fa] px-2.5 py-1 text-[10px] font-bold text-slate-600">
                            {{ count($cat->services) }} Layanan
                        </span>
                    </div>

                    <!-- Tombol Aksi Kategori: Tambah Sub-Layanan & Hapus Kategori -->
                    <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold">
                        <button type="button" 
                                onclick="bukaModalTambahLayanan({{ $cat->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-[#397b9d] transition hover:bg-[#f1f7fa] hover:text-[#1d405c] cursor-pointer">
                            + Tambah Sub-Layanan
                        </button>
                        <span class="text-slate-200">•</span>
                        <button type="button"
                                onclick="konfirmasiHapus('/bidang/kategori/{{ $cat->id }}', 'Kategori', '{{ addslashes($cat->name) }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-rose-500 transition hover:bg-rose-50 hover:text-rose-700 cursor-pointer"
                                title="Hapus Kategori Ini">
                            <i class="fas fa-trash-alt text-[11px]"></i>
                            <span>Hapus Kategori</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Sub-Layanan -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="border-b border-slate-100 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">
                            <tr>
                                <th class="px-5 py-3 w-72">NAMA SUB-LAYANAN</th>
                                <th class="px-5 py-3">DESKRIPSI</th>
                                <th class="px-5 py-3 w-48">TARGET SLA</th>
                                <th class="px-5 py-3 w-28">STATUS</th>
                                <th class="px-5 py-3 w-32 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($cat->services as $svc)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-3.5 font-bold text-slate-800 align-middle">
                                        {{ $svc->name }}
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500 font-medium align-middle whitespace-normal max-w-md">
                                        {{ $svc->description ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 align-middle">
                                        <span class="font-mono font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-[#1a3668] border border-blue-100">
                                            {{ $svc->sla_display }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 align-middle">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 align-middle text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <!-- Tombol Edit -->
                                            <button type="button" 
                                                    onclick="bukaModalEditLayanan({{ $svc->id }}, '{{ addslashes($svc->name) }}', {{ $svc->sla_minutes_val }}, '{{ $svc->work_hours ?? '08:00 - 15:00 WITA' }}')"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-[#397b9d] transition hover:border-[#9fb8c8] hover:bg-[#f1f7fa] cursor-pointer" title="Edit layanan">
                                                <i class="fas fa-pen text-[10px]"></i>
                                            </button>
                                            <span class="text-slate-200">|</span>
                                            <!-- Tombol Hapus Layanan -->
                                            <button type="button"
                                                    onclick="konfirmasiHapus('/bidang/layanan/{{ $svc->id }}', 'Sub-Layanan', '{{ addslashes($svc->name) }}')"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 text-rose-500 transition hover:bg-rose-50 cursor-pointer" title="Hapus layanan">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
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
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-[#d3e0e8] bg-white p-12 text-center text-xs italic text-slate-400 shadow-sm">
                Belum ada kategori layanan. Silakan klik tombol <b>+ Tambah Kategori</b> di sudut kanan atas.
            </div>
        @endforelse
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH KATEGORI ================= -->
<div id="modalTambahKategori" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 bg-white text-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-folder-plus text-[#397b9d]"></i>
                <span>Tambah Kategori Baru</span>
            </h3>
            <button type="button" onclick="tutupModal('modalTambahKategori')" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
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
                <button type="button" onclick="tutupModal('modalTambahKategori')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: TAMBAH SUB-LAYANAN (SLA DINAMIS MINIMAL 2) ================= -->
<div id="modalTambahLayanan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 bg-white text-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle text-[#397b9d]"></i>
                <span>Tambah Sub-Layanan Baru</span>
            </h3>
            <button type="button" onclick="tutupModal('modalTambahLayanan')" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('bidang.service.store') }}" method="POST" class="p-6 space-y-4 max-h-[78vh] overflow-y-auto">
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

            <!-- Bagian Target SLA Dinamis (Minimal 2 Input) -->
            <div class="space-y-3 p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Target SLA (Minimal 2 Input) <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400">SLA 1: Respon Triage, SLA 2+: Proses Teknisi</p>
                    </div>
                    <button type="button" 
                            onclick="tambahBarisSla()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1a3668] hover:bg-[#122548] text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer">
                        <i class="fas fa-plus text-[10px]"></i>
                        <span>Tambah SLA</span>
                    </button>
                </div>

                <div id="slaInputsContainer" class="space-y-2 pt-1">
                    <!-- Baris SLA 1 (Terkunci Wajib) -->
                    <div class="sla-item flex items-center gap-2">
                        <span class="sla-label w-24 text-xs font-bold text-blue-700 px-3 py-2 bg-blue-50 rounded-xl border border-blue-200 text-center shrink-0">
                            SLA 1
                        </span>
                        <div class="relative flex-1">
                            <input type="number" name="sla_minutes[]" value="60" min="5" required placeholder="Durasi SLA 1"
                                   class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-28 py-2 text-xs text-slate-800 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-medium pointer-events-none">Menit (Respon)</span>
                        </div>
                        <div class="w-8 shrink-0"></div>
                    </div>

                    <!-- Baris SLA 2 (Terkunci Wajib) -->
                    <div class="sla-item flex items-center gap-2">
                        <span class="sla-label w-24 text-xs font-bold text-blue-700 px-3 py-2 bg-blue-50 rounded-xl border border-blue-200 text-center shrink-0">
                            SLA 2
                        </span>
                        <div class="relative flex-1">
                            <input type="number" name="sla_minutes[]" value="240" min="5" required placeholder="Durasi SLA 2"
                                   class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-28 py-2 text-xs text-slate-800 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-medium pointer-events-none">Menit (Proses)</span>
                        </div>
                        <div class="w-8 shrink-0"></div>
                    </div>
                </div>
            </div>

            <!-- Jam Operasional Kerja -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jam Operasional Kerja
                </label>
                <input type="text" name="work_hours" value="08:00 - 15:00 WITA" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahLayanan')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Layanan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: EDIT SLA & INFORMASI LAYANAN ================= -->
<div id="modalEditLayanan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 bg-white text-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-edit text-[#397b9d]"></i>
                <span>Edit Target SLA Layanan</span>
            </h3>
            <button type="button" onclick="tutupModal('modalEditLayanan')" class="text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
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
                <p class="text-[11px] text-slate-400 mt-1">Durasi ini menjadi acuan SLA Proses pengerjaan oleh teknisi.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Jam Operasional Layanan
                </label>
                <input type="text" name="work_hours" id="editServiceWorkHours" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditLayanan')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Perbarui SLA
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 4: KONFIRMASI HAPUS UNIVERSAL ================= -->
<div id="modalKonfirmasiHapus" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 border border-rose-100 rounded-full flex items-center justify-center mx-auto mb-4 text-rose-500 text-xl">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-base font-extrabold text-slate-800 mb-1">Konfirmasi Penghapusan</h3>
            <p class="text-xs text-slate-500" id="pesanKonfirmasiHapus">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <form id="formHapusUniversal" method="POST" action="" class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="tutupModal('modalKonfirmasiHapus')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200/60 transition cursor-pointer">
                Batal
            </button>
            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                Ya, Hapus
            </button>
        </form>
    </div>
</div>

<script>
    function bukaModalTambahKategori() {
        document.getElementById('modalTambahKategori').classList.remove('hidden');
    }

    function bukaModalTambahLayanan(catId = null) {
        if (catId) {
            document.getElementById('tambahLayananCategoryId').value = catId;
        }
        document.getElementById('modalTambahLayanan').classList.remove('hidden');
    }

    function bukaModalEditLayanan(serviceId, name, slaMinutes, workHours) {
        document.getElementById('editServiceName').innerText = name;
        document.getElementById('editServiceSla').value = slaMinutes;
        document.getElementById('editServiceWorkHours').value = workHours;
        document.getElementById('formEditLayanan').action = '/bidang/layanan/' + serviceId + '/sla';
        document.getElementById('modalEditLayanan').classList.remove('hidden');
    }

    // Modal Konfirmasi Hapus Universal (Bisa untuk Kategori & Layanan)
    function konfirmasiHapus(actionUrl, jenis, nama) {
        document.getElementById('formHapusUniversal').action = actionUrl;
        document.getElementById('pesanKonfirmasiHapus').innerHTML = `Apakah Anda yakin ingin menghapus ${jenis} <strong>"${nama}"</strong>? Tindakan ini permanen.`;
        document.getElementById('modalKonfirmasiHapus').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Fungsi Tambah Baris SLA Dinamis
    function tambahBarisSla() {
        const container = document.getElementById('slaInputsContainer');
        const items = container.querySelectorAll('.sla-item');
        const nomor = items.length + 1;

        const div = document.createElement('div');
        div.className = 'sla-item flex items-center gap-2 animate-in fade-in duration-150';
        div.innerHTML = `
            <span class="sla-label w-24 text-xs font-bold text-emerald-700 px-3 py-2 bg-emerald-50 rounded-xl border border-emerald-200 text-center shrink-0">
                SLA ${nomor}
            </span>
            <div class="relative flex-1">
                <input type="number" name="sla_minutes[]" value="120" min="5" required placeholder="Durasi SLA ${nomor}"
                       class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-20 py-2 text-xs text-slate-800 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 font-medium pointer-events-none">Menit</span>
            </div>
            <button type="button" onclick="hapusBarisSla(this)"
                    class="w-8 h-8 rounded-lg text-rose-500 hover:text-white hover:bg-rose-500 flex items-center justify-center transition shrink-0 cursor-pointer"
                    title="Hapus baris SLA ini">
                <i class="fas fa-trash-alt text-xs"></i>
            </button>
        `;
        container.appendChild(div);
        updateLabelSla();
    }

    // Fungsi Hapus Baris SLA (Terkunci minimal 2 baris)
    function hapusBarisSla(button) {
        const container = document.getElementById('slaInputsContainer');
        const items = container.querySelectorAll('.sla-item');
        if (items.length <= 2) {
            alert('Minimal harus ada 2 tahapan SLA (SLA 1 dan SLA 2)!');
            return;
        }
        button.closest('.sla-item').remove();
        updateLabelSla();
    }

    // Re-index penomoran label SLA
    function updateLabelSla() {
        const container = document.getElementById('slaInputsContainer');
        const items = container.querySelectorAll('.sla-item');
        items.forEach((item, index) => {
            const num = index + 1;
            const label = item.querySelector('.sla-label');
            if (label) {
                label.textContent = `SLA ${num}`;
                if (num <= 2) {
                    label.className = 'sla-label w-24 text-xs font-bold text-blue-700 px-3 py-2 bg-blue-50 rounded-xl border border-blue-200 text-center shrink-0';
                } else {
                    label.className = 'sla-label w-24 text-xs font-bold text-emerald-700 px-3 py-2 bg-emerald-50 rounded-xl border border-emerald-200 text-center shrink-0';
                }
            }
            const spanDesc = item.querySelector('.relative span');
            if (spanDesc) {
                if (num === 1) {
                    spanDesc.textContent = 'Menit (Respon)';
                } else if (num === 2) {
                    spanDesc.textContent = 'Menit (Proses)';
                } else {
                    spanDesc.textContent = 'Menit';
                }
            }
        });
    }
</script>
@endsection