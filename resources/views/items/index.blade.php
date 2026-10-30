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

    <form id="item-report-filters" method="GET" action="{{ url()->current() }}"
          class="bg-surface border border-gray-200 rounded-xl p-4 mb-6 grid grid-cols-1 sm:grid-cols-5 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul/deskripsi..."
               aria-label="Cari judul atau deskripsi"
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

    </form>

    <p id="item-report-search-status" class="text-sm text-gray-500 mb-3" role="status" aria-live="polite"></p>
    <div id="item-report-results" aria-busy="false">
        @include('items.partials.results')
    </div>
@endsection
