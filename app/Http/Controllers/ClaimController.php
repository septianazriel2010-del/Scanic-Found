<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClaimRequest;
use App\Models\Claim;
use App\Models\ItemReport;
use App\Services\ClaimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ClaimController extends Controller
{
    public function __construct(private readonly ClaimService $claimService)
    {
    }

    /** Daftar klaim milik user yang sedang login. */
    public function index(Request $request): View
    {
        $claims = $request->user()->claims()
            ->with('itemReport')
            ->latest()
            ->paginate(10);

        return view('claims.index', compact('claims'));
    }

    public function create(ItemReport $itemReport): View|RedirectResponse
    {
        $this->authorize('create', Claim::class);

        if ($itemReport->status !== ItemReport::STATUS_OPEN) {
            return redirect()->route('items.show', $itemReport)
                ->with('status', 'Laporan ini sudah tidak bisa diklaim.');
        }

        if (auth()->id() === $itemReport->user_id) {
            return redirect()->route('items.show', $itemReport)
                ->withErrors(['proof_details' => 'Anda tidak bisa mengklaim laporan yang Anda buat sendiri.']);
        }

        return view('claims.create', compact('itemReport'));
    }

    public function store(StoreClaimRequest $request, ItemReport $itemReport): RedirectResponse
    {
        $this->authorize('create', Claim::class);

        try {
            $this->claimService->submit(
                $itemReport,
                $request->user(),
                $request->validated('claimant_full_name'),
                $request->validated('claimant_class_position'),
                $request->validated('proof_details'),
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['proof_details' => $e->getMessage()]);
        }

        return redirect()->route('claims.index')
            ->with('status', 'Klaim berhasil diajukan. Menunggu verifikasi admin.');
    }

    /**
     * Detail satu klaim, termasuk proof_details yang privat.
     * Policy memastikan hanya claimant sendiri atau admin yang bisa masuk sini.
     */
    public function show(Claim $claim): View
    {
        $this->authorize('view', $claim);

        $claim->load(['itemReport', 'claimant', 'reviewer', 'handover']);

        return view('claims.show', compact('claim'));
    }

    public function cancel(Claim $claim): RedirectResponse
    {
        $this->authorize('cancel', $claim);

        $claim->update(['status' => Claim::STATUS_CANCELLED]);

        return redirect()->route('claims.index')
            ->with('status', 'Klaim dibatalkan.');
    }
}
