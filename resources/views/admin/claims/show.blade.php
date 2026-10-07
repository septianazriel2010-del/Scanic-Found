@extends('layouts.app')

@section('title', 'Detail Klaim')

@section('content')
    <a href="{{ route('admin.claims.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Kembali</a>

    <div class="bg-white border border-gray-200 rounded-xl p-6 mt-4 max-w-2xl space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold">{{ $claim->itemReport->title }}</h1>
            <x-status-badge :status="$claim->status" />
        </div>

        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <dt class="text-gray-400">Pengaju Klaim</dt>
                <dd class="font-medium">
                    {{ $claim->claimant_full_name ?: $claim->claimant->name }} ({{ $claim->claimant->email }})
                    <span class="block text-brand-700">Role akun: {{ ucfirst($claim->claimant->role) }}</span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Kelas / Jabatan</dt>
                <dd class="font-medium">{{ $claim->claimant_class_position ?: 'Tidak tersedia (klaim lama)' }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Pelapor Barang</dt>
                <dd class="font-medium">{{ $claim->itemReport->user->name }}</dd>
            </div>
        </dl>

        <div>
            <p class="text-sm text-gray-400 mb-1">Deskripsi Detail Barang</p>
            <p class="text-gray-700 whitespace-pre-line">{{ $claim->proof_details }}</p>
        </div>

        @if ($claim->isPending())
            <form method="POST" action="{{ route('admin.claims.update-status', $claim) }}" class="space-y-3 border-t border-gray-100 pt-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                    <textarea name="review_note" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="status" value="approved"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                        Setujui Klaim
                    </button>
                    <button type="submit" name="status" value="rejected"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                        Tolak Klaim
                    </button>
                </div>
            </form>
        @endif

        @if ($claim->isApproved() && ! $claim->handover)
            <form method="POST" action="{{ route('admin.claims.handover', $claim) }}" class="space-y-3 border-t border-gray-100 pt-4">
                @csrf
                <h2 class="font-medium">Catat Serah Terima</h2>

                <div>
                    <label class="block text-sm font-medium mb-1">Waktu Serah Terima</label>
                    <input type="datetime-local" name="handed_over_at" required
                           class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                </div>

                <button type="submit" class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-brand-700">
                    Simpan Serah Terima
                </button>
            </form>
        @endif

        @if ($claim->handover)
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                Barang diserahkan pada {{ $claim->handover->handed_over_at->translatedFormat('d M Y H:i') }}
                oleh {{ $claim->handover->handedOverBy->name }}.
            </div>
        @endif
    </div>
@endsection
