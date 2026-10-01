<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Handover extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'handed_over_by',
        'received_by',
        'notes',
        'handed_over_at',
    ];

    protected function casts(): array
    {
        return [
            'handed_over_at' => 'datetime',
        ];
    }

    /** Klaim yang diselesaikan lewat serah terima ini. */
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    /** Petugas (admin/staf) yang menyerahkan barang. */
    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    /** User (claimant) yang menerima barang. */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
