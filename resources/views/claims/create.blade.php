@extends('layouts.app')

@section('title', 'Ajukan Klaim')

@section('content')
    <h1 class="text-xl font-semibold mb-2">Ajukan Klaim</h1>
    <p class="text-sm text-gray-500 mb-6">
        Untuk laporan: <span class="font-medium text-gray-700">{{ $itemReport->title }}</span>
    </p>

    <form method="POST" action="{{ route('claims.store', $itemReport) }}"
          class="bg-white border border-gray-200 rounded-xl p-6 space-y-4 max-w-2xl">
        @csrf

        <div>
            <label for="claimant_full_name" class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input id="claimant_full_name" type="text" name="claimant_full_name"
                   value="{{ old('claimant_full_name', auth()->user()->name) }}" required maxlength="255"
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="claimant_class_position" class="block text-sm font-medium mb-1">Kelas / Jabatan</label>
            <input id="claimant_class_position" type="text" name="claimant_class_position"
                   value="{{ old('claimant_class_position') }}" required maxlength="120"
                   placeholder="Contoh: XI PPLG 2 atau Guru Matematika"
                   class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="proof_details" class="block text-sm font-medium mb-1">Deskripsi Detail Barang</label>
            <textarea id="proof_details" name="proof_details" rows="5" required minlength="20" maxlength="2000"
                      placeholder="Jelaskan warna, merek, ciri khusus, isi, atau tanda unik barang untuk dicocokkan dengan laporan."
                      class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">{{ old('proof_details') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">
                Detail ini bersifat privat dan hanya dapat dilihat oleh Anda dan admin.
            </p>
        </div>

        <button type="submit"
                class="bg-brand-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-brand-700">
            Kirim Klaim
        </button>
    </form>
@endsection
