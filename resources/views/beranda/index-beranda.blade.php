<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - SIGELATIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Tambahkan Chart.js untuk grafik History Tiket -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .grid-bg {
            background-image: linear-gradient(to right, #f0f0f0 1px, transparent 1px), linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fbff] text-gray-700 antialiased overflow-x-hidden">

    <!-- ========================================== -->
    <!-- 1. NAVBAR -->
    <!-- ========================================== -->
    <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 py-4 fixed w-full top-0 z-50">
        <div class="max-w-6xl mx-auto w-full px-6 flex justify-between items-center">
            
            <!-- Bagian Kiri: Logo -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logofavicon.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <h1 class="text-[15px] font-bold text-[#1e3a8a] leading-none">SIGELATIK</h1>
                    <p class="text-[9px] text-gray-500">Sistem Informasi Gangguan Layanan<br>TIK</p>
                </div>
            </div>
            
            <!-- Bagian Tengah: Menu Link -->
            <nav class="hidden md:flex gap-8 text-sm font-semibold text-gray-600">
                <a href="#" class="text-[#1a3668]">Beranda</a>
                <a href="#" class="hover:text-[#1a3668] transition-colors">Tentang</a>
                <a href="#" class="hover:text-[#1a3668] transition-colors">Statistik</a>
                <a href="#" class="hover:text-[#1a3668] transition-colors">Layanan</a>
            </nav>

            <!-- Bagian Kanan: Tombol Login -->
            <div>
                @auth
                    <!-- Tombol ini muncul jika user SUDAH login -->
                    <a href="{{ route('login') }}" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-150 flex items-center gap-2 transition-colors">
                        <i class="far fa-user"></i> Dashboard
                    </a>
                @else
                    <!-- Tombol ini muncul jika user BELUM login -->
                    <a href="{{ route('login') }}" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-blue-100 flex items-center gap-2 transition-colors">
                        <i class="far fa-user"></i> Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- 2. HERO SECTION & LACAK TIKET -->
    <!-- ========================================== -->
    <section class="grid-bg pt-32 pb-16 px-6 md:px-16 flex flex-col items-center">
        <div class="max-w-6xl w-full flex flex-col lg:flex-row items-center gap-12">
            
            <!-- Kiri: Teks -->
            <div class="flex-1 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-gray-200 text-[#1a3668] text-[10px] font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Pusat Layanan Gangguan TIK
                </div>
                
                <h2 class="text-5xl md:text-6xl font-extrabold text-[#1a3668] leading-[1.1]">
                    Bantuan TIK,<br><span class="text-blue-600">Lebih Cepat.</span>
                </h2>
                
                <p class="text-gray-500 text-lg max-w-md">
                    SIGELATIK mencatat, mengelola, dan memantau laporan gangguan layanan TIK Anda dalam satu sistem yang mudah, transparan, dan terintegrasi.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="#" class="bg-[#1a3668] text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-[#112445] transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-ticket-alt"></i> Buat Permohonan Tiket <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                    <a href="#" class="bg-white border border-gray-200 text-gray-700 px-6 py-3 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-search"></i> Cek Status Tiket
                    </a>
                </div>
                
                <div class="flex items-center gap-6 pt-4 text-xs font-semibold text-gray-400">
                    <span class="flex items-center gap-1"><i class="fas fa-check text-green-500"></i> Layanan terintegrasi</span>
                    <span class="flex items-center gap-1"><i class="fas fa-check text-green-500"></i> Status waktu nyata</span>
                    <span class="flex items-center gap-1"><i class="fas fa-check text-green-500"></i> Aman dan transparan</span>
                </div>
            </div>

            <!-- Kanan: Gambar Ilustrasi Dashboard -->
            <div class="flex-1 relative">
                <!-- Gunakan gambar ilustrasi Anda jika ada, atau buat kotak mock-up -->
                <div class="bg-white p-4 rounded-2xl shadow-xl border border-gray-100 relative z-10 w-full max-w-lg mx-auto transform hover:-translate-y-2 transition-transform duration-500">
                    <div class="flex items-center gap-2 mb-4 px-2">
                        <div class="flex gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-red-400"></div><div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div><div class="w-2.5 h-2.5 rounded-full bg-green-400"></div></div>
                        <div class="mx-auto text-[10px] font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-md">sigelatik.go.id</div>
                    </div>
                    <!-- Mockup dalam box -->
                    <div class="flex gap-4 h-64">
                        <div class="w-16 bg-[#1a3668] rounded-xl flex flex-col items-center py-4 gap-4 text-white/50">
                            <i class="fas fa-desktop text-white"></i><i class="fas fa-sitemap"></i><i class="fas fa-mobile-alt"></i><i class="far fa-user"></i>
                        </div>
                        <div class="flex-1 flex flex-col gap-4">
                            <div class="flex justify-between items-end"><div class="text-[10px] text-gray-400">Dasbor Layanan<br><span class="text-sm font-bold text-[#1a3668]">Ringkasan Tiket</span></div><div class="text-[9px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded">HARI INI</div></div>
                            <div class="flex gap-2">
                                <div class="flex-1 bg-gray-50 rounded-lg p-3 border border-gray-100"><div class="text-xl font-bold text-blue-600">24</div><div class="text-[9px] text-gray-500">Tiket Masuk</div></div>
                                <div class="flex-1 bg-orange-50 rounded-lg p-3 border border-orange-100"><div class="text-xl font-bold text-orange-500">16</div><div class="text-[9px] text-gray-500">Diproses</div></div>
                                <div class="flex-1 bg-green-50 rounded-lg p-3 border border-green-100"><div class="text-xl font-bold text-green-500">82</div><div class="text-[9px] text-gray-500">Selesai</div></div>
                            </div>
                            <div class="flex-1 bg-gray-50 rounded-lg border border-gray-100 p-3 flex flex-col justify-end gap-1 relative overflow-hidden">
                                <div class="text-[9px] text-gray-400 absolute top-2 left-3">Aktivitas Tiket</div><div class="text-[8px] text-gray-400 absolute top-2 right-3">7 hari terakhir</div>
                                <!-- Bar chart bohongan -->
                                <div class="flex items-end justify-between h-12 w-full px-2 gap-1 opacity-70">
                                    <div class="w-full bg-blue-200 rounded-t-sm h-[30%]"></div><div class="w-full bg-blue-200 rounded-t-sm h-[50%]"></div><div class="w-full bg-blue-200 rounded-t-sm h-[40%]"></div>
                                    <div class="w-full bg-blue-200 rounded-t-sm h-[70%]"></div><div class="w-full bg-blue-200 rounded-t-sm h-[60%]"></div><div class="w-full bg-blue-600 rounded-t-sm h-[90%]"></div><div class="w-full bg-blue-200 rounded-t-sm h-[50%]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Box Terapung -->
                <div class="absolute -bottom-6 -left-6 bg-white p-3 rounded-xl shadow-lg border border-gray-100 flex items-center gap-3 z-20 animate-bounce" style="animation-duration: 3s;">
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-500 flex items-center justify-center"><i class="fas fa-check"></i></div>
                    <div><p class="text-xs font-bold text-[#1a3668]">Tiket terselesaikan</p><p class="text-[10px] text-gray-500">Rata-rata 2.4 jam</p></div>
                </div>
            </div>
        </div>

        <!-- Box Lacak Tiket Tengah -->
        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 p-6 md:p-8 mt-20 relative z-20 flex flex-col md:flex-row items-center gap-6">
            <div class="flex gap-4 items-start w-full md:w-1/3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-search"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-[#1a3668]">Lacak Status Tiket</h3>
                    <p class="text-xs text-gray-500 mt-1">Cek perkembangan layanan tanpa perlu masuk.</p>
                </div>
            </div>
            <div class="w-full md:w-2/3 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-ticket-alt absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Nomor tiket, mis. HD-2610-0142" class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                </div>
                <button class="bg-[#1a3668] text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-[#112445] transition-colors shrink-0">
                    Cari Tiket
                </button>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 3. PENJELASAN 3 LANGKAH -->
    <!-- ========================================== -->
    <section class="py-16 px-6 md:px-16 bg-white flex justify-center">
        <div class="max-w-6xl w-full flex flex-col lg:flex-row gap-12 items-center">
            
            <!-- Teks Kiri -->
            <div class="flex-1 max-w-md">
                <h2 class="text-3xl md:text-4xl font-bold text-[#1a3668] leading-tight mb-4">
                    Satu Sistem untuk Setiap Gangguan Layanan TIK
                </h2>
                <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                    SIGELATIK (Sistem Informasi Gangguan Layanan TIK) menerima laporan gangguan dari perangkat daerah di lingkup Pemerintah Provinsi Kalimantan Timur, lalu memantaunya hingga selesai dalam tiga langkah.
                </p>
                <a href="{{ route('login') }}" class="bg-[#1a3668] text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-[#112445] transition-colors inline-flex items-center gap-2">
                    Mulai Buat Tiket <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Kartu Kanan (3 Steps) -->
            <div class="flex-1 flex flex-col sm:flex-row gap-4 w-full">
                <!-- Step 1 -->
                <div class="bg-gray-50 border border-gray-100 p-6 rounded-2xl flex-1 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-ticket-alt"></i></div>
                        <span class="text-2xl font-bold text-gray-200">01</span>
                    </div>
                    <h3 class="text-[#1a3668] font-bold mb-2">Buat Tiket</h3>
                    <p class="text-xs text-gray-500">Isi formulir permohonan dan jelaskan kendala yang Anda alami.</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-gray-50 border border-gray-100 p-6 rounded-2xl flex-1 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="far fa-clock"></i></div>
                        <span class="text-2xl font-bold text-gray-200">02</span>
                    </div>
                    <h3 class="text-[#1a3668] font-bold mb-2">Tim Memproses</h3>
                    <p class="text-xs text-gray-500">Petugas terkait menerima dan menindaklanjuti permohonan Anda.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-gray-50 border border-gray-100 p-6 rounded-2xl flex-1 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-check"></i></div>
                        <span class="text-2xl font-bold text-gray-200">03</span>
                    </div>
                    <h3 class="text-[#1a3668] font-bold mb-2">Masalah Selesai</h3>
                    <p class="text-xs text-gray-500">Dapatkan notifikasi dan konfirmasi saat solusi telah diberikan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 4. STATISTIK LAYANAN -->
    <!-- ========================================== -->
    <section class="py-16 px-6 md:px-16 bg-[#f8fbff] flex justify-center">
        <div class="max-w-6xl w-full">
            <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
                <div>
                    <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-2">STATISTIK LAYANAN</div>
                    <h2 class="text-3xl font-bold text-[#1a3668]">Ringkasan Permohonan Tiket</h2>
                </div>
                <p class="text-sm text-gray-500 text-right max-w-xs">Data ringkas permohonan layanan teknologi informasi berdasarkan tiket yang diterima.</p>
            </div>

            <!-- Tiga Kotak Donut Chart (Disimulasikan dengan HTML CSS bulat) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kategori -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-[#1a3668]">Total Tiket Berdasarkan Kategori</h3>
                    <p class="text-[10px] text-gray-400 mb-6">Distribusi jenis layanan</p>
                    <div class="flex items-center justify-between mt-auto">
                        <!-- CSS Donut Circle -->
                        <div class="relative w-24 h-24 rounded-full border-[10px] border-cyan-400 border-r-purple-400 border-b-gray-300 border-l-blue-600 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-xl font-bold text-[#1a3668] leading-none">126</div>
                                <div class="text-[8px] text-gray-400 mt-1">Total Tiket</div>
                            </div>
                        </div>
                        <div class="text-xs space-y-2">
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Aplikasi & W...</span><span class="font-bold text-gray-700">48</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-cyan-400"></span> Jaringan & Int...</span><span class="font-bold text-gray-700">34</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-purple-400"></span> Perangkat TIK</span><span class="font-bold text-gray-700">26</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-gray-300"></span> Lainnya</span><span class="font-bold text-gray-700">18</span></div>
                        </div>
                    </div>
                </div>

                <!-- Prioritas -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-[#1a3668]">Total Tiket Berdasarkan Prioritas</h3>
                    <p class="text-[10px] text-gray-400 mb-6">Tingkat urgensi permohonan</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="relative w-24 h-24 rounded-full border-[10px] border-orange-400 border-t-green-500 border-r-orange-400 border-b-orange-400 border-l-red-500 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-xl font-bold text-[#1a3668] leading-none">127</div>
                                <div class="text-[8px] text-gray-400 mt-1">Total Tiket</div>
                            </div>
                        </div>
                        <div class="text-xs space-y-2">
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-green-500"></span> Rendah</span><span class="font-bold text-gray-700">28</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-orange-400"></span> Sedang</span><span class="font-bold text-gray-700">57</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-orange-600"></span> Tinggi</span><span class="font-bold text-gray-700">32</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-red-500"></span> Darurat</span><span class="font-bold text-gray-700">10</span></div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <h3 class="text-sm font-bold text-[#1a3668]">Total Tiket Berdasarkan Status</h3>
                    <p class="text-[10px] text-gray-400 mb-6">Perkembangan penanganan tiket</p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="relative w-24 h-24 rounded-full border-[10px] border-green-500 border-b-blue-500 border-l-yellow-400 border-t-green-500 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-xl font-bold text-[#1a3668] leading-none">127</div>
                                <div class="text-[8px] text-gray-400 mt-1">Total Tiket</div>
                            </div>
                        </div>
                        <div class="text-xs space-y-2">
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-green-500"></span> Selesai</span><span class="font-bold text-gray-700">66</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Diproses</span><span class="font-bold text-gray-700">32</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-yellow-400"></span> Menunggu</span><span class="font-bold text-gray-700">19</span></div>
                            <div class="flex items-center justify-between gap-4"><span class="flex items-center gap-2 text-gray-500"><span class="w-2 h-2 rounded-full bg-red-500"></span> Ditolak</span><span class="font-bold text-gray-700">10</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Kotak Panjang History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-sm font-bold text-[#1a3668]">History Ticket Permohonan</h3>
                        <p class="text-[10px] text-gray-400">Jumlah tiket masuk setiap bulan selama tahun berjalan</p>
                    </div>
                    <div class="px-3 py-1 bg-blue-50 text-blue-600 rounded-md text-[10px] font-bold flex items-center gap-1.5 border border-blue-100">
                        <i class="fas fa-ticket-alt"></i> 127 tiket tahun ini
                    </div>
                </div>
                <div class="w-full h-48">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>
            
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 5. DIREKTORI LAYANAN -->
    <!-- ========================================== -->
    <section class="py-16 px-6 md:px-16 bg-white flex flex-col items-center">
        <div class="text-center mb-10 max-w-xl">
            <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-2">DIREKTORI LAYANAN</div>
            <h2 class="text-3xl font-bold text-[#1a3668] mb-4">Temukan Layanan yang Anda Butuhkan</h2>
            <p class="text-sm text-gray-500">Pilih kategori layanan sesuai kebutuhan perangkat daerah Anda. Tim kami siap membantu setiap kendala teknologi informasi.</p>
        </div>

        <div class="max-w-6xl w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Bidang 1 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5"><i class="fas fa-code"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Layanan Aplikasi</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang e-Government • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Akun & email dinas</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Subdomain & hosting</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

            <!-- Bidang 2 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center mb-5"><i class="fas fa-network-wired"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Infrastruktur Jaringan</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang Jaringan & Server • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Gangguan LAN / WLAN</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Akses VPN</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

            <!-- Bidang 3 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-5"><i class="fas fa-shield-alt"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Keamanan Informasi</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang Persandian • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Sertifikat elektronik</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Insiden keamanan</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

            <!-- Bidang 4 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-5"><i class="fas fa-chart-pie"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Statistik & Data</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang Statistik Sektoral • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Satu Data Daerah</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Permintaan data</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

            <!-- Bidang 5 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-5"><i class="far fa-newspaper"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Humas & Publik</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang Layanan Informasi • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Portal PPID</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Konten website</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

            <!-- Bidang 6 -->
            <div class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow rounded-2xl p-6">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-5"><i class="fas fa-desktop"></i></div>
                <h3 class="text-lg font-bold text-[#1a3668] mb-1">Bidang Perangkat Pengguna</h3>
                <p class="text-[11px] text-gray-400 mb-4 pb-4 border-b border-gray-100">Sub-Bidang Dukungan Pengguna • 4 layanan</p>
                <div class="space-y-3 mb-6 text-sm text-gray-600">
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Perbaikan komputer</div>
                    <div class="flex items-center gap-3"><div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center"><i class="fas fa-check text-[8px] text-gray-300"></i></div> Printer & scanner</div>
                </div>
                <a href="#" class="text-xs font-bold text-[#1a3668] hover:text-blue-600 transition-colors">Lihat semua layanan <i class="fas fa-chevron-right ml-1"></i></a>
            </div>

        </div>
    </section>

    <!-- ========================================== -->
    <!-- 6. FOOTER -->
    <!-- ========================================== -->
    <footer class="bg-[#112040] text-gray-300 py-12 px-6 md:px-16 flex justify-center">
        <div class="max-w-6xl w-full flex flex-col md:flex-row justify-between gap-10">
            <!-- Footer Kiri -->
            <div class="max-w-sm">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('img/logofavicon.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                    <div>
                        <h1 class="text-[15px] font-bold text-white leading-none">SIGELATIK</h1>
                        <p class="text-[9px] text-gray-400">Sistem Informasi Gangguan Layanan<br>TIK</p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Sistem Informasi Gangguan Layanan TIK untuk pelayanan publik yang lebih cepat dan terukur.
                </p>
            </div>

            <!-- Footer Kanan (Link) -->
            <div class="flex flex-col sm:flex-row gap-12 md:gap-24">
                <div>
                    <h4 class="text-white font-bold mb-4 text-sm">Tautan Cepat</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Direktori Layanan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Lacak Tiket</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tentang SIGELATIK</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4 text-sm">Pusat Bantuan</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-center gap-2"><i class="far fa-envelope"></i> helpdesk@sigelatik.go.id</li>
                        <li class="flex items-center gap-2"><i class="far fa-clock"></i> Senin–Jumat, 08.00–16.00</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- Latar belakang gelap full width -->
    <div class="bg-[#0b152b] w-full flex justify-center">
        <!-- Konten dibatasi lebarnya agar sejajar dengan atas -->
        <div class="text-center md:text-left py-4 px-6 md:px-16 flex flex-col md:flex-row justify-between items-center text-xs text-gray-400 max-w-6xl w-full">
            <p>&copy; 2026 SIGELATIK - Dinas Komunikasi dan Informatika. Hak cipta dilindungi.</p>
            <p class="mt-2 md:mt-0 flex items-center gap-2"><i class="fas fa-shield-alt"></i> Sistem aman dan terpercaya</p>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('historyChart').getContext('2d');
            
            // Gradient untuk area di bawah garis
            let gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)'); // blue-600 opacity
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Tiket Masuk',
                        data: [1, 3, 2, 5, 3, 8, 6, 12, 4, 7, 9, 3],
                        borderColor: '#2563eb', // text-blue-600
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Sembunyikan titik seperti desain
                        pointHoverRadius: 4,
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung (smooth curve)
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } // Sembunyikan legenda
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 12,
                            ticks: { stepSize: 3, color: '#9ca3af', font: { size: 10 } },
                            border: { display: false, dash: [4, 4] },
                            grid: { color: '#f3f4f6', tickLength: 0 }
                        },
                        x: {
                            ticks: { color: '#9ca3af', font: { size: 10 } },
                            border: { display: false },
                            grid: { display: true, color: '#f3f4f6', drawOnChartArea: true, drawTicks: false, tickLength: 0, borderDash: [4, 4] }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
</body>
</html>