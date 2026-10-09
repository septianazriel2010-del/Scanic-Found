@extends('layouts.app')

@section('title', 'Daftar Laporan')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Daftar Laporan Barang</h1>
        @auth
            <a href="{{ route('items.create') }}"
               class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-brand-700">
                + Buat Laporan
            </a>
        @endauth
    </div>

    <form method="GET" action="{{ route('items.index') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-6 grid grid-cols-1 sm:grid-cols-5 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul/deskripsi..."
               class="sm:col-span-2 rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">

        <select name="type" class="rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Jenis</option>
            <option value="lost" {{ request('type') === 'lost' ? 'selected' : '' }}>Hilang</option>
            <option value="found" {{ request('type') === 'found' ? 'selected' : '' }}>Ditemukan</option>
        </select>

        <select name="category" class="rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                    {{ $category }}
                </option>
            @endforeach
        </select>

        <select name="location" class="rounded-lg text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Lokasi</option>
            @foreach ($locations as $location)
                <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }}>
                    {{ $location }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="sm:col-span-5 justify-self-start bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900">
            Terapkan Filter
        </button>
    </form>

    @if ($itemReports->isEmpty())
        <p class="text-gray-500 text-sm">Belum ada laporan yang cocok dengan pencarian/filter ini.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($itemReports as $report)
                <a href="{{ route('items.show', $report) }}"
                   class="block bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium px-2 py-1 rounded-full {{ $report->type === 'lost' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                            {{ $report->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                        </span>
                        @if ($report->status === 'open' && $report->has_pending_claim)
                            <x-status-badge status="pending" />
                        @elseif ($report->status === 'open')
                            <x-status-badge status="open" :label="$report->type === 'lost' ? 'Masih Dicari' : 'Belum Diklaim'" />
                        @else
                            <x-status-badge :status="$report->status" />
                        @endif
                    </div>

                    @if ($report->photo_url)
                        <img src="{{ $report->photo_url }}" alt="Foto {{ $report->title }}"
                             onerror="this.remove()"
                             class="mb-3 h-36 w-full rounded-lg border border-gray-200 object-cover">
                    @endif

                    <h2 class="font-semibold text-gray-800 mb-1">{{ $report->title }}</h2>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ $report->description }}</p>

                    <div class="text-xs text-gray-400 space-y-0.5">
                        <p>Kategori: {{ $report->category }}</p>
                        <p>Lokasi: {{ $report->location }}</p>
                        <p>Tanggal: {{ $report->incident_date->translatedFormat('d M Y') }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $itemReports->links() }}
        </div>
    @endif
@endsection
