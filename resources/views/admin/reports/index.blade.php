@extends('layouts.app')

@section('title', 'Kelola Laporan')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Kelola Laporan</h1>

    <div class="bg-white border border-gray-200 rounded-xl divide-y divide-gray-100">
        @forelse ($itemReports as $report)
            <div class="flex items-center justify-between px-4 py-3">
                <a href="{{ route('items.show', $report) }}" class="flex min-w-0 items-center gap-3 hover:text-brand-600">
                    @if ($report->photo_url)
                        <img src="{{ $report->photo_url }}" alt="" onerror="this.remove()" class="h-12 w-12 shrink-0 rounded border border-gray-200 object-cover">
                    @endif
                    <span class="min-w-0">
                        <span class="block font-medium text-gray-800">{{ $report->title }}</span>
                        <span class="block text-xs text-gray-400">Oleh {{ $report->user->name }} &middot; {{ $report->created_at->diffForHumans() }}</span>
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    <x-status-badge :status="$report->status" />
                    @if ($report->status === 'closed')
                        <form method="POST" action="{{ route('admin.reports.reopen', $report) }}"
                              onsubmit="return confirm('Buka kembali laporan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-emerald-700 hover:underline">Buka Kembali</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reports.close', $report) }}"
                              onsubmit="return confirm('Tutup laporan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs text-red-600 hover:underline">Tutup</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-sm text-gray-400">Belum ada laporan.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $itemReports->links() }}
    </div>
@endsection
