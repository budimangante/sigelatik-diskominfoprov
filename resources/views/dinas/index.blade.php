<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <script>
        const token = localStorage.getItem('access_token');
        const user = JSON.parse(localStorage.getItem('user_profile') || '{}');

        // Kunci khusus role super_admin saja
        if (!token || user.role !== 'helpdesk_utama') {
            localStorage.clear();
            window.location.href = '/login';
        }
    </script>
</head>
<body>
    iiii
</body>
</html>