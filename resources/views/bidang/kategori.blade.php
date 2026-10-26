@extends('components.b-master')

@section('title', 'Katalog Layanan & SLA')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                <span class="text-[#1a3668]">Helpdesk Bidang</span>
                <span class="text-slate-300">•</span>
                <span>Master Katalog Layanan & SLA</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">Kategori & Standar SLA</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Kelola kategori layanan bidang, tentukan target durasi SLA default, dan susun rincian tahapan kerja SOP mengacu pada dokumen Helpdesk Utama.
            </p>
        </div>

        <!-- Tombol Aksi Utama -->
        <div class="flex items-center gap-2.5 shrink-0">
            <button type="button" onclick="bukaModalKategori()" 
                    class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#1a3668] border border-slate-200 text-xs font-bold px-4 py-2.5 rounded-xl shadow-xs transition hover:border-slate-300">
                <i class="fas fa-folder-plus text-xs"></i>
                <span>Tambah Kategori</span>
            </button>
            <button type="button" onclick="bukaModalLayanan()" 
                    class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Layanan Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <!-- 2. STATS RINGKASAN MASTER DATA -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1a3668] flex items-center justify-center text-lg font-bold">
                <i class="fas fa-folder-tree"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $categories->count() }}</span>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Total Kategori Bidang</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-bold">
                <i class="fas fa-cogs"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">
                    {{ $categories->sum(fn($c) => $c->services->count()) }}
                </span>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Sub-Layanan Aktif</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                <i class="fas fa-book-bookmark"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $sops->count() }}</span>
                <p class="text-xs font-semibold text-slate-400 mt-0.5">Dokumen Acuan SOP</p>
            </div>
        </div>
    </div>

    <!-- 3. PANEL ACUAN SOP DARI HELPDESK UTAMA -->
    <div class="mb-8 bg-gradient-to-br from-white to-slate-50/60 rounded-2xl border border-slate-200 shadow-xs p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#1a3668]/10 text-[#1a3668] flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-file-shield"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-[#1a3668]">Standar Acuan SOP (Helpdesk Utama)</h2>
                    <p class="text-xs text-slate-500">Gunakan dokumen ini sebagai acuan regulasi penentuan nama layanan dan target durasi SLA.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            @forelse($sops as $sop)
                <div class="border border-slate-200/80 bg-white rounded-xl p-4 hover:border-blue-300 hover:shadow-xs transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="font-bold text-xs text-slate-800 line-clamp-1">
                                {{ $sop->name ?? $sop->title ?? 'Dokumen Standar Pelayanan' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono bg-blue-50 text-blue-700 shrink-0">
                                {{ count($sop->steps ?? []) }} Langkah
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                            {{ $sop->description ?? 'Dokumen acuan penetapan alur kerja dan SLA resmi bidang.' }}
                        </p>
                    </div>

                    <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" 
                                onclick="lihatDetailSop('{{ addslashes($sop->name ?? $sop->title ?? 'SOP Layanan') }}', '{{ addslashes($sop->description ?? '') }}')"
                                class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1.5 transition">
                            <i class="far fa-file-lines text-xs"></i>
                            <span>Buka Panduan SOP</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-xs text-slate-400 bg-white/60 border border-dashed border-slate-200 rounded-xl">
                    <i class="far fa-folder-open text-2xl mb-1.5 block text-slate-300"></i>
                    Belum ada dokumen acuan SOP yang disubmit oleh Helpdesk Utama.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 4. DAFTAR KATALOG KATEGORI & LAYANAN BIDANG -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-[#1a3668]"></i>
                <span>Struktur Kategori & Layanan Bidang</span>
            </h2>
            <span class="text-xs text-slate-400">Total {{ $categories->count() }} Kategori Terdaftar</span>
        </div>

        @forelse($categories as $category)
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden transition">
                
                <!-- Category Banner Header -->
                <div class="px-6 py-4.5 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-[#1a3668] flex items-center justify-center font-bold text-xs shadow-2xs">
                            <i class="fas fa-folder"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">{{ $category->name }}</h3>
                            <p class="text-[11px] text-slate-400">Kategori Induk Layanan</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $category->services->count() }} Layanan
                        </span>
                        <button type="button" 
                                onclick="bukaModalLayananKhusus({{ $category->id }})" 
                                class="text-[11px] font-bold text-[#1a3668] hover:text-blue-700 bg-white border border-slate-200 px-3 py-1 rounded-lg hover:border-[#1a3668] transition">
                            <i class="fas fa-plus text-[10px]"></i> Tambah Sub-Layanan
                        </button>
                    </div>
                </div>

                <!-- Services List -->
                <div class="divide-y divide-slate-100">
                    @forelse($category->services as $service)
                        <div class="p-5 sm:p-6 hover:bg-slate-50/40 transition flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                            
                            <!-- Kolom Kiri: Info Layanan & Tahapan SOP -->
                            <div class="flex-1 space-y-4">
                                <div>
                                    <div class="flex items-center gap-2.5">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $service->name }}</h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            SVC-{{ str_pad($service->id, 3, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed max-w-3xl">
                                        {{ $service->description ?? 'Belum ada uraian teknis penanganan layanan ini.' }}
                                    </p>
                                </div>

                                <!-- Box Tahapan Aktivitas SOP (Checklist Teknisi) -->
                                <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-4 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fas fa-list-check text-[#1a3668]"></i>
                                            Daftar Tahapan SOP Penanganan (Checklist)
                                        </span>
                                        <button type="button" 
                                                onclick="bukaModalAktivitas({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            <span>Tambah Tahapan</span>
                                        </button>
                                    </div>

                                    <div class="space-y-1.5">
                                        @forelse($service->activities ?? [] as $index => $act)
                                            <div class="flex items-center justify-between text-xs py-2 px-3 rounded-lg bg-white border border-slate-200/60 shadow-2xs">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-4.5 h-4.5 rounded-full bg-slate-100 text-[10px] font-bold text-slate-600 flex items-center justify-center shrink-0">
                                                        {{ $index + 1 }}
                                                    </span>
                                                    <span class="text-slate-800 font-medium">{{ $act->name }}</span>
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                                                    Checklist Teknisi
                                                </span>
                                            </div>
                                        @empty
                                            <div class="text-[11px] text-slate-400 italic py-1">
                                                Belum ada rincian tahapan penanganan SOP. Klik 'Tambah Tahapan' untuk melengkapi lembar kerja teknisi.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Parameter Standar SLA & Aksi Atur -->
                            <div class="w-full lg:w-72 bg-white rounded-xl border border-slate-200 p-4.5 flex flex-col justify-between shrink-0 space-y-4 shadow-2xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2.5">
                                        Standar SLA Layanan
                                    </span>

                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-500 flex items-center gap-1.5">
                                                <i class="far fa-clock text-[#1a3668]"></i> Target Waktu:
                                            </span>
                                            <span class="font-bold font-mono text-[#1a3668] bg-blue-50/80 border border-blue-100 px-2 py-0.5 rounded text-xs">
                                                {{ $service->sla_display }}
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-500 flex items-center gap-1.5">
                                                <i class="far fa-calendar-alt text-[#1a3668]"></i> Jam Operasional:
                                            </span>
                                            <span class="font-semibold text-slate-700 text-xs">
                                                {{ $service->work_hours ?? '08:00 - 15:00 WITA' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" 
                                        onclick="bukaModalEditSla({{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->sla_minutes_val }}, '{{ $service->work_hours ?? '08:00 - 15:00 WITA' }}')"
                                        class="w-full py-2.5 bg-slate-50 hover:bg-[#1a3668] hover:text-white text-[#1a3668] border border-slate-200 hover:border-[#1a3668] rounded-xl text-xs font-bold transition flex items-center justify-center gap-2">
                                    <i class="fas fa-sliders-h text-xs"></i>
                                    <span>Sesuaikan SLA & Jam Kerja</span>
                                </button>
                            </div>

                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            Belum ada layanan yang didaftarkan di kategori ini.
                        </div>
                    @endforelse
                </div>

            </div>
        @empty
            <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold">Belum Ada Kategori Layanan</p>
                <p class="text-xs text-slate-400 mt-1">Silakan klik tombol 'Tambah Kategori' di bagian atas untuk memulai penyusunan layanan.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- ================= MODAL 1: BACA PANDUAN SOP ================= -->
<div id="modalDetailSop" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-book-open text-blue-200"></i>
                <h3 id="modalSopJudul" class="text-sm font-bold">Detail Dokumen SOP</h3>
            </div>
            <button type="button" onclick="tutupModal('modalDetailSop')" class="text-white/70 hover:text-white p-1">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    Uraian & Panduan Alur Resmi:
                </label>
                <div id="modalSopDeskripsi" class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 leading-relaxed max-h-60 overflow-y-auto whitespace-pre-line font-normal"></div>
            </div>
            <div class="text-[11px] text-slate-400 bg-blue-50/50 border border-blue-100 p-3 rounded-lg leading-relaxed">
                <span class="font-bold text-blue-700">Catatan:</span> Standar waktu dan tahapan di atas merupakan panduan baku yang ditetapkan oleh Helpdesk Utama.
            </div>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="tutupModal('modalDetailSop')" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 rounded-xl text-xs font-bold text-slate-700 transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL 2: TAMBAH KATEGORI ================= -->
<div id="modalKategori" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-folder-plus text-blue-200"></i>
                <span>Tambah Kategori Layanan</span>
            </h3>
            <button type="button" onclick="tutupModal('modalKategori')" class="text-white/70 hover:text-white">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('bidang.kategori.category.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kategori</label>
                <input type="text" name="name" placeholder="Contoh: Infrastruktur Jaringan & Server" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                <p class="text-[11px] text-slate-400 mt-1">Kelompokkan layanan bidang ke dalam kategori yang tepat.</p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalKategori')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 3: TAMBAH LAYANAN BARU ================= -->
<div id="modalLayanan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <h3 class="text-sm font-bold flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-200"></i>
                <span>Tambah Sub-Layanan & Target SLA</span>
            </h3>
            <button type="button" onclick="tutupModal('modalLayanan')" class="text-white/70 hover:text-white">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('bidang.kategori.service.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Kategori Induk</label>
                <select name="category_id" id="modalSelectCategory" required 
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <option value="" disabled selected>Pilih salah satu kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Layanan</label>
                <input type="text" name="name" placeholder="Contoh: Pembuatan Akun Email Dinas (@kaltimprov.go.id)" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi Layanan</label>
                <textarea name="description" rows="2" placeholder="Uraian penanganan kendala atau permohonan sesuai SOP..."
                          class="w-full bg-white border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Target SLA Standar (Menit)</label>
                    <input type="number" name="sla_minutes" min="5" step="5" value="120" required
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Operasional</label>
                    <input type="text" name="work_hours" value="08:00 - 15:00 WITA" required
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalLayanan')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 4: ATUR SLA & JAM KERJA ================= -->
<div id="modalEditSla" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold">Sesuaikan Target SLA Layanan</h3>
                <p id="modalSlaServiceName" class="text-xs text-blue-200 font-medium mt-0.5"></p>
            </div>
            <button type="button" onclick="tutupModal('modalEditSla')" class="text-white/70 hover:text-white">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="formEditSla" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Target SLA Standar (Menit)</label>
                <div class="relative">
                    <input type="number" name="sla_minutes" id="inputSlaMinutes" min="5" step="5" required
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">Menit</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Durasi ini akan menjadi SLA bawaan saat tiket permohonan baru masuk ke bidang.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Kerja Operasional</label>
                <input type="text" name="work_hours" id="inputWorkHours" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalEditSla')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 5: TAMBAH TAHAPAN KERJA SOP ================= -->
<div id="modalAktivitas" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold">Tambah Tahapan SOP Layanan</h3>
                <p id="modalActServiceName" class="text-xs text-blue-200 font-medium mt-0.5"></p>
            </div>
            <button type="button" onclick="tutupModal('modalAktivitas')" class="text-white/70 hover:text-white">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        <form id="formAktivitas" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Tahapan / Aktivitas</label>
                <input type="text" name="name" placeholder="Contoh: Verifikasi data pemohon / Uji ping port switch" required
                       class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                <p class="text-[11px] text-slate-400 mt-1">Aktivitas ini menjadi daftar periksa tahapan penanganan resmi untuk teknisi.</p>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalAktivitas')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition">Simpan Tahapan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalKategori() {
        document.getElementById('modalKategori').classList.remove('hidden');
    }

    function bukaModalLayanan() {
        document.getElementById('modalSelectCategory').value = "";
        document.getElementById('modalLayanan').classList.remove('hidden');
    }

    function bukaModalLayananKhusus(categoryId) {
        document.getElementById('modalSelectCategory').value = categoryId;
        document.getElementById('modalLayanan').classList.remove('hidden');
    }

    function lihatDetailSop(judul, deskripsi) {
        document.getElementById('modalSopJudul').innerText = judul;
        document.getElementById('modalSopDeskripsi').innerText = deskripsi || 'Tidak ada uraian detail pada dokumen SOP ini.';
        document.getElementById('modalDetailSop').classList.remove('hidden');
    }

    function bukaModalEditSla(serviceId, serviceName, currentMinutes, currentWorkHours) {
        document.getElementById('modalSlaServiceName').innerText = serviceName;
        document.getElementById('inputSlaMinutes').value = currentMinutes;
        document.getElementById('inputWorkHours').value = currentWorkHours;
        document.getElementById('formEditSla').action = '/bidang/kategori/services/' + serviceId + '/sla';
        document.getElementById('modalEditSla').classList.remove('hidden');
    }

    function bukaModalAktivitas(serviceId, serviceName) {
        document.getElementById('modalActServiceName').innerText = serviceName;
        document.getElementById('formAktivitas').action = '/bidang/kategori/services/' + serviceId + '/activity';
        document.getElementById('modalAktivitas').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection