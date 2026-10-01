<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ItemReport extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_LOST = 'lost';
    public const TYPE_FOUND = 'found';

    public const STATUS_OPEN = 'open';
    public const STATUS_CLAIMED = 'claimed';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'category',
        'location',
        'incident_date',
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
        ];
    }

    /** Pelapor (siswa/guru/staf/admin) yang membuat laporan ini. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Semua klaim yang pernah diajukan untuk laporan ini. */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    /** Klaim yang statusnya sudah disetujui (kalau ada). */
    public function approvedClaim(): HasMany
    {
        return $this->claims()->where('status', Claim::STATUS_APPROVED);
    }

    // ----- Query Scopes untuk search & filter -----

    public function scopeType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type', $type) : $query;
    }

    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return $category ? $query->where('category', $category) : $query;
    }

    public function scopeLocation(Builder $query, ?string $location): Builder
    {
        return $location ? $query->where('location', $location) : $query;
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
}
