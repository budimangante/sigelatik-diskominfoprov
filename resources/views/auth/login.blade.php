<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIGELATIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
        
        <!-- Header Aplikasi -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 p-6 text-white text-center">
            <h1 class="text-2xl font-bold tracking-tight">SIGELATIK</h1>
            <p class="text-xs text-blue-100 mt-1">Sistem Layanan Terpadu & Penanganan Kendala</p>
        </div>

        <!-- Form Login Tunggal -->
        <form id="loginForm" onsubmit="handleFormSubmit(event)" class="p-6 space-y-4">
            
            <!-- Box Pesan Error -->
            <div id="alertError" class="hidden p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded"></div>

            <!-- Input Identitas (NIP / Username / Email) -->
            <div>
                <label for="identifier" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    NIP / Username / Email
                </label>
                <input 
                    type="text" 
                    id="identifier" 
                    placeholder="Masukkan NIP, username, atau email" 
                    required 
                    autocomplete="username"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                >
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    placeholder="••••••••" 
                    required 
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                >
            </div>

            <p class="text-[11px] text-slate-500 leading-relaxed">
                * ASN silakan gunakan <strong>NIP</strong>, Administrator gunakan <strong>Username</strong>, dan Masyarakat gunakan <strong>Email</strong>.
            </p>

            <!-- Tombol Masuk Tunggal -->
            <button 
                type="submit" 
                id="submitBtn"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow-md hover:shadow-lg disabled:opacity-50">
                Masuk ke Akun
            </button>
        </form>

    </div>

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
                submitBtn.innerText = 'Masuk ke Akun';
            }
        }
    </script>
</body>
</html>