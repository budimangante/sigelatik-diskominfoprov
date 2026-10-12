@extends('components.b-master') 
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>USER</span>
            <span class="text-gray-300">•</span>
            <span>TIKET BARU</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Buat permohonan layanan</h1>
        <p class="text-gray-500">Pilih dinas, kategori, dan layanan secara berurutan, lalu jelaskan kebutuhan Anda.</p>
    </div>

    <!-- Layout Utama: Form Kiri & Alur Kanan -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- ========================================== -->
        <!-- KOLOM KIRI: FORMULIR INPUT -->
        <!-- ========================================== -->
        <div class="flex-1 w-full bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- 1. Pilih Dinas -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">1. Pilih Dinas</label>
                    <select id="inputDinas" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm text-gray-600 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors appearance-none cursor-pointer">
                        <option value="" disabled selected>Pilih dinas...</option>
                        <option value="Dinas Komunikasi dan Informatika">Dinas Komunikasi dan Informatika</option>
                        <option value="Dinas Pendidikan">Dinas Pendidikan</option>
                        <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                    </select>
                </div>

                <!-- 2. Pilih Kategori Layanan -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">2. Pilih Kategori Layanan</label>
                    <!-- Default Disabled -->
                    <select id="inputKategori" disabled class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors appearance-none cursor-not-allowed">
                        <option value="" disabled selected>Pilih kategori...</option>
                    </select>
                </div>

                <!-- 3. Pilih Layanan -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">3. Pilih Layanan</label>
                    <!-- Default Disabled -->
                    <select id="inputLayanan" disabled class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors appearance-none cursor-not-allowed">
                        <option value="" disabled selected>Pilih layanan...</option>
                    </select>
                </div>

                <!-- 4. Penjelasan Permohonan -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">4. Penjelasan Permohonan</label>
                    <!-- Default Disabled -->
                    <textarea id="inputPenjelasan" disabled rows="4" placeholder="Jelaskan kendala atau kebutuhan Anda..." class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-[#1a3668]/20 focus:border-[#1a3668] outline-none transition-colors resize-y cursor-not-allowed"></textarea>
                    <p class="text-[11px] text-gray-400 mt-1.5">Sertakan lokasi, waktu kejadian, dan pesan galat bila ada.</p>
                </div>

                <!-- 5. Lampiran File -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-2">Lampiran</label>
                    <div class="relative flex items-center border border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50/50 hover:bg-gray-50 transition-colors">
                        <input type="file" id="inputFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" title="Pilih File">
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
                <div class="pt-2">
                    <button type="submit" class="bg-[#1a3668] text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-[#112445] transition-colors shadow-sm">
                        Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- KOLOM KANAN: ALUR PENGAJUAN (TRACKER) -->
        <!-- ========================================== -->
        <div class="w-full lg:w-80 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit shrink-0">
            <h2 class="text-base font-bold text-gray-800 mb-6">Alur pengajuan</h2>
            
            <div class="flex flex-col gap-5 relative">
                
                <!-- Garis Penghubung Vertikal (Opsional, untuk estetika) -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-100 -z-10"></div>

                <!-- Step 1: Dinas (Aktif Default) -->
                <div class="flex items-center gap-4">
                    <div id="step1-icon" class="w-8 h-8 rounded-full bg-[#1a3668] text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-sm transition-all duration-300">
                        <span class="number">1</span>
                    </div>
                    <span id="step1-text" class="text-sm font-bold text-[#1a3668] transition-colors duration-300">Dinas</span>
                </div>

                <!-- Step 2: Kategori -->
                <div class="flex items-center gap-4">
                    <div id="step2-icon" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300">
                        <span class="number">2</span>
                    </div>
                    <span id="step2-text" class="text-sm font-medium text-gray-400 transition-colors duration-300">Kategori</span>
                </div>

                <!-- Step 3: Layanan -->
                <div class="flex items-center gap-4">
                    <div id="step3-icon" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300">
                        <span class="number">3</span>
                    </div>
                    <span id="step3-text" class="text-sm font-medium text-gray-400 transition-colors duration-300">Layanan</span>
                </div>

                <!-- Step 4: Penjelasan -->
                <div class="flex items-center gap-4">
                    <div id="step4-icon" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300">
                        <span class="number">4</span>
                    </div>
                    <span id="step4-text" class="text-sm font-medium text-gray-400 transition-colors duration-300">Penjelasan</span>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT UNTUK LOGIKA INTERAKTIF -->
<!-- ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Deklarasi Elemen Input
    const inputDinas = document.getElementById('inputDinas');
    const inputKategori = document.getElementById('inputKategori');
    const inputLayanan = document.getElementById('inputLayanan');
    const inputPenjelasan = document.getElementById('inputPenjelasan');
    const inputFile = document.getElementById('inputFile');
    const fileName = document.getElementById('fileName');

    // 2. Fungsi untuk mengubah gaya input yang aktif (Buka Kunci)
    function unlockInput(element) {
        element.disabled = false;
        element.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed', 'border-gray-200');
        element.classList.add('bg-white', 'text-gray-600', 'border-gray-300', 'cursor-pointer');
    }

    // 3. Fungsi untuk memperbarui Tracker (Alur)
    // Mode bisa 'active' (biru angka) atau 'completed' (biru centang)
    function updateTracker(stepNumber, mode) {
        const icon = document.getElementById(`step${stepNumber}-icon`);
        const text = document.getElementById(`step${stepNumber}-text`);
        
        if (mode === 'active' || mode === 'completed') {
            // Ubah warna menjadi biru
            icon.classList.remove('bg-gray-100', 'text-gray-500');
            icon.classList.add('bg-[#1a3668]', 'text-white', 'shadow-sm');
            text.classList.remove('text-gray-400', 'font-medium');
            text.classList.add('text-[#1a3668]', 'font-bold');
        }

        if (mode === 'completed') {
            // Ganti angka dengan ikon centang
            icon.innerHTML = '<i class="fas fa-check text-xs"></i>';
        }
    }

    // 4. Logika saat Dinas Dipilih
    inputDinas.addEventListener('change', function() {
        if (this.value !== "") {
            // Selesaikan Step 1, Aktifkan Step 2
            updateTracker(1, 'completed');
            updateTracker(2, 'active');
            
            // Buka kunci Kategori
            unlockInput(inputKategori);
            
            // (Opsional) Memasukkan data dummy kategori agar interaktif seperti video
            inputKategori.innerHTML = `
                <option value="" disabled selected>Pilih kategori...</option>
                <option value="Layanan Aplikasi">Layanan Aplikasi</option>
                <option value="Infrastruktur">Infrastruktur</option>
            `;
        }
    });

    // 5. Logika saat Kategori Dipilih
    inputKategori.addEventListener('change', function() {
        if (this.value !== "") {
            // Selesaikan Step 2, Aktifkan Step 3
            updateTracker(2, 'completed');
            updateTracker(3, 'active');
            
            // Buka kunci Layanan
            unlockInput(inputLayanan);
            
            // Memasukkan data dummy layanan
            inputLayanan.innerHTML = `
                <option value="" disabled selected>Pilih layanan...</option>
                <option value="Pembuatan Subdomain">Pembuatan Subdomain</option>
                <option value="Reset Akun Email">Reset Akun Email</option>
                <option value="Perbaikan Aplikasi">Perbaikan Aplikasi</option>
            `;
        }
    });

    // 6. Logika saat Layanan Dipilih
    inputLayanan.addEventListener('change', function() {
        if (this.value !== "") {
            // Selesaikan Step 3, Aktifkan Step 4
            updateTracker(3, 'completed');
            updateTracker(4, 'active');
            
            // Buka kunci Penjelasan
            unlockInput(inputPenjelasan);
            inputPenjelasan.classList.remove('cursor-not-allowed'); // Khusus textarea
        }
    });

    // 7. Logika saat Penjelasan Diisi
    inputPenjelasan.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            // Selesaikan Step 4 jika mulai mengetik
            updateTracker(4, 'completed');
        } else {
            // Kembalikan ke angka 4 jika dihapus sampai kosong
            const icon4 = document.getElementById('step4-icon');
            icon4.innerHTML = '<span class="number">4</span>';
        }
    });

    // 8. Logika untuk Input File (Menampilkan Nama File)
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

});
</script>
</body>
</html>
@endsection