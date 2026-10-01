@extends('layouts.app')

@section('title', 'Kelola Klaim')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Kelola Klaim</h1>

    <div class="flex gap-2 mb-4 text-sm">
        @foreach (['' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan'] as $value => $label)
            <a href="{{ route('admin.claims.index', array_filter(['status' => $value])) }}"
               class="px-3 py-1.5 rounded-full border {{ request('status', '') === $value ? 'bg-brand-600 text-white border-brand-600' : 'border-gray-200 text-gray-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100">
        @forelse ($claims as $claim)
            <a href="{{ route('admin.claims.show', $claim) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-800">{{ $claim->itemReport->title }}</p>
                    <p class="text-xs text-gray-400">Diajukan oleh {{ $claim->claimant->name }} &middot; {{ $claim->created_at->diffForHumans() }}</p>
                </div>
                <x-status-badge :status="$claim->status" />
            </a>
        @empty
            <p class="px-4 py-6 text-sm text-gray-400">Tidak ada klaim.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $claims->links() }}
    </div>
@endsection
