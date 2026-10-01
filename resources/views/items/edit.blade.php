@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Edit Laporan</h1>

    <form method="POST" action="{{ route('items.update', $itemReport) }}" enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-4 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Judul Barang</label>
            <input type="text" name="title" value="{{ old('title', $itemReport->title) }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="description" rows="4" required
                      class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">{{ old('description', $itemReport->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <input type="text" name="category" value="{{ old('category', $itemReport->category) }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $itemReport->location) }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Kejadian</label>
            <input type="date" name="incident_date"
                   value="{{ old('incident_date', $itemReport->incident_date->format('Y-m-d')) }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                @foreach (['open' => 'Terbuka', 'claimed' => 'Diklaim', 'returned' => 'Dikembalikan', 'closed' => 'Ditutup'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $itemReport->status) === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Ganti Foto (opsional)</label>
            <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-600">
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-brand-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-brand-700">
                Simpan Perubahan
            </button>

            <form method="POST" action="{{ route('items.destroy', $itemReport) }}"
                  onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 text-sm hover:underline">Hapus Laporan</button>
            </form>
        </div>
    </form>
@endsection
