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
            <span>Helpdesk Bidang</span>
            <span class="text-gray-300">•</span>
            <span>Layanan Aplikasi</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Tiket bidang</h1>
        <p class="text-gray-500">Monitor seluruh tiket beserta waktu SLA, lalu tugaskan atau tugaskan ulang ke teknisi.</p>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium tracking-wider">Tiket</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Judul</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Status</th>
                    <th class="px-6 py-4 font-medium tracking-wider w-56">SLA</th>
                    <th class="px-6 py-4 font-medium tracking-wider">Teknisi</th>
                    <th class="px-6 py-4 font-medium tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-gray-100 text-gray-600">
                <!-- Baris 1 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-blue-600 font-medium align-top">HD-2610-0142</td>
                    <td class="px-6 py-4 align-top">
                        <div class="font-bold text-gray-800">Akun email dinas tidak bisa login</div>
                        <div class="text-xs text-gray-500 mt-1">Dinas Pendidikan</div>
                    </td>
                    <td class="px-6 py-4 align-top"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-blue-200 text-blue-700 bg-blue-50"><span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> PROCESS</span></td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex justify-between items-center mb-1 text-xs"><span class="text-[#1a3668] font-bold">Berjalan</span><span class="text-gray-400">target 4 jam</span></div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-[#1a3668] h-1.5 rounded-full" style="width: 40%"></div></div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 align-top">Bima Prakoso</td>
                    <td class="px-6 py-4 align-top text-center">
                        <!-- Tambahan onclick="bukaModal(...)" -->
                        <button onclick="bukaModal('HD-2610-0142')" class="px-4 py-1.5 border border-[#1a3668] text-[#1a3668] rounded-md hover:bg-[#1a3668] hover:text-white transition-colors text-xs font-semibold">Reassign</button>
                    </td>
                </tr>

                <!-- Baris 2 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-blue-600 font-medium align-top">HD-2610-0139</td>
                    <td class="px-6 py-4 align-top">
                        <div class="font-bold text-gray-800">Jaringan LAN ruang arsip terputus</div>
                        <div class="text-xs text-gray-500 mt-1">Dinas Kearsipan</div>
                    </td>
                    <td class="px-6 py-4 align-top"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-cyan-200 text-cyan-700 bg-cyan-50"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> NEW</span></td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex justify-between items-center mb-1 text-xs"><span class="text-[#1a3668] font-bold">Berjalan</span><span class="text-gray-400">target 8 jam</span></div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-[#1a3668] h-1.5 rounded-full" style="width: 10%"></div></div>
                    </td>
                    <td class="px-6 py-4 text-gray-400 italic align-top">Belum ditugaskan</td>
                    <td class="px-6 py-4 align-top text-center">
                        <button onclick="bukaModal('HD-2610-0139')" class="px-4 py-1.5 border border-[#1a3668] text-[#1a3668] rounded-md hover:bg-[#1a3668] hover:text-white transition-colors text-xs font-semibold">Assign</button>
                    </td>
                </tr>

                <!-- Baris 3 -->
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-blue-600 font-medium align-top">HD-2610-0131</td>
                    <td class="px-6 py-4 align-top">
                        <div class="font-bold text-gray-800">Permohonan subdomain portal PPID</div>
                        <div class="text-xs text-gray-500 mt-1">Dinas Kominfo</div>
                    </td>
                    <td class="px-6 py-4 align-top"><span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border border-yellow-200 text-yellow-700 bg-yellow-50"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> PENDING</span></td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex justify-between items-center mb-1 text-xs"><span class="text-[#1a3668] font-bold">Berjalan</span><span class="text-gray-400">target 2 hari</span></div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5"><div class="bg-[#1a3668] h-1.5 rounded-full" style="width: 80%"></div></div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 align-top">Dewi Lestari</td>
                    <td class="px-6 py-4 align-top text-center">
                        <button onclick="bukaModal('HD-2610-0131')" class="px-4 py-1.5 border border-[#1a3668] text-[#1a3668] rounded-md hover:bg-[#1a3668] hover:text-white transition-colors text-xs font-semibold">Reassign</button>
                    </td>
                </tr>
                
                <!-- (Tambahkan baris lainnya jika perlu, cukup panggil bukaModal('ID_TIKET') di tag buttonnya) -->

            </tbody>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- MODAL POP-UP ASSIGN TEKNISI (Awalnya Hidden) -->
    <!-- ========================================== -->
    <div id="modalPenugasan" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm hidden transition-opacity">
        
        <!-- Kotak Modal -->
        <div class="bg-white rounded-2xl shadow-xl w-[90%] max-w-lg overflow-hidden flex flex-col">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold text-[#1a3668]">Assign tiket <span id="modalIdTiket"></span></h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih satu atau beberapa teknisi bidang.</p>
                </div>
                <!-- Tombol Close (X) -->
                <button onclick="tutupModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body Modal (List Teknisi) -->
            <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
                
                <!-- Teknisi 1 (Contoh menggunakan fitur has-[:checked] dari Tailwind) -->
                <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-lg cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                    <!-- accent-color agar kotak ceklis bawaan browser warnanya biru -->
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">Bima Prakoso</span>
                </label>

                <!-- Teknisi 2 -->
                <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-lg cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">Dewi Lestari</span>
                </label>

                <!-- Teknisi 3 -->
                <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-lg cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">Fajar Hidayat</span>
                </label>

                <!-- Teknisi 4 -->
                <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-lg cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">Nadia Putri</span>
                </label>

                <!-- Teknisi 5 -->
                <label class="flex items-center gap-3 p-3.5 border border-gray-200 rounded-lg cursor-pointer transition-all hover:bg-gray-50 has-[:checked]:bg-[#f4f7fb] has-[:checked]:border-[#1a3668]">
                    <input type="checkbox" class="w-5 h-5 rounded border-gray-300 accent-[#1a3668] cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700">Eko Wahyudi</span>
                </label>

            </div>

            <!-- Footer Modal (Tombol Aksi) -->
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/50">
                <button onclick="tutupModal()" class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 hover:text-gray-800 transition-colors text-sm font-semibold">
                    Batal
                </button>
                <button onclick="tutupModal()" class="px-5 py-2.5 bg-[#1a3668] text-white rounded-lg hover:bg-[#112445] transition-colors text-sm font-semibold shadow-md">
                    Simpan Penugasan
                </button>
            </div>
            
        </div>
    </div>
</div>
</body>
<!-- SCRIPT UNTUK MENGENDALIKAN MODAL -->
<script>
    function bukaModal(idTiket) {
        // 1. Ubah tulisan ID Tiket di judul modal
        document.getElementById('modalIdTiket').innerText = idTiket;
        // 2. Hilangkan class 'hidden' untuk memunculkan pop-up
        document.getElementById('modalPenugasan').classList.remove('hidden');
    }

    function tutupModal() {
        // 1. Tambahkan class 'hidden' untuk menyembunyikan pop-up
        document.getElementById('modalPenugasan').classList.add('hidden');
        
        // Opsional: Hapus semua centang (uncheck) saat modal ditutup
        const checkboxes = document.querySelectorAll('#modalPenugasan input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
    }
</script>
@endsection