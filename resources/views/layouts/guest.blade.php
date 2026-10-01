<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SCANIC TRACE')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <a href="{{ route('items.index') }}" class="text-2xl font-bold text-brand-600">SCANIC TRACE</a>
            <p class="text-sm text-gray-500 mt-1">Sistem Lost &amp; Found Sekolah</p>
        </div>

        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-6">
            <x-alert />
            @yield('content')
        </div>
    </div>
</body>
</html>
