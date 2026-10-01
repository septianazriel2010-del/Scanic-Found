@extends('layouts.app')

@section('title', 'Klaim Saya')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Klaim Saya</h1>

    @if ($claims->isEmpty())
        <p class="text-gray-500 text-sm">Anda belum mengajukan klaim apa pun.</p>
    @else
        <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100">
            @foreach ($claims as $claim)
                <a href="{{ route('claims.show', $claim) }}"
                   class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                    <div>
                        <p class="font-medium text-gray-800">{{ $claim->itemReport->title }}</p>
                        <p class="text-xs text-gray-400">Diajukan {{ $claim->created_at->diffForHumans() }}</p>
                    </div>
                    <x-status-badge :status="$claim->status" />
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $claims->links() }}
        </div>
    @endif
@endsection
