<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHandoverRequest;
use App\Http\Requests\UpdateClaimStatusRequest;
use App\Models\Claim;
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

    /** Daftar semua klaim untuk ditinjau admin, filter by status. */
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString() ?: null;

        $claims = Claim::query()
            ->with(['itemReport', 'claimant'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.claims.index', compact('claims', 'status'));
    }

    public function show(Claim $claim): View
    {
        $this->authorize('review', $claim);

        $claim->load(['itemReport.user', 'claimant', 'reviewer', 'handover']);

        return view('admin.claims.show', compact('claim'));
    }

    public function updateStatus(UpdateClaimStatusRequest $request, Claim $claim): RedirectResponse
    {
        $this->authorize('review', $claim);

        $data = $request->validated();

        if ($data['status'] === 'approved') {
            $this->claimService->approve($claim, $request->user(), $data['review_note'] ?? null);
            $message = 'Klaim disetujui. Silakan catat serah terima barang.';
        } else {
            $this->claimService->reject($claim, $request->user(), $data['review_note'] ?? null);
            $message = 'Klaim ditolak.';
        }

        return redirect()->route('admin.claims.show', $claim)->with('status', $message);
    }

    public function storeHandover(StoreHandoverRequest $request, Claim $claim): RedirectResponse
    {
        $this->authorize('review', $claim);

        try {
            $this->claimService->recordHandover(
                $claim,
                $request->user(),
                $request->validated('handed_over_at'),
                $request->validated('notes'),
            );
        } catch (RuntimeException $e) {
            return back()->withErrors(['handed_over_at' => $e->getMessage()]);
        }

        return redirect()->route('admin.claims.show', $claim)
            ->with('status', 'Serah terima barang berhasil dicatat.');
    }
}
