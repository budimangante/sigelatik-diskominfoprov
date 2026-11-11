@extends('components.b-master')

@section('title', 'Buat Permohonan Layanan')
@section('subtitle', 'Pilih dinas dan kategori untuk mengajukan permohonan ke Diskominfo')

@section('content')
<div class="min-h-screen bg-[#f3f6f9] p-5 font-sans md:p-8">
    
    <!-- Header Section -->
    <div class="mb-6">
        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.16em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>USER</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>TIKET BARU</span>
            </div>
        <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">Buat permohonan</h1>
        <p class="text-xs text-slate-500 md:text-sm">Pilih dinas dan kategori layanan secara berurutan, lalu jelaskan kebutuhan Anda.</p>
    </div>

    <!-- Layout Utama: Form Kiri & Alur Kanan -->
    <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_288px]">
        
        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR INPUT -->
        <!-- ========================================== -->
        <div class="w-full min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-7">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-[#347fa5]"><i class="fas fa-clipboard-list"></i></span><div><h2 class="text-sm font-extrabold text-slate-800">Formulir Permohonan</h2><p class="mt-0.5 text-[11px] text-slate-400">Lengkapi data secara berurutan untuk mengirim tiket.</p></div></div>
            <form action="{{ route('user.tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                
                <!-- 1. Pilih Dinas -->
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-800 md:text-sm">
                        1. Pilih Dinas / Instansi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="inputDinas" 
                                name="dinas" 
                                required 
                                class="w-full appearance-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_1px_2px_rgba(15,23,42,0.04)] outline-none transition focus:border-[#397fa5] focus:ring-2 focus:ring-[#397fa5]/15 cursor-pointer">
                            <option value="" disabled selected>Pilih dinas...</option>
                            @foreach($dinasList as $dinas)
                                <option value="{{ $dinas }}" {{ old('dinas') == $dinas ? 'selected' : '' }}>{{ $dinas }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                    @error('dinas')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Pilih Kategori Layanan -->
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-800 md:text-sm">
                        2. Pilih Kategori Layanan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="inputKategori" 
                                name="category_id" 
                                disabled 
                                required 
                                class="w-full cursor-not-allowed appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-400 outline-none transition focus:border-[#397fa5] focus:ring-2 focus:ring-[#397fa5]/15">
                            <option value="" disabled selected>Pilih kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                    @error('category_id')
                        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Penjelasan Permohonan -->
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-xs font-bold text-slate-800 md:text-sm">
                            3. Penjelasan Permohonan <span class="text-rose-500">*</span>
                        </label>
                        
                        <!-- Subjek / Judul Ringkas -->
                        <input type="text" 
                               id="inputSubject" 
                               name="subject" 
                               disabled 
                               value="{{ old('subject') }}"
                               placeholder="Subjek / judul kendala (misal: Gangguan Koneksi Ruang Rapat Lt. 2)" 
                               class="mb-3 w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-400 outline-none transition focus:border-[#397fa5] focus:ring-2 focus:ring-[#397fa5]/15">

                        <!-- Textarea Deskripsi Rinci -->
                        <textarea id="inputPenjelasan" 
                                  name="description" 
                                  disabled 
                                  required 
                                  rows="4" 
                                  placeholder="Jelaskan kendala atau kebutuhan Anda secara detail..." 
                                  class="w-full cursor-not-allowed resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-400 outline-none transition focus:border-[#397fa5] focus:ring-2 focus:ring-[#397fa5]/15">{{ old('description') }}</textarea>
                        
                        <p class="text-[11px] text-gray-400 mt-1.5">Sertakan ruangan/lokasi spesifik, kronologi kejadian, dan pesan galat bila ada.</p>
                        @error('description')
                            <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 4. Lampiran File -->
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-800 md:text-sm">Lampiran Berkas (Opsional)</label>
                    <div class="relative flex items-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50/70 transition hover:bg-slate-50">
                        <input type="file" 
                               id="inputFile" 
                               name="attachment" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                               title="Pilih File">
                        <div class="z-0 shrink-0 whitespace-nowrap border-r border-slate-300 bg-[#347fa5] px-5 py-3 text-xs font-bold text-white">
                            Choose File
                        </div>
                        <div id="fileName" class="z-0 w-full truncate px-4 py-3 text-xs text-slate-400">
                            No file chosen
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">PDF, JPG, PNG, atau DOCX. Maksimal 10 MB.</p>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2 flex items-center gap-3">
                    <a href="{{ route('user.tiket') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmit" class="rounded-xl bg-[#347fa5] px-6 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-[#2c6a8c] focus:outline-none focus:ring-2 focus:ring-[#347fa5]/30">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: ALUR PENGAJUAN (TRACKER 3 LANGKAH) -->
        <!-- ========================================== -->
        <div class="h-fit w-full shrink-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-6 text-sm font-extrabold text-slate-800">Alur pengajuan</h2>
            
            <div class="flex flex-col gap-6 relative">
                <!-- Garis Penghubung Vertikal -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-100 -z-10"></div>

                <!-- Step 1: Dinas (Aktif Default) -->
                <div class="flex items-center gap-4">
                    <div id="step1-icon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#347fa5] bg-[#347fa5] text-sm font-bold text-white shadow-sm transition-all duration-300">
                        <span class="number">1</span>
                    </div>
                    <span id="step1-text" class="text-sm font-bold text-[#1d405c] transition-colors duration-300">Dinas / Instansi</span>
                </div>

                <!-- Step 2: Kategori -->
                <div class="flex items-center gap-4">
                    <div id="step2-icon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#dce7ee] bg-[#edf3f7] text-sm font-bold text-[#526b7d] transition-all duration-300">
                        <span class="number">2</span>
                    </div>
                    <span id="step2-text" class="text-sm font-semibold text-[#647b8b] transition-colors duration-300">Kategori Layanan</span>
                </div>

                <!-- Step 3: Penjelasan -->
                <div class="flex items-center gap-4">
                    <div id="step3-icon" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-[#dce7ee] bg-[#edf3f7] text-sm font-bold text-[#526b7d] transition-all duration-300">
                        <span class="number">3</span>
                    </div>
                    <span id="step3-text" class="text-sm font-semibold text-[#647b8b] transition-colors duration-300">Penjelasan & Detail</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= JAVASCRIPT LOGIC ================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputDinas = document.getElementById('inputDinas');
    const inputKategori = document.getElementById('inputKategori');
    const inputSubject = document.getElementById('inputSubject');
    const inputPenjelasan = document.getElementById('inputPenjelasan');
    const inputFile = document.getElementById('inputFile');
    const fileName = document.getElementById('fileName');

    // Aktifkan field tanpa meninggalkan warna disabled (slate-50/slate-400).
    // Class yang lama memakai slate-*, sedangkan script sebelumnya hanya
    // menghapus gray-* sehingga field tampak tetap pudar walaupun sudah aktif.
    function unlockInput(element) {
        if (!element) return;

        element.disabled = false;
        element.classList.remove(
            'bg-gray-50', 'text-gray-400', 'cursor-not-allowed', 'border-gray-200',
            'bg-slate-50', 'text-slate-400', 'border-slate-200'
        );
        element.classList.add('bg-white', 'text-slate-700', 'border-slate-300');
        element.style.opacity = '1';
        element.style.color = '#334155';
        element.style.cursor = element.tagName === 'SELECT' ? 'pointer' : 'text';
    }

    // Gunakan inline style untuk warna tracker agar tidak bergantung pada
    // Tailwind CDN saat class arbitrary ditambahkan lewat JavaScript.
    function updateTracker(stepNumber, mode) {
        const icon = document.getElementById(`step${stepNumber}-icon`);
        const text = document.getElementById(`step${stepNumber}-text`);
        if (!icon || !text) return;

        const isEnabled = mode === 'active' || mode === 'completed';

        if (isEnabled) {
            icon.style.backgroundColor = '#347fa5';
            icon.style.borderColor = '#347fa5';
            icon.style.color = '#ffffff';
            icon.style.boxShadow = '0 4px 10px rgba(52, 127, 165, 0.20)';
            text.style.color = '#1d405c';
            text.style.fontWeight = '700';
        } else {
            icon.style.backgroundColor = '#edf3f7';
            icon.style.borderColor = '#dce7ee';
            icon.style.color = '#526b7d';
            icon.style.boxShadow = 'none';
            text.style.color = '#647b8b';
            text.style.fontWeight = '600';
        }

        if (mode === 'completed') {
            icon.innerHTML = '<i class="fas fa-check text-xs" aria-hidden="true"></i>';
        } else {
            icon.innerHTML = `<span class="number">${stepNumber}</span>`;
        }
    }

    // 1. Saat Dinas Dipilih -> Buka Kategori (Step 2)
    inputDinas.addEventListener('change', function() {
        if (this.value !== "") {
            updateTracker(1, 'completed');
            updateTracker(2, 'active');
            unlockInput(inputKategori);
            inputKategori.classList.add('cursor-pointer');
        }
    });

    // 2. Saat Kategori Dipilih -> Langsung Buka Penjelasan (Step 3)
    inputKategori.addEventListener('change', function() {
        if (this.value !== "") {
            updateTracker(2, 'completed');
            updateTracker(3, 'active');
            unlockInput(inputSubject);
            unlockInput(inputPenjelasan);
            inputPenjelasan.focus();
        }
    });

    // 3. Saat Penjelasan Diisi
    inputPenjelasan.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            updateTracker(3, 'completed');
        } else {
            // Tahap penjelasan masih aktif, hanya tanda selesai yang dikembalikan.
            updateTracker(3, 'active');
        }
    });

    // 4. Input File Display
    inputFile.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            fileName.textContent = e.target.files[0].name;
            fileName.classList.remove('text-gray-400');
            fileName.classList.add('font-semibold');
            fileName.style.color = '#1d405c';
        } else {
            fileName.textContent = 'No file chosen';
            fileName.classList.remove('font-semibold');
            fileName.classList.add('text-gray-400');
            fileName.style.color = '';
        }
    });

    // Cek jika ada old value (setelah validasi gagal)
    if (inputDinas.value !== "") {
        updateTracker(1, 'completed');
        unlockInput(inputKategori);
        if (inputKategori.value !== "") {
            updateTracker(2, 'completed');
            unlockInput(inputSubject);
            unlockInput(inputPenjelasan);
            if (inputPenjelasan.value.trim().length > 0) {
                updateTracker(3, 'completed');
            }
        }
    }
});
</script>
@endsection