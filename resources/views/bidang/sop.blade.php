@extends('components.b-master')

@section('title', 'Standar Operasional Prosedur (SOP)')
@section('subtitle', 'Pustaka regulasi dan checklist tahapan kerja teknisi SIGELATIK')

@section('content')
<div class="min-h-screen bg-[#f4f7fa] px-4 py-5 font-sans sm:px-6 lg:px-7 space-y-5">

    <!-- Header halaman -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-[#397b9d]"></span>
                <span>HELPDESK BIDANG</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>PUSTAKA PROSEDUR</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Manajemen SOP</h1>
            <p class="mt-1 text-xs text-slate-500">Daftar dokumen regulasi penanganan aduan dan rincian tahapan kerja baku untuk teknisi.</p>
        </div>
        <button type="button" onclick="bukaModalSop()"
                class="inline-flex items-center gap-2 self-start rounded-xl bg-[#327fa2] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#286b89] sm:self-auto">
            <i class="fas fa-plus text-[10px]"></i>
            <span>Tambah Dokumen SOP</span>
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800">
            <div class="flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600"></i><span>{{ session('success') }}</span></div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="space-y-1 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-800">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2"><i class="fas fa-exclamation-circle text-rose-600"></i><span>{{ $error }}</span></div>
            @endforeach
        </div>
    @endif

    <!-- Kartu dokumen SOP -->
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($sops as $sop)
            @php
                $pdfPath = $sop->file?->path ?? $sop->file_path ?? null;
                $serviceName = $sop->service?->name ?? $sop->service_name ?? null;
                $stepCount = count($sop->steps ?? []);
            @endphp
            <article class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-[#dce5ec] bg-white shadow-[0_3px_14px_rgba(24,54,82,0.045)] transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start gap-3 px-4 pt-4 sm:px-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#d9e8ef] bg-[#eff6f9] text-[#397b9d]">
                        <i class="fas fa-file-contract text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <h3 class="text-sm font-extrabold leading-snug text-[#243d50]">{{ $sop->title ?? $sop->name }}</h3>
                            @if(!empty($serviceName))
                                <span class="rounded-full border border-[#dceaf1] bg-[#f1f7fa] px-2 py-0.5 text-[10px] font-bold text-[#397b9d]">{{ $serviceName }}</span>
                            @endif
                            @if(!empty($sop->doc_number))
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[10px] font-medium text-slate-600">{{ $sop->doc_number }}</span>
                            @endif
                            @if(isset($sop->version))
                                <span class="rounded-full border border-indigo-100 bg-indigo-50 px-2 py-0.5 font-mono text-[10px] font-bold text-indigo-700">v{{ $sop->version }}</span>
                            @endif
                        </div>
                        @if(!empty($sop->description))
                            <p class="mt-2 line-clamp-3 text-xs leading-relaxed text-slate-500">{{ $sop->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-4 py-3 sm:px-5">
                    <div class="flex min-w-0 items-center gap-2 text-[10px] text-slate-400">
                        @if($pdfPath)
                            <a href="{{ route('bidang.sop.view', $sop->slug ?? $sop->id) }}" target="_blank" class="inline-flex min-w-0 items-center gap-1.5 font-semibold text-[#397b9d] transition hover:text-[#1d405c]">
                                <i class="fas fa-file-pdf text-rose-500"></i><span>Lihat dokumen PDF</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5"><i class="far fa-file text-slate-400"></i>Dokumen belum tersedia</span>
                        @endif
                        <span class="text-slate-300">•</span>
                        <span>{{ $stepCount }} tahapan</span>
                    </div>
                    <div class="flex shrink-0 items-center gap-1.5">
                        <button type="button" onclick="bukaModalTahapan({{ $sop->id }}, '{{ addslashes($sop->title ?? $sop->name) }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-[#dce5ec] bg-white px-2.5 py-1.5 text-[10px] font-bold text-[#397b9d] transition hover:bg-[#f1f7fa]">
                            <i class="fas fa-plus text-[9px]"></i><span>Tambah Tahapan</span>
                        </button>
                        <button type="button" onclick="editSop({{ $sop->id }}, {{ $sop->service_id ?? 'null' }}, '{{ addslashes($sop->title ?? $sop->name) }}', '{{ addslashes($sop->doc_number ?? '') }}', '{{ addslashes($sop->description ?? '') }}')"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-[#a9c2d0] hover:bg-[#f1f7fa] hover:text-[#397b9d]" title="Edit SOP">
                            <i class="fas fa-pen text-[10px]"></i>
                        </button>
                    </div>
                </div>

                @if($stepCount > 0)
                    <details class="border-t border-slate-100 px-4 py-3 sm:px-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-[11px] font-bold text-slate-500 marker:hidden">
                            <span class="inline-flex items-center gap-2"><i class="fas fa-list-check text-[#397b9d]"></i> Tahapan pengerjaan lapangan</span>
                            <span class="text-[10px] font-semibold text-[#397b9d]">Lihat tahapan <i class="fas fa-chevron-down ml-1 text-[9px]"></i></span>
                        </summary>
                        <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                            @foreach($sop->steps ?? [] as $index => $step)
                                <div class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-[#f8fafc] p-3 text-xs">
                                    <div class="flex min-w-0 items-start gap-2.5">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#e6f0f5] text-[10px] font-bold text-[#397b9d]">{{ $step->step_no ?? ($index + 1) }}</span>
                                        <span class="break-words font-medium leading-relaxed text-slate-700">{{ $step->description ?? $step->name }}</span>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <button type="button" onclick="bukaModalEditTahapan({{ $step->id }}, '{{ addslashes($step->description ?? $step->name) }}', {{ $step->step_no ?? ($index + 1) }})" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white hover:text-[#397b9d]" title="Edit Tahapan"><i class="fas fa-pen text-[10px]"></i></button>
                                        <form action="{{ route('bidang.sop.step.destroy', $step->id) }}" method="POST" onsubmit="return confirm('Hapus tahapan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus Tahapan"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @endif
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-[#d3e0e8] bg-white p-12 text-center text-xs text-slate-400">
                <i class="fas fa-book-open mb-3 block text-3xl text-slate-300"></i>
                <span class="font-semibold text-slate-600">Belum ada dokumen SOP.</span>
                <p class="mt-1">Silakan klik tombol "Tambah Dokumen SOP" untuk mendaftarkan dokumen acuan baru.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal 1: Tambah / Edit Dokumen SOP -->
<div id="modalSop" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/45 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 id="judulModalSop" class="font-bold text-sm text-[#243d50]">Tambah Dokumen SOP</h3>
            <button type="button" onclick="tutupModal('modalSop')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formSop" method="POST" action="{{ route('bidang.sop.store') }}" enctype="multipart/form-data" class="p-5 space-y-3.5">
            @csrf
            
            <!-- Pilihan Layanan Terkait -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Layanan Terkait</label>
                <select name="service_id" id="inputSopService" required
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#1a3668]">
                    <option value="" disabled selected>Pilih Layanan...</option>
                    @foreach($services as $svc)
                        <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nama / Judul SOP -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama / Judul SOP</label>
                <input type="text" name="name" id="inputSopName" placeholder="Contoh: SOP Penanganan Insiden Jaringan OPD" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#1a3668]">
            </div>

            <!-- Nomor Dokumen -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Dokumen / SK (Opsional)</label>
                <input type="text" name="doc_number" id="inputSopNumber" placeholder="Contoh: SOP/KOMINFO/2026/01"
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#1a3668]">
            </div>

            <!-- Upload File PDF -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1" id="labelSopFile">Upload Dokumen SOP (Khusus PDF)</label>
                <input type="file" name="file" id="inputSopFile" accept=".pdf,application/pdf" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1a3668] hover:file:bg-blue-100 focus:outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Maksimal ukuran file 10 MB, hanya ekstensi .pdf</p>
            </div>

            <!-- Deskripsi Singkat -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ruang Lingkup / Ringkasan Regulasi</label>
                <textarea name="description" id="inputSopDesc" rows="3" placeholder="Uraikan dasar kebijakan dan ruang lingkup pelayanan..."
                          class="w-full border border-slate-200 rounded-lg p-2.5 text-xs focus:outline-none focus:border-[#1a3668] resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalSop')" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="bg-[#1a3668] text-white text-xs font-semibold px-4 py-1.5 rounded-lg hover:bg-[#122548]">Simpan Dokumen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Tambah Tahapan Checklist SOP -->
<div id="modalTahapan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/45 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-[#243d50]">Tambah Langkah SOP</h3>
                <p id="subJudulTahapanSop" class="text-[11px] text-slate-500 truncate max-w-xs"></p>
            </div>
            <button type="button" onclick="tutupModal('modalTahapan')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formTahapan" method="POST" action="" class="p-5 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tahapan / Checklist Pekerjaan</label>
                <input type="text" name="name" placeholder="Contoh: Pengecekan konektor RJ45 dan switch port" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#1a3668]">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalTahapan')" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="bg-[#1a3668] text-white text-xs font-semibold px-4 py-1.5 rounded-lg hover:bg-[#122548]">Simpan Langkah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Edit Tahapan Checklist SOP -->
<div id="modalEditTahapan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/45 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-[#243d50]">Edit Langkah SOP</h3>
                <p id="labelNomorTahapan" class="text-[11px] text-slate-500"></p>
            </div>
            <button type="button" onclick="tutupModal('modalEditTahapan')" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form id="formEditTahapan" method="POST" action="" class="p-5 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Tahapan</label>
                <input type="text" name="name" id="inputEditTahapanName" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-[#1a3668]">
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="tutupModal('modalEditTahapan')" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                <button type="submit" class="bg-[#1a3668] text-white text-xs font-semibold px-4 py-1.5 rounded-lg hover:bg-[#122548]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalSop() {
        document.getElementById('judulModalSop').innerText = 'Tambah Dokumen SOP';
        document.getElementById('inputSopService').value = '';
        document.getElementById('inputSopName').value = '';
        document.getElementById('inputSopNumber').value = '';
        document.getElementById('inputSopDesc').value = '';
        document.getElementById('inputSopFile').value = '';
        document.getElementById('inputSopFile').required = true;
        document.getElementById('labelSopFile').innerText = 'Upload Dokumen SOP (Khusus PDF)';
        document.getElementById('formSop').action = "{{ route('bidang.sop.store') }}";
        document.getElementById('modalSop').classList.remove('hidden');
    }

    function editSop(id, serviceId, name, docNumber, desc) {
        document.getElementById('judulModalSop').innerText = 'Edit Dokumen SOP';
        if (serviceId) {
            document.getElementById('inputSopService').value = serviceId;
        }
        document.getElementById('inputSopName').value = name;
        document.getElementById('inputSopNumber').value = docNumber || '';
        document.getElementById('inputSopDesc').value = desc || '';
        document.getElementById('inputSopFile').value = '';
        document.getElementById('inputSopFile').required = false;
        document.getElementById('labelSopFile').innerText = 'Ganti Berkas PDF (Kosongkan jika tidak diganti)';
        document.getElementById('formSop').action = '/bidang/sop/' + id + '/update';
        document.getElementById('modalSop').classList.remove('hidden');
    }

    function bukaModalTahapan(sopId, sopTitle) {
        document.getElementById('subJudulTahapanSop').innerText = sopTitle;
        document.getElementById('formTahapan').action = '/bidang/sop/' + sopId + '/step';
        document.getElementById('modalTahapan').classList.remove('hidden');
    }

    function bukaModalEditTahapan(stepId, description, stepNo) {
        document.getElementById('labelNomorTahapan').innerText = 'Langkah ke-' + stepNo;
        document.getElementById('inputEditTahapanName').value = description;
        document.getElementById('formEditTahapan').action = '/bidang/sop/step/' + stepId + '/update';
        document.getElementById('modalEditTahapan').classList.remove('hidden');
    }

    function tutupModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection