<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    /**
     * Hanya pemilik klaim (claimant) atau admin yang boleh melihat detail
     * klaim -- termasuk kolom proof_details yang privat.
     * Ini kunci utama pencegahan IDOR pada data klaim.
     */
    public function view(User $user, Claim $claim): bool
    {
        return $user->id === $claim->claimant_id || $user->isAdmin();
    }

    /** Semua user login boleh mengajukan klaim atas laporan orang lain. */
    public function create(User $user): bool
    {
        return true;
    }

    /** Hanya pemilik klaim yang boleh membatalkan klaimnya sendiri. */
    public function cancel(User $user, Claim $claim): bool
    {
        return $user->id === $claim->claimant_id && $claim->isPending();
    }

    /** Hanya admin yang boleh approve/reject klaim. */
    public function review(User $user, Claim $claim): bool
    {
        return $user->isAdmin();
    }
}
