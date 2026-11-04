@extends('components.b-master')

@section('title', 'Manajemen Perangkat Daerah')
@section('subtitle', 'Daftar dan pengelolaan dinas, badan, dan biro Pemerintah Provinsi')

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
                Manajemen Perangkat Daerah (OPD)
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola master data dinas, badan, biro, dan unit instansi di lingkungan Pemprov Kalimantan Timur.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
            <i class="fas fa-building text-xs"></i>
            <span>Tambah Perangkat Daerah</span>
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
        <form method="GET" action="{{ route('perangkat') }}" class="flex items-center justify-between gap-3">
            <div class="relative w-full sm:w-96">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari nama dinas atau kode singkatan..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            @if(!empty($search))
                <a href="{{ route('perangkat') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl font-medium transition" title="Reset Filter">
                    <i class="fas fa-redo-alt text-xs"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Perangkat Daerah -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">PERANGKAT DAERAH (DINAS / OPD)</th>
                    <th class="px-6 py-4">KODE / SINGKATAN</th>
                    <th class="px-6 py-4 text-center">TOTAL BIDANG</th>
                    <th class="px-6 py-4 text-center">TOTAL PEGAWAI</th>
                    <th class="px-6 py-4 text-center w-36">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($perangkatList as $item)
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Nama Dinas -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center font-bold text-sm shrink-0 border border-blue-100">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 leading-snug">{{ $item->name }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">ID: #{{ $item->id }} • Dibuat {{ $item->created_at?->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Kode / Singkatan -->
                        <td class="px-6 py-4 align-middle font-mono font-bold text-slate-600">
                            @if($item->code)
                                <span class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px]">
                                    {{ $item->code }}
                                </span>
                            @else
                                <span class="text-slate-400 font-normal italic">-</span>
                            @endif
                        </td>

                        <!-- Total Bidang -->
                        <td class="px-6 py-4 align-middle text-center">
                            <a href="{{ route('bidang', ['perangkat_daerah_id' => $item->id]) }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                <i class="fas fa-sitemap text-[10px]"></i>
                                <span>{{ $item->bidang_count ?? 0 }} Bidang</span>
                            </a>
                        </td>

                        <!-- Total Pegawai ASN -->
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $item->employments_count ?? 0 }} Pegawai
                            </span>
                        </td>

                        <!-- Aksi Edit & Hapus -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="bukaModalEdit({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->code ?? '' }}')"
                                        class="p-2 border border-slate-200 text-slate-600 hover:text-[#1a3668] hover:border-slate-300 rounded-xl transition cursor-pointer"
                                        title="Ubah Data Perangkat Daerah">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <form action="{{ route('perangkat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus perangkat daerah {{ addslashes($item->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 border border-rose-100 bg-rose-50/50 text-rose-600 hover:bg-rose-100 rounded-xl transition cursor-pointer" 
                                            title="Hapus Perangkat Daerah">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-xs text-slate-400 italic">
                            Tidak ditemukan data perangkat daerah yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($perangkatList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $perangkatList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH PERANGKAT DAERAH ================= -->
<div id="modalTambahPerangkat" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-building text-blue-200"></i>
                    <span>Tambah Perangkat Daerah</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Daftarkan dinas, badan, atau biro instansi baru.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTambahPerangkat')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('perangkat.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Dinas / Badan / Biro <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" required placeholder="Contoh: Dinas Komunikasi dan Informatika"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode / Singkatan OPD (Opsional)
                </label>
                <input type="text" name="code" placeholder="Contoh: DISKOMINFO atau 1.02.01"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambahPerangkat')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Perangkat Daerah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH PERANGKAT DAERAH ================= -->
<div id="modalEditPerangkat" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-edit text-blue-200"></i>
                    <span>Ubah Perangkat Daerah</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Perbarui nama atau kode dinas terkait.</p>
            </div>
            <button type="button" onclick="tutupModal('modalEditPerangkat')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditPerangkat" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Dinas / Badan / Biro <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="editNamaPerangkat" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Kode / Singkatan OPD (Opsional)
                </label>
                <input type="text" name="code" id="editKodePerangkat"
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEditPerangkat')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
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
        document.getElementById('modalTambahPerangkat').classList.remove('hidden');
    }

    function bukaModalEdit(id, name, code) {
        document.getElementById('editNamaPerangkat').value = name;
        document.getElementById('editKodePerangkat').value = code;
        document.getElementById('formEditPerangkat').action = '/superadmin/perangkat/' + id;
        document.getElementById('modalEditPerangkat').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection