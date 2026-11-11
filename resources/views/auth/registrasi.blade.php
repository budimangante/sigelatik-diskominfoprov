<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi - SIGELATIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden font-sans">

    <!-- Latar Belakang Ungu di Kiri dengan Animasi Jaringan TIK -->
    <div class="absolute top-0 -left-[10%] h-full w-[55%] bg-[#4c2a95] [border-top-right-radius:50%] [border-bottom-right-radius:50%] z-0 hidden md:block overflow-hidden">
        <canvas id="networkCanvas" class="absolute inset-0 w-full h-full opacity-60 pointer-events-none" aria-hidden="true"></canvas>
    </div>

    <!-- Container Utama -->
    <div class="relative z-10 flex flex-col md:flex-row w-full max-w-5xl bg-white shadow-2xl rounded-xl overflow-hidden mx-4 my-8 min-h-[550px]">
        
        <!-- BAGIAN KIRI: Informasi & Teks -->
        <div class="w-full md:w-1/2 p-8 lg:p-12 flex flex-col justify-center border-r border-gray-100">
            <h1 id="teksAnimasi" class="text-3xl lg:text-4xl font-bold mb-4 whitespace-nowrap">
                S I G E L A <span class="text-red-600">T I K</span>
            </h1>
            
            <!-- CSS Animasi -->
            <style>
                .huruf-putar {
                    display: inline-block;
                    animation: putarHuruf 4s ease-in-out infinite;
                }
                
                @keyframes putarHuruf {
                    0%, 15% { transform: rotateY(0deg); }
                    50% { transform: rotateY(360deg); }
                    70%, 100% { transform: rotateY(360deg); }
                }
            </style>

            <!-- SCRIPT ANIMASI (Jeda Diperbesar) -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const kontainer = document.getElementById('teksAnimasi');
                    
                    const kataHitam = "SIGELA";
                    const kataMerah = "TIK";
                    
                    kontainer.innerHTML = '';
                    
                    let counterIndeks = 0;

                    function buatSpanAnimasi(huruf) {
                        const span = document.createElement('span');
                        
                        // Hapus spasi manual, cukup hurufnya saja
                        span.innerText = huruf; 
                        
                        // Tambahkan mr-2 (margin-right) agar ada jarak fisik antar huruf yang konsisten
                        span.className = 'huruf-putar mr-2 md:mr-3'; 
                        
                        span.style.animationDelay = `${counterIndeks * 0.3}s`;
                        counterIndeks++;
                        
                        return span;
                    }

                    kataHitam.split('').forEach(huruf => {
                        kontainer.appendChild(buatSpanAnimasi(huruf));
                    });

                    const spanMerah = document.createElement('span');
                    spanMerah.className = 'text-red-600';
                    
                    kataMerah.split('').forEach(huruf => {
                        spanMerah.appendChild(buatSpanAnimasi(huruf));
                    });

                    kontainer.appendChild(spanMerah);
                });
            </script>
            
            <p class="text-sm text-gray-500 leading-relaxed mb-8 max-w-md">
                <strong class="text-gray-700">(Sistem Informasi Gangguan Layanan TIK)</strong>, Laporkan apabila terjadi gangguan terhadap Layanan TIK yang ada di Lingkup Pemerintah Provinsi Kalimantan Timur
            </p>
            
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-blue-600 hover:bg-gray-200 transition">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-blue-400 hover:bg-gray-200 transition">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-red-500 hover:bg-gray-200 transition">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-blue-700 hover:bg-gray-200 transition">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Registrasi -->
        <div class="w-full md:w-1/2 p-8 lg:p-12 flex flex-col justify-center items-center">
            
            <img src="{{ asset('img/logo-diskominfo.png') }}" alt="Logo Diskominfo" class="h-16 mb-6 object-contain">
            
            <h2 class="text-xl text-gray-800 mb-8 font-normal">Registrasi Akun OPD</h2>
            
            <!-- Form Registrasi -->
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('registrasi.post') }}" method="POST" class="w-full max-w-sm space-y-4">
                @csrf
                
                <input type="text" name="name" placeholder="Nama Lengkap" required
                    class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">

                <input type="email" name="email" placeholder="Alamat Email" required
                    class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">

                <input type="password" id="password" name="password" placeholder="Kata Sandi" required
                    class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">

                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" required
                    class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">

                <div class="flex items-center px-4 pt-1 pb-2">
                    <input type="checkbox" id="showPassword" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="showPassword" class="ml-2 text-[12px] text-gray-500 cursor-pointer">
                        Tampilkan kata sandi
                    </label>
                </div>

                <button type="submit" 
                    class="w-full py-3 bg-[#ff0000] hover:bg-red-700 text-white rounded-full font-bold text-sm transition-colors mt-2">
                    DAFTAR
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-gray-500">
                <p>Sudah punya akun ? <a href="{{ route('login') }}" class="text-gray-800 font-bold hover:underline">Login</a></p>
                
                <a href="{{ route('beranda') }}" class="block mt-4 text-[#4c2a95] font-semibold text-sm hover:underline">
                    &lt; Kembali ke beranda
                </a>
            </div>
        </div>

    </div>

    <!-- Script Checkbox Tampilkan Password (dipertahankan seperti form asli) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showPassword = document.getElementById('showPassword');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            if (!showPassword || !passwordInput || !confirmPasswordInput) return;

            showPassword.addEventListener('change', function () {
                const inputType = this.checked ? 'text' : 'password';
                passwordInput.type = inputType;
                confirmPasswordInput.type = inputType;
            });
        });
    </script>
    <!-- Animasi Canvas Jaringan TIK: hanya visual, tidak mengubah proses autentikasi/registrasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('networkCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            const container = canvas.parentElement;
            let width = 0;
            let height = 0;
            let particles = [];
            let animationFrameId = null;
            let resizeTimer = null;

            function initCanvas() {
                width = container.clientWidth;
                height = container.clientHeight;
                const dpr = Math.min(window.devicePixelRatio || 1, 2);

                canvas.width = Math.max(1, Math.floor(width * dpr));
                canvas.height = Math.max(1, Math.floor(height * dpr));
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                particles = [];

                const particleCount = 40;
                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        vx: (Math.random() - 0.5) * 1.2,
                        vy: (Math.random() - 0.5) * 1.2,
                        radius: Math.random() * 1.5 + 1
                    });
                }
            }

            function drawNetwork() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];
                    p.x += p.vx;
                    p.y += p.vy;

                    if (p.x < 0 || p.x > width) p.vx *= -1;
                    if (p.y < 0 || p.y > height) p.vy *= -1;

                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.55)';
                    ctx.fill();

                    for (let j = i + 1; j < particles.length; j++) {
                        const p2 = particles[j];
                        const distance = Math.hypot(p.x - p2.x, p.y - p2.y);
                        if (distance < 120) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(p2.x, p2.y);
                            ctx.strokeStyle = `rgba(255, 255, 255, ${(1 - distance / 120) * 0.22})`;
                            ctx.lineWidth = 1;
                            ctx.stroke();
                        }
                    }
                }

                animationFrameId = window.requestAnimationFrame(drawNetwork);
            }

            function startAnimation() {
                if (window.innerWidth < 768 || animationFrameId !== null) return;
                initCanvas();
                drawNetwork();
            }

            function stopAnimation() {
                if (animationFrameId !== null) {
                    window.cancelAnimationFrame(animationFrameId);
                    animationFrameId = null;
                }
                ctx.clearRect(0, 0, width, height);
            }

            window.addEventListener('resize', function () {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(function () {
                    if (window.innerWidth >= 768) {
                        stopAnimation();
                        startAnimation();
                    } else {
                        stopAnimation();
                    }
                }, 120);
            }, { passive: true });

            startAnimation();
        });
    </script>

</body>
</html>