<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'item_report_id',
        'claimant_id',
        'claimant_full_name',
        'claimant_class_position',
        'proof_details',
        'status',
        'reviewed_by',
        'review_note',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    /** Laporan barang yang diklaim. */
    public function itemReport(): BelongsTo
    {
        return $this->belongsTo(ItemReport::class);
    }

    /** User yang mengajukan klaim ini. */
    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    /** Admin yang memverifikasi (approve/reject) klaim ini. */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Catatan serah terima, dibuat setelah klaim di-approve. */
    public function handover(): HasOne
    {
        return $this->hasOne(Handover::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
