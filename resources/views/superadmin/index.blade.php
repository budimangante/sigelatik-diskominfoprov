<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin</title>

    <script>
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user_profile') || '{}');

        // Kunci khusus role super_admin saja
        if (!token || user.role !== 'super_admin') {
            localStorage.clear();
            window.location.href = '/login';
        }
    </script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold">Halo Super Admin!</h1>
    <p>Selamat datang di panel kendali utama SIGELATIK.</p>
</body>
</html>