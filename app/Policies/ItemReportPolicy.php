<?php

namespace App\Policies;

use App\Models\ItemReport;
use App\Models\User;

class ItemReportPolicy
{
    /** Semua user (termasuk tamu) boleh melihat daftar & detail laporan. */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ItemReport $itemReport): bool
    {
        return true;
    }

    /** Semua user yang sudah login boleh membuat laporan. */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya pemilik laporan atau admin yang boleh mengubah.
     * Ini mencegah IDOR: user A tidak bisa edit laporan milik user B.
     */
    public function update(User $user, ItemReport $itemReport): bool
    {
        return $user->id === $itemReport->user_id || $user->isAdmin();
    }

    public function delete(User $user, ItemReport $itemReport): bool
    {
        return $user->id === $itemReport->user_id || $user->isAdmin();
    }
}
