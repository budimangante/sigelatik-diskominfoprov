<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SIGELATIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8f9fa] flex h-screen overflow-hidden text-gray-700">

    @include('superadmin.b-sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-y-auto">
        @include('superadmin.b-nav')
        
        <main class="p-6 flex-1">
            @yield('content')
        </main>

        @include('superadmin.b-footer')
    </div>

    @include('superadmin.b-js')
</body>
</html>