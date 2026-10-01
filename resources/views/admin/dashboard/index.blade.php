@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1 class="text-xl font-semibold mb-6">Dashboard Admin</h1>

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
                    <li class="py-2 flex justify-between">
                        <a href="{{ route('items.show', $report) }}" class="hover:text-brand-600">{{ $report->title }}</a>
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
                    <li class="py-2 flex justify-between">
                        <a href="{{ route('admin.claims.show', $claim) }}" class="hover:text-brand-600">
                            {{ $claim->itemReport->title }} &mdash; {{ $claim->claimant->name }}
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
