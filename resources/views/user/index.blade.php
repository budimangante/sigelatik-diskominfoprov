@extends('components.b-master')

@section('title', 'Buat Permohonan Layanan')
@section('subtitle', 'Pilih dinas dan kategori untuk mengajukan permohonan ke Diskominfo')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    
    <!-- Header Section -->
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>USER</span>
            <span class="text-gray-300">•</span>
            <span>TIKET BARU</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Buat permohonan layanan</h1>
        <p class="text-gray-500">Pilih dinas dan kategori layanan secara berurutan, lalu jelaskan kebutuhan Anda.</p>
    </div>

    <!-- Layout Utama: Form Kiri & Alur Kanan -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR INPUT -->
        <!-- ========================================== -->
        <div class="flex-1 w-full bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('user.tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- 1. Pilih Dinas -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">
                        1. Pilih Dinas / Instansi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="inputDinas" 
                                name="dinas" 
                                required 
                                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih dinas...</option>
                            @foreach($dinasList as $dinas)
                                <option value="{{ $dinas }}" {{ old('dinas') == $dinas ? 'selected' : '' }}>{{ $dinas }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    @error('dinas')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Pilih Kategori Layanan -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">
                        2. Pilih Kategori Layanan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="inputKategori" 
                                name="category_id" 
                                disabled 
                                required 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors appearance-none cursor-not-allowed">
                            <option value="" disabled selected>Pilih kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    @error('category_id')
                        <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Penjelasan Permohonan -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">
                            3. Penjelasan Permohonan <span class="text-rose-500">*</span>
                        </label>
                        
                        <!-- Subjek / Judul Ringkas -->
                        <input type="text" 
                               id="inputSubject" 
                               name="subject" 
                               disabled 
                               value="{{ old('subject') }}"
                               placeholder="Subjek / judul kendala (misal: Gangguan Koneksi Ruang Rapat Lt. 2)" 
                               class="w-full mb-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors cursor-not-allowed">

                        <!-- Textarea Deskripsi Rinci -->
                        <textarea id="inputPenjelasan" 
                                  name="description" 
                                  disabled 
                                  required 
                                  rows="4" 
                                  placeholder="Jelaskan kendala atau kebutuhan Anda secara detail..." 
                                  class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors resize-y cursor-not-allowed">{{ old('description') }}</textarea>
                        
                        <p class="text-[11px] text-gray-400 mt-1.5">Sertakan ruangan/lokasi spesifik, kronologi kejadian, dan pesan galat bila ada.</p>
                        @error('description')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 4. Lampiran File -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Lampiran Berkas (Opsional)</label>
                    <div class="relative flex items-center border border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50/50 hover:bg-gray-50 transition-colors">
                        <input type="file" 
                               id="inputFile" 
                               name="attachment" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                               title="Pilih File">
                        <div class="bg-[#1a3668] text-white px-5 py-2.5 text-sm font-semibold z-0 border-r border-gray-300 whitespace-nowrap shrink-0">
                            Choose File
                        </div>
                        <div id="fileName" class="px-4 text-sm text-gray-400 z-0 truncate w-full">
                            No file chosen
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1.5">PDF, JPG, PNG, atau DOCX. Maksimal 10 MB.</p>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2 flex items-center gap-3">
                    <a href="{{ route('user.tiket') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold transition">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmit" class="bg-[#1a3668] text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#112445] transition-colors shadow-sm">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: ALUR PENGAJUAN (TRACKER 3 LANGKAH) -->
        <!-- ========================================== -->
        <div class="w-full lg:w-80 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit shrink-0">
            <h2 class="text-base font-bold text-gray-800 mb-6">Alur pengajuan</h2>
            
            <div class="flex flex-col gap-6 relative">
                <!-- Garis Penghubung Vertikal -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-100 -z-10"></div>

                <!-- Step 1: Dinas (Aktif Default) -->
                <div class="flex items-center gap-4">
                    <div id="step1-icon" class="w-8 h-8 rounded-full bg-[#1a3668] text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-sm transition-all duration-300">
                        <span class="number">1</span>
                    </div>
                    <span id="step1-text" class="text-sm font-bold text-[#1a3668] transition-colors duration-300">Dinas / Instansi</span>
                </div>

                <!-- Step 2: Kategori -->
                <div class="flex items-center gap-4">
                    <div id="step2-icon" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300">
                        <span class="number">2</span>
                    </div>
                    <span id="step2-text" class="text-sm font-medium text-gray-400 transition-colors duration-300">Kategori Layanan</span>
                </div>

                <!-- Step 3: Penjelasan -->
                <div class="flex items-center gap-4">
                    <div id="step3-icon" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300">
                        <span class="number">3</span>
                    </div>
                    <span id="step3-text" class="text-sm font-medium text-gray-400 transition-colors duration-300">Penjelasan & Detail</span>
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

    function unlockInput(element) {
        element.disabled = false;
        element.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed', 'border-gray-200');
        element.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    }

    function updateTracker(stepNumber, mode) {
        const icon = document.getElementById(`step${stepNumber}-icon`);
        const text = document.getElementById(`step${stepNumber}-text`);
        if (!icon || !text) return;

        if (mode === 'active' || mode === 'completed') {
            icon.classList.remove('bg-gray-100', 'text-gray-500');
            icon.classList.add('bg-[#1a3668]', 'text-white', 'shadow-sm');
            text.classList.remove('text-gray-400', 'font-medium');
            text.classList.add('text-[#1a3668]', 'font-bold');
        }

        if (mode === 'completed') {
            icon.innerHTML = '<i class="fas fa-check text-xs"></i>';
        } else if (mode === 'active') {
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
            const icon3 = document.getElementById('step3-icon');
            if (icon3) icon3.innerHTML = '<span class="number">3</span>';
        }
    });

    // 4. Input File Display
    inputFile.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            fileName.textContent = e.target.files[0].name;
            fileName.classList.remove('text-gray-400');
            fileName.classList.add('text-[#1a3668]', 'font-medium');
        } else {
            fileName.textContent = 'No file chosen';
            fileName.classList.remove('text-[#1a3668]', 'font-medium');
            fileName.classList.add('text-gray-400');
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