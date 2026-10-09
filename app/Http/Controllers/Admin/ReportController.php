<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /** Kelola semua laporan (termasuk milik user lain) dari sisi admin. */
    public function index(Request $request): View
    {
        $itemReports = ItemReport::query()
            ->with('user')
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.reports.index', compact('itemReports'));
    }

    /** Admin bisa menutup paksa laporan (misal salah input / sudah tidak relevan). */
    public function close(ItemReport $itemReport): RedirectResponse
    {
        $itemReport->update(['status' => ItemReport::STATUS_CLOSED]);

        return back()->with('status', 'Laporan ditutup.');
    }

    public function reopen(ItemReport $itemReport): RedirectResponse
    {
        $itemReport->update(['status' => ItemReport::STATUS_OPEN]);

        return back()->with('status', 'Laporan dibuka kembali.');
    }
}
