@extends('components.b-master')

@section('title', 'Manajemen Bidang')
@section('subtitle', 'Daftar dan pengelolaan unit bidang pada Perangkat Daerah')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">SUPER ADMIN</span>
                <span class="text-slate-300">•</span>
                <span>STRUKTUR ORGANISASI</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Manajemen Bidang
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola divisi, bidang, atau sub-unit kerja di lingkungan Pemerintah Provinsi Kalimantan Timur.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Bidang</span>
        </button>
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

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs mb-6">
        <form method="GET" action="{{ route('bidang') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari nama bidang atau kode..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="perangkat_daerah_id" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-64 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                    <option value="">Semua Perangkat Daerah (OPD)</option>
                    @foreach($perangkatDaerahList as $opd)
                        <option value="{{ $opd->id }}" {{ ($opdFilter ?? '') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->name }}
                        </option>
                    @endforeach
                </select>

                @if(!empty($search) || !empty($opdFilter))
                    <a href="{{ route('bidang') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl font-medium transition" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Bidang -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">NAMA BIDANG / DIVISI</th>
                    <th class="px-6 py-4">KODE</th>
                    <th class="px-6 py-4">PERANGKAT DAERAH INDUK</th>
                    <th class="px-6 py-4 text-center">TOTAL PEGAWAI</th>
                    <th class="px-6 py-4 text-center w-36">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($bidangList as $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Nama Bidang -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1a3668] flex items-center justify-center font-bold text-xs shrink-0 border border-blue-100">
                                    <i class="fas fa-sitemap"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 leading-snug">{{ $item->name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Dibuat {{ $item->created_at?->diffForHumans() }}</div>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $item->employments_count ?? 0 }} Pegawai
                            </span>
                        </td>

                        <!-- Aksi Edit & Hapus -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="bukaModalEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->code ?? '' }}', '{{ $item->perangkat_daerah_id }}')"
                                        class="p-2 border border-slate-200 text-slate-600 hover:text-[#1a3668] hover:border-slate-300 rounded-xl transition cursor-pointer"
                                        title="Ubah Data Bidang">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <form action="{{ route('bidang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bidang {{ addslashes($item->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 border border-rose-100 bg-rose-50/50 text-rose-600 hover:bg-rose-100 rounded-xl transition cursor-pointer" 
                                            title="Hapus Bidang">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-xs text-slate-400 italic">
                            Tidak ditemukan data bidang yang sesuai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($bidangList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $bidangList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH BIDANG ================= -->
<div id="modalTambahBidang" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
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

        <form action="{{ route('bidang.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Perangkat Daerah Induk <span class="text-rose-500">*</span>
                </label>
                <select name="perangkat_daerah_id" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
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
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode Bidang (Opsional)
                </label>
                <input type="text" name="code" placeholder="Contoh: 1 atau APTIKA"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahBidang')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Bidang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH BIDANG ================= -->
<div id="modalEditBidang" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
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

        <form id="formEditBidang" method="POST" action="" class="p-6 space-y-4">
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
                <button type="button" onclick="tutupModal('modalEditBidang')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('modalTambahBidang').classList.remove('hidden');
    }

    function bukaModalEdit(id, name, code, opdId) {
        document.getElementById('editNamaBidang').value = name;
        document.getElementById('editKodeBidang').value = code;
        document.getElementById('editOpdId').value = opdId;
        document.getElementById('formEditBidang').action = '/superadmin/bidang/' + id;
        document.getElementById('modalEditBidang').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection