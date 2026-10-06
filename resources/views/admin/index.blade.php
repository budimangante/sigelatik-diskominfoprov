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
    <p>apa ini datang kah</p>
</body>
</html>