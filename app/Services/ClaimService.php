<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\Handover;
use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ClaimService
{
    /**
     * Ajukan klaim baru atas sebuah laporan.
     * Mencegah user mengklaim laporan miliknya sendiri, dan mencegah
     * klaim dobel yang masih pending dari user yang sama.
     */
    public function submit(
        ItemReport $itemReport,
        User $claimant,
        string $fullName,
        string $classPosition,
        string $proofDetails,
    ): Claim
    {
        if ($itemReport->status !== ItemReport::STATUS_OPEN) {
            throw new RuntimeException('Laporan ini sudah tidak bisa diklaim (status: '.$itemReport->status.').');
        }

        if ($itemReport->user_id === $claimant->id) {
            throw new RuntimeException('Anda tidak bisa mengklaim laporan yang Anda buat sendiri.');
        }

        $alreadyPending = $itemReport->claims()
            ->where('claimant_id', $claimant->id)
            ->where('status', Claim::STATUS_PENDING)
            ->exists();

        if ($alreadyPending) {
            throw new RuntimeException('Anda sudah mengajukan klaim untuk laporan ini dan masih menunggu verifikasi.');
        }

        return Claim::create([
            'item_report_id' => $itemReport->id,
            'claimant_id' => $claimant->id,
            'claimant_full_name' => $fullName,
            'claimant_class_position' => $classPosition,
            'proof_details' => $proofDetails,
            'status' => Claim::STATUS_PENDING,
        ]);
    }

    /**
     * Admin menyetujui klaim. Otomatis:
     * - menandai klaim lain untuk laporan yang sama menjadi rejected,
     * - mengubah status laporan menjadi "claimed".
     */
    public function approve(Claim $claim, User $admin, ?string $note): Claim
    {
        return DB::transaction(function () use ($claim, $admin, $note) {
            $claim->update([
                'status' => Claim::STATUS_APPROVED,
                'reviewed_by' => $admin->id,
                'review_note' => $note,
                'reviewed_at' => now(),
            ]);

            $claim->itemReport()->update(['status' => ItemReport::STATUS_CLAIMED]);

            // Klaim pending lain untuk laporan yang sama otomatis ditolak.
            Claim::where('item_report_id', $claim->item_report_id)
                ->where('id', '!=', $claim->id)
                ->where('status', Claim::STATUS_PENDING)
                ->update([
                    'status' => Claim::STATUS_REJECTED,
                    'reviewed_by' => $admin->id,
                    'review_note' => 'Ditolak otomatis: klaim lain untuk laporan ini sudah disetujui.',
                    'reviewed_at' => now(),
                ]);

            return $claim->fresh();
        });
    }

    public function reject(Claim $claim, User $admin, ?string $note): Claim
    {
        $claim->update([
            'status' => Claim::STATUS_REJECTED,
            'reviewed_by' => $admin->id,
            'review_note' => $note,
            'reviewed_at' => now(),
        ]);

        return $claim->fresh();
    }

    /**
     * Catat serah terima barang untuk klaim yang sudah approved,
     * lalu tandai laporan sebagai "returned".
     */
    public function recordHandover(Claim $claim, User $staff, string $handedOverAt, ?string $notes): Handover
    {
        if (! $claim->isApproved()) {
            throw new RuntimeException('Serah terima hanya bisa dicatat untuk klaim yang sudah disetujui.');
        }

        return DB::transaction(function () use ($claim, $staff, $handedOverAt, $notes) {
            $handover = Handover::create([
                'claim_id' => $claim->id,
                'handed_over_by' => $staff->id,
                'received_by' => $claim->claimant_id,
                'notes' => $notes,
                'handed_over_at' => $handedOverAt,
            ]);

            $claim->itemReport()->update(['status' => ItemReport::STATUS_RETURNED]);

            return $handover;
        });
    }
}
