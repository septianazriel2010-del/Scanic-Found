@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Dashboard Admin</h1>

    <nav class="flex flex-wrap gap-2 mb-6" aria-label="Navigasi admin">
        <a href="{{ route('admin.reports.index') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm hover:border-gray-500">Kelola Laporan</a>
        <a href="{{ route('admin.claims.index') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm hover:border-gray-500">Kelola Klaim</a>
        <a href="{{ route('admin.users.index') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm hover:border-gray-500">Kelola Pengguna</a>
    </nav>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @foreach ([
            'Total Laporan' => $stats['total_reports'],
            'Laporan Terbuka' => $stats['open_reports'],
            'Barang Hilang' => $stats['lost_reports'],
            'Barang Ditemukan' => $stats['found_reports'],
            'Klaim Menunggu' => $stats['pending_claims'],
            'Sudah Dikembalikan' => $stats['returned_reports'],
            'Total Pengguna' => $stats['total_users'],
        ] as $label => $value)
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <p class="text-2xl font-semibold text-brand-600">{{ $value }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <h2 class="font-medium mb-3">Laporan Terbaru</h2>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($recentReports as $report)
                    <li class="py-2 flex items-center justify-between gap-3">
                        <a href="{{ route('items.show', $report) }}" class="flex min-w-0 items-center gap-3 hover:text-brand-600">
                            @if ($report->photo_url)
                                <img src="{{ $report->photo_url }}" alt="" class="h-10 w-10 shrink-0 rounded border border-gray-200 object-cover">
                            @endif
                            <span class="truncate">{{ $report->title }}</span>
                        </a>
                        <x-status-badge :status="$report->status" />
                    </li>
                @empty
                    <li class="py-2 text-gray-400">Belum ada laporan.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <h2 class="font-medium mb-3">Klaim Menunggu Verifikasi</h2>
            <ul class="divide-y divide-gray-100 text-sm">
                @forelse ($pendingClaims as $claim)
                    <li class="py-2 flex items-center justify-between gap-3">
                        <a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-brand-600">
                            <span class="block">{{ $claim->itemReport->title }} &mdash; {{ $claim->claimant_full_name ?: $claim->claimant->name }}</span>
                            <span class="block text-xs text-gray-500">
                                {{ ucfirst($claim->claimant->role) }}
                                @if ($claim->claimant_class_position)
                                    &middot; {{ $claim->claimant_class_position }}
                                @endif
                            </span>
                        </a>
                        <x-status-badge :status="$claim->status" />
                    </li>
                @empty
                    <li class="py-2 text-gray-400">Tidak ada klaim yang menunggu.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
