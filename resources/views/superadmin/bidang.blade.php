@extends('components.b-master')

@section('title', 'Manajemen Bidang')
@section('subtitle', 'Daftar dan pengelolaan unit bidang pada Perangkat Daerah')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] p-4 font-sans sm:p-6 lg:p-7">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                    <span>SUPER ADMIN</span>
                    <span class="text-[#b8c7d0]">•</span>
                    <span>STRUKTUR ORGANISASI</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Manajemen Bidang
            </h1>
            <p class="mt-1 text-xs text-[#71808c] sm:text-sm">
                Kelola divisi, bidang, atau sub-unit kerja di lingkungan Pemerintah Provinsi Kalimantan Timur.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#2f789e] px-4 py-3 text-xs font-bold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-[#256887] hover:shadow-md cursor-pointer">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Bidang</span>
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-5 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 space-y-1 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800 shadow-sm">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="mb-3 rounded-2xl border border-[#dce4e9] bg-white p-3 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-3.5">
        <form method="GET" action="{{ route('bidang') }}" class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-[420px]">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari nama bidang atau kode..."
                       class="w-full rounded-xl border border-[#d6e0e7] bg-[#f7f9fb] py-3 pl-10 pr-3.5 text-xs text-slate-800 placeholder:text-[#91a0aa] transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <div class="flex w-full min-w-0 items-center gap-2 sm:w-auto sm:min-w-[310px]">
                <select name="perangkat_daerah_id" 
                        onchange="this.form.submit()" 
                        class="w-full min-w-0 rounded-xl border border-[#d6e0e7] bg-[#f7f9fb] px-3.5 py-3 text-xs text-slate-700 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10 sm:min-w-[310px]">
                    <option value="">Semua Perangkat Daerah (OPD)</option>
                    @foreach($perangkatDaerahList as $opd)
                        <option value="{{ $opd->id }}" {{ ($opdFilter ?? '') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->name }}
                        </option>
                    @endforeach
                </select>

                @if(!empty($search) || !empty($opdFilter))
                    <a href="{{ route('bidang') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-slate-100 px-3.5 py-3 text-xs font-semibold text-slate-600 transition hover:bg-slate-200" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Bidang -->
    <div class="overflow-hidden rounded-2xl border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]">
                    <i class="fas fa-sitemap text-sm"></i>
                </span>
                <div>
                    <p class="text-sm font-bold text-[#293b49]">{{ $bidangList->count() }} dari {{ $bidangList->total() }} bidang</p>
                    <p class="mt-0.5 text-[11px] text-[#84919a]">Daftar bidang yang terdaftar pada Perangkat Daerah</p>
                </div>
            </div>
            @if(!empty($search) || !empty($opdFilter))
                <span class="inline-flex w-fit max-w-full items-center truncate rounded-full bg-[#edf4f8] px-3 py-1 text-[11px] font-semibold text-[#397b9d]">
                    {{ !empty($search) ? 'Pencarian: '.$search : 'Filter OPD aktif' }}
                </span>
            @endif
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left whitespace-nowrap">
            <thead class="border-b border-[#e5ebef] bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.10em] text-[#82919c]">
                <tr>
                    <th class="px-6 py-4">NAMA BIDANG / DIVISI</th>
                    <th class="px-6 py-4">KODE</th>
                    <th class="px-6 py-4">PERANGKAT DAERAH INDUK</th>
                    <th class="px-6 py-4 text-center">TOTAL PEGAWAI</th>
                    <th class="px-6 py-4 text-center w-36">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[#edf1f4] text-xs">
                @forelse($bidangList as $item)
                    <tr class="transition-colors duration-150 hover:bg-[#f7fafc]">
                        <!-- Nama Bidang -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#e2edf3] bg-[#edf4f8] text-sm font-bold text-[#397b9d]">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <div>
                                    <div class="font-bold leading-snug text-[#293b49]">{{ $item->name }}</div>
                                    <div class="mt-1 text-[10px] text-[#84919a]">Dibuat {{ $item->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Kode Bidang -->
                        <td class="px-6 py-4 align-middle font-mono font-semibold text-slate-600">
                            {{ $item->code ?? '-' }}
                        </td>

                        <!-- Perangkat Daerah -->
                        <td class="px-6 py-4 align-middle text-slate-700 font-medium">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-building text-slate-400 text-xs"></i>
                                <span>{{ $item->perangkatDaerah?->name ?? 'Belum ditentukan' }}</span>
                            </span>
                        </td>

                        <!-- Total Pegawai ASN Terdaftar -->
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center rounded-full border border-[#d9e0e5] bg-[#f2f5f7] px-3 py-1 text-[10px] font-bold text-[#617482]">
                                {{ $item->employments_count ?? 0 }} Pegawai
                            </span>
                        </td>

                        <!-- Aksi Edit & Hapus -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="bukaModalEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->code ?? '' }}', '{{ $item->perangkat_daerah_id }}')"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#cbdde8] bg-white text-[#397b9d] shadow-sm transition hover:border-[#397b9d] hover:bg-[#edf6fa] cursor-pointer"
                                        title="Ubah Data Bidang">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <form action="{{ route('bidang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang {{ addslashes($item->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-white text-rose-500 shadow-sm transition hover:border-rose-200 hover:bg-rose-50 cursor-pointer" 
                                            title="Hapus Bidang">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-xs italic text-[#84919a]">
                            Tidak ditemukan data bidang yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <!-- Pagination -->
        @if($bidangList->hasPages())
            <div class="border-t border-slate-100 bg-[#fbfcfd] px-5 py-4">
                {{ $bidangList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH BIDANG ================= -->
<div id="modalTambahBidang" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 p-4 backdrop-blur-[3px]">
    <div class="flex w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-white/70 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between bg-[#1d405c] p-5 text-white">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-sitemap text-blue-200"></i>
                    <span>Tambah Bidang Baru</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Daftarkan divisi kerja baru pada Perangkat Daerah terkait.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTambahBidang')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('bidang.store') }}" method="POST" class="space-y-4 p-6">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Perangkat Daerah Induk <span class="text-rose-500">*</span>
                </label>
                <select name="perangkat_daerah_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
                    <option value="" disabled selected>Pilih Perangkat Daerah...</option>
                    @foreach($perangkatDaerahList as $opd)
                        <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Bidang / Divisi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Contoh: Aplikasi Informatika (APTIKA)"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode Bidang (Opsional)
                </label>
                <input type="text" name="code" placeholder="Contoh: 1 atau APTIKA"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahBidang')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#256887] cursor-pointer">
                    Simpan Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH BIDANG ================= -->
<div id="modalEditBidang" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/45 p-4 backdrop-blur-[3px]">
    <div class="flex w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-white/70 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between bg-[#1d405c] p-5 text-white">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-edit text-blue-200"></i>
                    <span>Ubah Data Bidang</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Perbarui informasi divisi kerja unit.</p>
            </div>
            <button type="button" onclick="tutupModal('modalEditBidang')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditBidang" method="POST" action="" class="space-y-4 p-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Perangkat Daerah Induk <span class="text-rose-500">*</span>
                </label>
                <select name="perangkat_daerah_id" id="editOpdId" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="" disabled>Pilih Perangkat Daerah...</option>
                    @foreach($perangkatDaerahList as $opd)
                        <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Bidang / Divisi <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="editNamaBidang" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode Bidang (Opsional)
                </label>
                <input type="text" name="code" id="editKodeBidang"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditBidang')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#256887] cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalTambahBidang').classList.remove('hidden');
        document.getElementById('modalTambahBidang').classList.add('flex');
    }

    function bukaModalEdit(id, name, code, opdId) {
        document.getElementById('editNamaBidang').value = name;
        document.getElementById('editKodeBidang').value = code;
        document.getElementById('editOpdId').value = opdId;
        document.getElementById('formEditBidang').action = '/superadmin/bidang/' + id;
        document.getElementById('modalEditBidang').classList.remove('hidden');
        document.getElementById('modalEditBidang').classList.add('flex');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById(modalId).classList.remove('flex');
    }
</script>
@endsection