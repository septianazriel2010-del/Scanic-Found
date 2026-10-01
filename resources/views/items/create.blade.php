@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Buat Laporan Barang</h1>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-4 max-w-2xl">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Jenis Laporan</label>
            <select name="type" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="lost" {{ old('type') === 'lost' ? 'selected' : '' }}>Barang Hilang</option>
                <option value="found" {{ old('type') === 'found' ? 'selected' : '' }}>Barang Ditemukan</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Judul Barang</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="4" required
                      class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="Elektronik, Dompet, dll"
                       required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Kantin, Lab Komputer, dll"
                       required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Kejadian</label>
            <input type="date" name="incident_date" value="{{ old('incident_date') }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Foto (opsional)</label>
            <input type="file" name="photo" accept="image/*"
                   class="w-full text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-1">Format JPG/PNG/WEBP, maksimal 2MB.</p>
        </div>

        <button type="submit"
                class="bg-brand-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-brand-700">
            Simpan Laporan
        </button>
    </form>
@endsection
