@extends('components.b-master')

@section('title', 'Dasbor Pemantauan SLA')
@section('subtitle', 'Gambaran kepatuhan layanan TIK seluruh perangkat daerah')

@section('content')
<!-- Library Chart.js -->
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
        <p class="text-gray-500 text-sm">Gambaran kepatuhan layanan TIK seluruh perangkat daerah. Akses hanya-baca.</p>
    </div>

    <!-- 4 Kotak Ringkasan Atas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Kotak 1: Kepatuhan SLA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-green-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">Kepatuhan SLA</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">{{ $pemprovData['kepatuhan_sla'] }}%</div>
            <div class="text-xs font-semibold text-green-500">Kinerja operasional helpdesk</div>
        </div>

        <!-- Kotak 2: Rata-rata penyelesaian -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-blue-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">Rata-rata penyelesaian</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">
                {{ $pemprovData['avg_resolution_hours'] }}<span class="text-2xl ml-1">jam</span>
            </div>
            <div class="text-xs font-semibold text-blue-500">Target resolusi 4 jam</div>
        </div>

        <!-- Kotak 3: Tiket berjalan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-orange-400"></div>
            <h3 class="text-xs text-gray-500 mb-1">Tiket berjalan</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">{{ $pemprovData['berjalan'] }}</div>
            <div class="text-xs font-semibold text-orange-500">Dalam batas SLA</div>
        </div>

        <!-- Kotak 4: SLA terlampaui -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>
            <h3 class="text-xs text-gray-500 mb-1">SLA terlampaui</h3>
            <div class="text-4xl font-bold text-[#1a3668] mb-1">{{ $pemprovData['terlampaui'] }}</div>
            <div class="text-xs font-semibold text-red-500">Perlu perhatian teknisi</div>
        </div>
    </div>

    <!-- Baris Tengah: Line Chart & Donut Chart -->
    <div class="flex flex-col lg:flex-row gap-6 mb-6">
        <!-- Line Chart -->
        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-[15px] font-bold text-[#1a3668]">Tren kepatuhan SLA</h2>
            <p class="text-xs text-gray-400 mb-6">Persentase tiket selesai tepat waktu, 6 bulan terakhir</p>
            <div class="w-full h-64">
                <canvas id="trenSlaChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="w-full lg:w-[350px] bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col shrink-0">
            <h2 class="text-[15px] font-bold text-[#1a3668]">Status SLA</h2>
            <p class="text-xs text-gray-400 mb-6">Seluruh tiket tahun berjalan</p>
            <div class="relative w-40 h-40 mx-auto mb-8">
                <canvas id="statusSlaChart"></canvas>
            </div>
            <div class="mt-auto space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Tercapai
                    </div>
                    <div class="font-bold text-[#1a3668]">{{ $pemprovData['tercapai'] }}</div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Berjalan
                    </div>
                    <div class="font-bold text-[#1a3668]">{{ $pemprovData['berjalan'] }}</div>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2 text-gray-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Terlampaui
                    </div>
                    <div class="font-bold text-[#1a3668]">{{ $pemprovData['terlampaui'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baris Bawah: Bar Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-[15px] font-bold text-[#1a3668] mb-6">Kepatuhan SLA per bidang</h2>
        <div class="w-full h-64">
            <canvas id="bidangSlaChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';

    const trendConfig  = {!! json_encode($pemprovData['trendChart']) !!};
    const donutConfig  = {!! json_encode($pemprovData['donutChart']) !!};
    const bidangConfig = {!! json_encode($pemprovData['bidangChart']) !!};

    // 1. Line Chart
    const ctxTren = document.getElementById('trenSlaChart').getContext('2d');
    let gradientTren = ctxTren.createLinearGradient(0, 0, 0, 300);
    gradientTren.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
    gradientTren.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctxTren, {
        type: 'line',
        data: {
            labels: trendConfig.labels,
            datasets: [{
                data: trendConfig.data,
                borderColor: '#2563eb',
                borderWidth: 3,
                backgroundColor: gradientTren,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        callback: (v) => v + '%',
                        font: { size: 11 }
                    },
                    border: { display: false },
                    grid: { color: '#f3f4f6', drawTicks: false, borderDash: [5, 5] }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    border: { display: false },
                    grid: { display: false }
                }
            }
        }
    });

    // 2. Donut Chart
    const ctxStatus = document.getElementById('statusSlaChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: donutConfig.labels,
            datasets: [{
                data: donutConfig.data,
                backgroundColor: ['#10b981', '#3b82f6', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    // 3. Bar Chart
    const ctxBidang = document.getElementById('bidangSlaChart').getContext('2d');
    new Chart(ctxBidang, {
        type: 'bar',
        data: {
            labels: bidangConfig.labels,
            datasets: [{
                data: bidangConfig.data,
                backgroundColor: '#2563eb',
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` Kepatuhan: ${ctx.parsed.y}%`
                    }
                }
            },
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20,
                        callback: (v) => v + '%',
                        font: { size: 11 }
                    },
                    border: { display: false },
                    grid: { color: '#f3f4f6', drawTicks: false }
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
@endsection