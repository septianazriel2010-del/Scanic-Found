<header class="bg-white border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('items.index') }}" class="font-bold text-lg text-brand-600">
            SCANIC TRACE
        </a>

        <nav class="flex items-center gap-4 text-sm">
            <a href="{{ route('items.index') }}" class="text-gray-600 hover:text-brand-600">Daftar Laporan</a>

            @auth
                <a href="{{ route('items.create') }}" class="text-gray-600 hover:text-brand-600">Buat Laporan</a>
                <a href="{{ route('claims.index') }}" class="text-gray-600 hover:text-brand-600">Klaim Saya</a>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-brand-600">Admin</a>
                @endif

                <span class="text-gray-300">|</span>
                <span class="text-gray-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-700">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600">Masuk</a>
                <a href="{{ route('register') }}"
                   class="bg-brand-600 text-white px-3 py-1.5 rounded-lg hover:bg-brand-700">
                    Daftar
                </a>
            @endauth
        </nav>
    </div>
</header>
