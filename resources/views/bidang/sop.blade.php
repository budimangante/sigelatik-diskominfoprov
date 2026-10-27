@extends('components.b-master')

@section('title', 'Standar Operasional Prosedur (SOP)')
@section('subtitle', 'Pustaka regulasi dan checklist tahapan kerja teknisi SIGELATIK')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manajemen Standar Operasional Prosedur (SOP)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar dokumen regulasi penanganan aduan dan rincian tahapan kerja baku untuk teknisi.</p>
        </div>
        <div>
            <button type="button" onclick="bukaModalSop()" 
                    class="bg-[#1a3668] hover:bg-[#122548] text-white text-xs font-semibold px-4 py-2 rounded-lg transition shadow-xs flex items-center gap-1.5">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Dokumen SOP</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-5 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Daftar Dokumen SOP & Rincian Tahapan -->
    <div class="space-y-4">
        @forelse($sops as $sop)
            @php
                $pdfPath = $sop->file?->path ?? $sop->file_path ?? null;
                $serviceName = $sop->service?->name ?? $sop->service_name ?? null;
            @endphp
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-2xs">
                
                <!-- Header Dokumen SOP -->
                <div class="px-5 py-4 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-[#1a3668] flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-sm text-slate-900">{{ $sop->title ?? $sop->name }}</h3>
                                
                                @if(!empty($serviceName))
                                    <span class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded">
                                        {{ $serviceName }}
                                    </span>
                                @endif

                                @if(!empty($sop->doc_number))
                                    <span class="text-[10px] font-mono bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-medium">
                                        {{ $sop->doc_number }}
                                    </span>
                                @endif

                                @if(isset($sop->version))
                                    <span class="text-[10px] font-mono bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded font-bold">
                                        v{{ $sop->version }}
                                    </span>
                                @endif
                            </div>
                            
                            @if(!empty($sop->description))
                                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                                    {{ $sop->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                    @if($pdfPath)
                        <a href="{{ route('bidang.sop.view', $sop->slug ?? $sop->id) }}" target="_blank"
                        class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <i class="fas fa-file-pdf"></i>
                            <span>Lihat PDF</span>
                        </a>
                    @endif

                        <button type="button" 
                                onclick="bukaModalTahapan({{ $sop->id }}, '{{ addslashes($sop->title ?? $sop->name) }}')"
                                class="text-xs font-semibold text-[#1a3668] hover:text-blue-800 bg-white border border-slate-200 px-3 py-1.5 rounded-lg hover:border-[#1a3668] transition">
                            + Tambah Tahapan
                        </button>
                        <button type="button" 
                                onclick="editSop({{ $sop->id }}, {{ $sop->service_id ?? 'null' }}, '{{ addslashes($sop->title ?? $sop->name) }}', '{{ addslashes($sop->doc_number ?? '') }}', '{{ addslashes($sop->description ?? '') }}')"
                                class="text-xs text-slate-600 hover:text-slate-800 px-2 py-1.5">
                            Edit
                        </button>
                    </div>
                </div>

                <!-- Daftar Tahapan Kerja SOP -->
                <div class="p-5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center justify-between">
                        <span>Tahapan Pengerjaan Lapangan:</span>
                        <span class="font-normal normal-case text-slate-500">
                            {{ count($sop->steps ?? []) }} langkah pengerjaan
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        @forelse($sop->steps ?? [] as $index => $step)
                            <div class="p-3 bg-slate-50/60 border border-slate-200/80 rounded-lg flex items-start justify-between text-xs gap-3 group hover:bg-slate-100/70 transition">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $step->step_no ?? ($index + 1) }}
                                    </span>
                                    <span class="text-slate-800 font-medium leading-relaxed break-words">
                                        {{ $step->description ?? $step->name }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button" 
                                            onclick="bukaModalEditTahapan({{ $step->id }}, '{{ addslashes($step->description ?? $step->name) }}', {{ $step->step_no ?? ($index + 1) }})" 
                                            class="p-1 text-slate-400 hover:text-blue-600 transition" 
                                            title="Edit Tahapan">
                                        <i class="fas fa-pen text-[11px]"></i>
                                    </button>
                                    <form action="{{ route('bidang.sop.step.destroy', $step->id) }}" method="POST" onsubmit="return confirm('Hapus tahapan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Hapus Tahapan">
                                            <i class="fas fa-trash-alt text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-3 text-center text-xs text-slate-400 italic bg-slate-50/30 rounded-lg border border-dashed border-slate-200">
                                Belum ada tahapan kerja yang didaftarkan. Klik tombol '+ Tambah Tahapan' untuk menambahkan langkah pengerjaan teknisi.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        @empty
            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center text-slate-400 text-xs">
                <i class="fas fa-book-open text-3xl mb-2 text-slate-300 block"></i>
                <span class="font-semibold text-slate-600">Belum ada dokumen SOP.</span>
                <p class="mt-1">Silakan klik tombol "Tambah Dokumen SOP" untuk mendaftarkan dokumen acuan baru.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- Modal 1: Tambah / Edit Dokumen SOP -->
<div id="modalSop" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 hidden p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <h3 id="judulModalSop" class="font-bold text-sm text-slate-800">Tambah Dokumen SOP</h3>
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
<div id="modalTahapan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 hidden p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-slate-800">Tambah Langkah SOP</h3>
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
<div id="modalEditTahapan" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 hidden p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-sm text-slate-800">Edit Langkah SOP</h3>
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