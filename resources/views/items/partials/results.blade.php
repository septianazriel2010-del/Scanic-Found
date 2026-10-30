@if ($itemReports->isEmpty())
    <p class="text-gray-500 text-sm">Belum ada laporan yang cocok dengan pencarian/filter ini.</p>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($itemReports as $report)
            <a href="{{ route('items.show', $report) }}"
               class="block bg-surface border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium px-2 py-1 rounded-full {{ $report->type === 'lost' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                        {{ $report->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                    </span>
                    @if ($report->status === 'open' && $report->has_pending_claim)
                        <x-status-badge status="pending" />
                    @elseif ($report->status === 'open')
                        <x-status-badge status="open" :label="$report->type === 'lost' ? 'Masih Dicari' : 'Belum Diklaim'" />
                    @else
                        <x-status-badge :status="$report->status" />
                    @endif
                </div>

                @if ($report->photo_url)
                    <img src="{{ $report->photo_url }}" alt="Foto {{ $report->title }}"
                         onerror="this.remove()"
                         class="mb-3 h-36 w-full rounded-lg border border-gray-200 object-cover">
                @endif

                <h2 class="font-semibold text-gray-800 mb-1">{{ $report->title }}</h2>
                <p class="text-sm text-gray-500 line-clamp-2 mb-2">{{ $report->description }}</p>

                <div class="text-xs text-gray-400 space-y-0.5">
                    <p>Kategori: {{ $report->category }}</p>
                    <p>Lokasi: {{ $report->location }}</p>
                    <p>Tanggal: {{ $report->incident_date->translatedFormat('d M Y') }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $itemReports->links() }}
    </div>
@endif
