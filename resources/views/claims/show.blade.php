@extends('layouts.app')

@section('title', 'Detail Klaim')

@section('content')
    <a href="{{ route('claims.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Kembali ke klaim saya</a>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-4 max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Klaim untuk: {{ $claim->itemReport->title }}</h1>
            <x-status-badge :status="$claim->status" />
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-400 mb-1">Bukti Kepemilikan (privat)</p>
            <p class="text-gray-700 whitespace-pre-line">{{ $claim->proof_details }}</p>
        </div>

        @if ($claim->review_note)
            <div class="mb-4">
                <p class="text-sm text-gray-400 mb-1">Catatan Admin</p>
                <p class="text-gray-700">{{ $claim->review_note }}</p>
            </div>
        @endif

        @if ($claim->handover)
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                Barang telah diserahkan pada
                {{ $claim->handover->handed_over_at->translatedFormat('d M Y H:i') }}.
            </div>
        @endif

        @if ($claim->isPending())
            <form method="POST" action="{{ route('claims.cancel', $claim) }}"
                  onsubmit="return confirm('Batalkan klaim ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-red-600 text-sm hover:underline">Batalkan Klaim</button>
            </form>
        @endif
    </div>
@endsection
