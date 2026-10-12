@extends('components.b-master') 
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user_profile') || '{}');
    
        // Jika tidak ada token atau bukan admin, langsung lempar ke login
        if (!token || (user.role !== 'admin_default' && user.role !== 'super_admin')) {
            localStorage.clear();
            window.location.href = '/login';
        }
    </script>
</head>
<body>
<!-- Tambahkan library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span>Pemerintah Provinsi</span>
            <span class="text-gray-300">•</span>
            <span>Mode Pantau</span>
        </div>
        <h1 class="text-3xl font-bold text-[#1a3668] mb-2">Dasbor Pemantauan SLA</h1>
        <p class="text-gray-500">Gambaran kepatuhan layanan TIK seluruh perangkat daerah. Akses hanya-baca.</p>
    </div>

    <!-- 4 Kotak Ringkasan Atas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Kotak 1: Kepatuhan SLA (Hijau) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <!-- Garis atas hijau -->
            <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">Kepatuhan SLA</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">96%</div>
            <div class="text-xs font-semibold text-green-500">naik 2% dari bulan lalu</div>
        </div>

        <!-- Kotak 2: Rata-rata penyelesaian (Biru) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <!-- Garis atas biru -->
            <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">Rata-rata penyelesaian</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">2,4<span class="text-2xl ml-1">jam</span></div>
            <div class="text-xs font-semibold text-blue-500">target 4 jam</div>
        </div>

        <!-- Kotak 3: Tiket berjalan (Oranye) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <!-- Garis atas oranye -->
            <div class="absolute top-0 left-0 w-full h-1 bg-orange-400"></div>
            <h3 class="text-xs text-gray-500 mb-1">Tiket berjalan</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">86</div>
            <div class="text-xs font-semibold text-orange-500">dalam batas SLA</div>
        </div>

        <!-- Kotak 4: SLA terlampaui (Merah) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <!-- Garis atas merah -->
            <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">SLA terlampaui</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">66</div>
            <div class="text-xs font-semibold text-red-500">perlu perhatian</div>
        </div>

    </div>

    <!-- Baris Tengah: Line Chart & Donut Chart -->
    <div class="flex flex-col lg:flex-row gap-6 mb-6">
        
        <!-- Kiri: Tren Kepatuhan SLA (Line Chart) -->
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-[15px] font-bold text-[#1a3668]">Tren kepatuhan SLA</h2>
            <p class="text-xs text-gray-400 mb-6">Persentase tiket selesai tepat waktu, 6 bulan terakhir</p>
            
            <div class="w-full h-64">
                <canvas id="trenSlaChart"></canvas>
            </div>
        </div>

        <!-- Kanan: Status SLA (Donut Chart) -->
        <div class="w-full lg:w-[350px] bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col shrink-0">
            <h2 class="text-[15px] font-bold text-[#1a3668]">Status SLA</h2>
            <p class="text-xs text-gray-400 mb-6">Seluruh tiket tahun berjalan</p>
            
            <div class="relative w-40 h-40 mx-auto mb-8">
                <canvas id="statusSlaChart"></canvas>
            </div>

            <div class="mt-auto space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Tercapai</div>
                    <div class="font-bold text-[#1a3668]">1088</div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Berjalan</div>
                    <div class="font-bold text-[#1a3668]">86</div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Terlampaui</div>
                    <div class="font-bold text-[#1a3668]">66</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Baris Bawah: Kepatuhan SLA per bidang (Bar Chart) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-[15px] font-bold text-[#1a3668] mb-6">Kepatuhan SLA per bidang</h2>
        <div class="w-full h-64">
            <canvas id="bidangSlaChart"></canvas>
        </div>
    </div>

</div>

<!-- SCRIPT UNTUK CHART.JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Konfigurasi umum font
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';

    // ==========================================
    // 1. TREN KEPATUHAN SLA (LINE CHART)
    // ==========================================
    const ctxTren = document.getElementById('trenSlaChart').getContext('2d');
    
    // Membuat gradien biru transparan untuk bawah garis
    let gradientTren = ctxTren.createLinearGradient(0, 0, 0, 300);
    gradientTren.addColorStop(0, 'rgba(37, 99, 235, 0.15)'); // Biru pudar
    gradientTren.addColorStop(1, 'rgba(37, 99, 235, 0)');    // Transparan

    new Chart(ctxTren, {
        type: 'line',
        data: {
            labels: ['Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt'],
            datasets: [{
                data: [88, 90, 91, 93, 94, 96], // Data persentase
                borderColor: '#2563eb', // Warna biru terang
                borderWidth: 3,
                backgroundColor: gradientTren,
                fill: true,
                pointRadius: 0, // Sembunyikan titik agar garis mulus
                pointHoverRadius: 6,
                tension: 0.3 // Melengkungkan garis
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 80, // Sumbu Y mulai dari 80% seperti di gambar
                    max: 100,
                    ticks: {
                        stepSize: 5,
                        callback: function(value) { return value + '%'; },
                        font: { size: 11 }
                    },
                    border: { display: false },
                    grid: { color: '#f3f4f6', drawTicks: false, borderDash: [5, 5] } // Garis grid putus-putus
                },
                x: {
                    ticks: { font: { size: 11 } },
                    border: { display: false },
                    grid: { display: false } // Hilangkan garis vertikal
                }
            },
            interaction: { mode: 'index', intersect: false }
        }
    });


    // ==========================================
    // 2. STATUS SLA (DONUT CHART)
    // ==========================================
    const ctxStatus = document.getElementById('statusSlaChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Tercapai', 'Berjalan', 'Terlampaui'],
            datasets: [{
                data: [1088, 86, 66],
                backgroundColor: [
                    '#10b981', // Hijau (Tercapai)
                    '#3b82f6', // Biru (Berjalan)
                    '#ef4444'  // Merah (Terlampaui)
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%', // Menentukan ketebalan donat
            plugins: {
                legend: { display: false }, // Legenda sudah kita buat manual pakai HTML
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' tiket';
                        }
                    }
                }
            }
        }
    });


    // ==========================================
    // 3. KEPATUHAN SLA PER BIDANG (BAR CHART)
    // ==========================================
    const ctxBidang = document.getElementById('bidangSlaChart').getContext('2d');
    new Chart(ctxBidang, {
        type: 'bar',
        data: {
            labels: ['Layanan', 'Infrastruktur', 'Keamanan', 'Statistik', 'Humas', 'Perangkat'],
            datasets: [{
                data: [97, 94, 98, 91, 89, 95], // Data persentase per bidang
                backgroundColor: '#2563eb', // Warna balok biru
                borderRadius: 4, // Ujung balok sedikit membulat
                borderSkipped: false,
                barPercentage: 0.7 // Mengatur ketebalan balok
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 80, // Sumbu Y mulai dari 80%
                    max: 100,
                    ticks: {
                        stepSize: 5,
                        callback: function(value) { return value + '%'; },
                        font: { size: 11 }
                    },
                    border: { display: false },
                    grid: { color: '#f3f4f6', drawTicks: false } // Garis lurus biasa (tidak putus)
                },
                x: {
                    ticks: { font: { size: 11 } },
                    border: { display: false },
                    grid: { display: false }
                }
            }
        }
    });

});
</script>
</body>
</html>
@endsection