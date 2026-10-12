<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.scale.grid.color = '#f3f4f6';

    // 1. Grafik Tiket Per Bulan (Line)
    new Chart(document.getElementById('tiketBulanChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                data: [45, 60, 75, 70, 90, 95, 80, 100, 115, 125, 0, 0],
                borderColor: '#3b82f6',
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3b82f6',
                pointRadius: 3,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 140, ticks: { stepSize: 35 } } } }
    });

    // 2. Grafik Peningkatan Pengguna (Area)
    new Chart(document.getElementById('penggunaChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                data: [10, 20, 30, 45, 55, 65, 75, 85, 100, 110, 0, 0],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                pointRadius: 0,
                tension: 0.4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 120, ticks: { stepSize: 30 } } } }
    });

    // 3. Grafik Kategori (Bar Hijau)
    new Chart(document.getElementById('kategoriChart'), {
        type: 'bar',
        data: {
            labels: ['Software', 'Hardware', 'Jaringan', 'Keamanan'],
            datasets: [{
                data: [55, 38, 70, 18],
                backgroundColor: '#10b981',
                barThickness: 30
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 80 } } }
    });

    // 4. Grafik Prioritas (Bar Oren)
    new Chart(document.getElementById('prioritasChart'), {
        type: 'bar',
        data: {
            labels: ['Sigap', 'Cepat', 'Nanti'],
            datasets: [{
                data: [30, 100, 55],
                backgroundColor: '#f59e0b',
                barThickness: 40
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
    });

    // 5. Grafik Status (Horizontal Bar)
    new Chart(document.getElementById('statusChart'), {
        type: 'bar',
        data: {
            labels: ['NEW', 'PROCESS', 'PENDING', 'SOLVED', 'CANCELLED'],
            datasets: [{
                data: [50, 80, 40, 400, 10],
                backgroundColor: ['#0ea5e9', '#2563eb', '#f59e0b', '#10b981', '#6b7280'],
                barThickness: 15
            }]
        },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, max: 600 } } }
    });

    // 6. Grafik Verifikasi (Doughnut)
    new Chart(document.getElementById('verifikasiChart'), {
        type: 'doughnut',
        data: {
            labels: ['Belum', 'Terverifikasi'],
            datasets: [{
                data: [15, 85],
                backgroundColor: ['#ef4444', '#10b981'],
                borderWidth: 2,
                cutout: '75%'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
});
</script>