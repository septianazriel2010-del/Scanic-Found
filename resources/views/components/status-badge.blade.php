@props(['status'])

@php
    $colors = [
        'open' => 'bg-blue-50 text-blue-700 border-blue-200',
        'claimed' => 'bg-amber-50 text-amber-700 border-amber-200',
        'returned' => 'bg-green-50 text-green-700 border-green-200',
        'closed' => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'approved' => 'bg-green-50 text-green-700 border-green-200',
        'rejected' => 'bg-red-50 text-red-700 border-red-200',
        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
    ];

    $labels = [
        'open' => 'Terbuka',
        'claimed' => 'Diklaim',
        'returned' => 'Dikembalikan',
        'closed' => 'Ditutup',
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block text-xs font-medium px-2 py-1 rounded-full border ' . ($colors[$status] ?? 'bg-gray-100 text-gray-600 border-gray-200')]) }}>
    {{ $labels[$status] ?? $status }}
</span>
