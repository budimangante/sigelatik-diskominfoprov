<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIGELATIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden font-sans">

    <!-- Latar Belakang Ungu di Kiri -->
    <div class="absolute top-0 -left-[10%] h-full w-[55%] bg-[#4c2a95] [border-top-right-radius:50%] [border-bottom-right-radius:50%] z-0 hidden md:block"></div>
    
    <!-- Container Utama -->
    <div class="relative z-10 flex flex-col md:flex-row w-full max-w-5xl bg-white shadow-2xl rounded-xl overflow-hidden mx-4 my-8 min-h-[550px]">
        
        <!-- BAGIAN KIRI: Informasi & Teks -->
        <div class="w-full md:w-1/2 p-8 lg:p-12 flex flex-col justify-center border-r border-gray-100">
            
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-[0.15em] mb-4 whitespace-nowrap">
                S I G E L A <span class="text-red-600">T I K</span>
            </h1>
            
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

        <!-- BAGIAN KANAN: Form Login -->
        <div class="w-full md:w-1/2 p-8 lg:p-12 flex flex-col justify-center items-center">
            
            <img src="{{ asset('img/logo-kaltim.png') }}" alt="Logo Diskominfo" class="h-16 mb-6 object-contain">
            
            <h2 class="text-2xl text-gray-800 mb-8 font-normal">Login ke Akun Anda</h2>
            
            <div id="alertError" class="hidden w-full max-w-sm mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded"></div>

            <form id="loginForm" onsubmit="handleFormSubmit(event)" class="w-full max-w-sm space-y-5">
                
                <div class="relative">
                    <input type="text" id="identifier" placeholder="NIP / Username / Email" required autocomplete="username"
                        class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="far fa-user"></i>
                    </div>
                </div>

                <div class="relative">
                    <input type="password" id="password" placeholder="Kata Sandi" required autocomplete="current-password"
                        class="w-full px-6 py-3 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400">
                    <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 leading-relaxed text-center px-2">
                    * ASN silakan gunakan <strong>NIP</strong>, Administrator gunakan <strong>Username</strong>, dan Masyarakat gunakan <strong>Email</strong>.
                </p>

                <button type="submit" id="submitBtn"
                    class="w-full py-3 bg-[#ff0000] hover:bg-red-700 text-white rounded-full font-bold text-sm transition-colors mt-2 disabled:opacity-50">
                    MASUK
                </button>
            </form>

            <div class="text-center mt-8 text-sm text-gray-500">
                <p class="mb-1">OPD Belum Mempunyai Akun ?</p>
                <p>Daftar <a href="{{ route('registrasi') }}" class="text-gray-800 font-bold hover:underline">Registrasi</a></p>
                <a href="/" class="block mt-6 text-[#4c2a95] font-semibold text-sm hover:underline">
                    &lt; Kembali ke beranda
                </a>
            </div>
        </div>

    </div>

    <!-- Script Logika Form -->
    <script>
        async function handleFormSubmit(event) {
            event.preventDefault();

            const identifier = document.getElementById('identifier').value.trim();
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            const alertError = document.getElementById('alertError');

            alertError.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Memverifikasi...';

            const payload = {
                identifier: identifier,
                password: password
            };

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Login gagal. Periksa kembali kredensial Anda.');
                }

                localStorage.setItem('access_token', data.access_token);
                localStorage.setItem('user_profile', JSON.stringify(data.user));

                const routes = {
                    'super_admin': '/superadmin',
                    'admin_default': '/admin',
                    'helpdesk_utama': '/dinas',
                    'helpdesk_bidang': '/bidang/',
                    'helpdesk_teknisi': '/teknisi',
                    'masyarakat': '/user'
                };

                window.location.href = routes[data.user.role] || '/dashboard';

            } catch (error) {
                alertError.innerText = error.message;
                alertError.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'MASUK';
            }
        }
    </script>
</body>
</html>