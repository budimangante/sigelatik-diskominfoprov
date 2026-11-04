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

<!-- ================= MODAL 2: TAMBAH SUB-LAYANAN (SLA DINAMIS MINIMAL 2) ================= -->
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