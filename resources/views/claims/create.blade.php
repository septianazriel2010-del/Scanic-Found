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
            <label class="block text-sm font-medium mb-1">Bukti Kepemilikan</label>
            <textarea name="proof_details" rows="5" required
                      placeholder="Jelaskan ciri khas barang, kapan/di mana Anda kehilangannya, atau bukti lain yang menguatkan klaim Anda."
                      class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">{{ old('proof_details') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">
                Informasi ini bersifat privat &mdash; hanya Anda dan admin yang bisa melihatnya.
            </p>
        </div>

        <button type="submit"
                class="bg-brand-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-brand-700">
            Kirim Klaim
        </button>
    </form>
@endsection
