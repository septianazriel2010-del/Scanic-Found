<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ItemReport extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_LOST = 'lost';
    public const TYPE_FOUND = 'found';

    public const STATUS_OPEN = 'open';
    public const STATUS_CLAIMED = 'claimed';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CLOSED = 'closed';

    public const CATEGORIES = ['Elektronik', 'Dompet', 'Alat Tulis', 'Pakaian', 'Kartu Pelajar', 'Buku'];

    public const LOCATION_GROUPS = [
        'Administrasi & Pimpinan' => [
            'Ruang Kepala Sekolah',
            'Ruang Guru',
            'Ruang Tata Usaha (TU)',
            'Ruang Wakil Kepala Sekolah',
            'Ruang Bimbingan Konseling (BK)',
            'Ruang Bendahara',
        ],
        'Belajar & Praktik' => [
            'Kelas X PPLG 1',
            'Kelas X PPLG 2',
            'Kelas X PPLG 3',
            'Kelas XI PPLG 1',
            'Kelas XI PPLG 2',
            'Kelas XI PPLG 3',
            'Kelas XII PPLG 1',
            'Kelas XII PPLG 2',
            'Kelas XII PPLG 3',
            'Perpustakaan',
            'Ruang Aula',
        ],
        'Fasilitas Pendukung' => [
            'Ruang UKS',
            'Ruang OSIS',
            'Masjid / Mushalla',
            'Kantin',
            'Toilet / WC',
        ],
        'Area Luar & Olahraga' => [
            'Lapangan',
            'Halaman Sekolah',
            'Area Parkir',
            'Taman',
            'Pos Satpam',
        ],
    ];

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

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        $diskName = config('filesystems.default');
        $disk = Storage::disk($diskName);

        if ($diskName === 'supabase') {
            return $disk->url($this->photo_path);
        }

        if ($disk->exists($this->photo_path)) {
            return $disk->url($this->photo_path);
        }

        $publicDisk = Storage::disk('public');

        return $diskName !== 'public' && $publicDisk->exists($this->photo_path)
            ? $publicDisk->url($this->photo_path)
            : null;
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
