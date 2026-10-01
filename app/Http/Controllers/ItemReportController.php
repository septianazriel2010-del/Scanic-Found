<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemReportRequest;
use App\Http\Requests\UpdateItemReportRequest;
use App\Models\ItemReport;
use App\Services\ItemReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemReportController extends Controller
{
    public function __construct(private readonly ItemReportService $itemReportService)
    {
    }

    /** Daftar laporan + search & filter, bisa diakses tanpa login. */
    public function index(Request $request): View
    {
        $itemReports = ItemReport::query()
            ->with('user')
            ->type($request->string('type')->toString() ?: null)
            ->category($request->string('category')->toString() ?: null)
            ->location($request->string('location')->toString() ?: null)
            ->search($request->string('q')->toString() ?: null)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // Dropdown filter kategori & lokasi diambil dari data yang ada,
        // supaya tidak perlu tabel master terpisah untuk MVP ini.
        $categories = ItemReport::query()->distinct()->orderBy('category')->pluck('category');
        $locations = ItemReport::query()->distinct()->orderBy('location')->pluck('location');

        return view('items.index', compact('itemReports', 'categories', 'locations'));
    }

    public function create(): View
    {
        $this->authorize('create', ItemReport::class);

        return view('items.create');
    }

    public function store(StoreItemReportRequest $request): RedirectResponse
    {
        $this->authorize('create', ItemReport::class);

        $itemReport = $this->itemReportService->create(
            $request->safe()->except('photo'),
            $request->user()->id,
            $request->file('photo'),
        );

        return redirect()->route('items.show', $itemReport)
            ->with('status', 'Laporan berhasil dibuat.');
    }

    public function show(ItemReport $itemReport): View
    {
        $itemReport->load(['user', 'claims' => function ($query) {
            // proof_details tidak di-load massal di sini untuk view publik;
            // detail klaim privat hanya diambil lewat ClaimController@show.
            $query->select('id', 'item_report_id', 'claimant_id', 'status', 'created_at');
        }]);

        return view('items.show', compact('itemReport'));
    }

    public function edit(ItemReport $itemReport): View
    {
        $this->authorize('update', $itemReport);

        return view('items.edit', compact('itemReport'));
    }

    public function update(UpdateItemReportRequest $request, ItemReport $itemReport): RedirectResponse
    {
        $this->authorize('update', $itemReport);

        $this->itemReportService->update(
            $itemReport,
            $request->safe()->except('photo'),
            $request->file('photo'),
        );

        return redirect()->route('items.show', $itemReport)
            ->with('status', 'Laporan berhasil diperbarui.');
    }

    public function destroy(ItemReport $itemReport): RedirectResponse
    {
        $this->authorize('delete', $itemReport);

        $this->itemReportService->delete($itemReport);

        return redirect()->route('items.index')
            ->with('status', 'Laporan berhasil dihapus.');
    }
}
