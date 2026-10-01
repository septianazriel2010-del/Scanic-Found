<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SCANIC TRACE') - Lost &amp; Found Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 flex flex-col">

    <x-nav />

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-8">
        <x-alert />

        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-gray-500 flex flex-col sm:flex-row justify-between gap-2">
            <span>&copy; {{ date('Y') }} SCANIC TRACE &mdash; Sistem Lost &amp; Found Sekolah</span>
            <span>Dibangun dengan Laravel</span>
        </div>
    </footer>
</body>
</html>
