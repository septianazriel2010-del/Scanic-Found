@extends('layouts.app')

@section('title', $itemReport->title)

@section('content')
    <a href="{{ route('items.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Kembali ke daftar</a>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-4">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $itemReport->type === 'lost' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                {{ $itemReport->type === 'lost' ? 'Barang Hilang' : 'Barang Ditemukan' }}
            </span>
            <x-status-badge :status="$itemReport->status" />
        </div>

        <h1 class="text-xl font-semibold mb-2">{{ $itemReport->title }}</h1>

        @if ($itemReport->photo_path)
            <img src="{{ asset('storage/' . $itemReport->photo_path) }}" alt="{{ $itemReport->title }}"
                 class="rounded-lg mb-4 max-h-80 object-cover">
        @endif

        <p class="text-gray-600 mb-4 whitespace-pre-line">{{ $itemReport->description }}</p>

        <dl class="grid grid-cols-2 gap-3 text-sm mb-4">
            <div>
                <dt class="text-gray-400">Kategori</dt>
                <dd class="font-medium">{{ $itemReport->category }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Lokasi</dt>
                <dd class="font-medium">{{ $itemReport->location }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Tanggal Kejadian</dt>
                <dd class="font-medium">{{ $itemReport->incident_date->translatedFormat('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Dilaporkan oleh</dt>
                <dd class="font-medium">{{ $itemReport->user->name }}</dd>
            </div>
        </dl>

        <div class="flex items-center gap-3">
            @auth
                @if (auth()->id() === $itemReport->user_id)
                    <a href="{{ route('items.edit', $itemReport) }}"
                       class="text-sm bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">
                        Edit Laporan
                    </a>
                @elseif ($itemReport->status === 'open')
                    <a href="{{ route('claims.create', $itemReport) }}"
                       class="text-sm bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700">
                        Ajukan Klaim
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="text-sm text-brand-600 hover:underline">
                    Masuk untuk mengajukan klaim
                </a>
            @endauth
        </div>
    </div>

    @if ($itemReport->claims->isNotEmpty())
        <div class="mt-4 text-sm text-gray-500">
            {{ $itemReport->claims->count() }} klaim telah diajukan untuk laporan ini.
            {{-- Detail klaim (termasuk bukti kepemilikan) bersifat privat dan hanya
                 bisa dilihat oleh pengaju klaim itu sendiri atau admin. --}}
        </div>
    @endif
@endsection
