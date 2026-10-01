<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Konstanta role, dipakai di seluruh aplikasi supaya tidak salah ketik string.
    public const ROLE_STUDENT = 'student';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STAFF = 'staff';
    public const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Laporan (lost/found) yang dibuat user ini. */
    public function itemReports(): HasMany
    {
        return $this->hasMany(ItemReport::class);
    }

    /** Klaim yang diajukan user ini. */
    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, 'claimant_id');
    }

    /** Klaim yang pernah direview (di-approve/reject) user ini, khusus admin. */
    public function reviewedClaims(): HasMany
    {
        return $this->hasMany(Claim::class, 'reviewed_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    /** Admin & staff dianggap "petugas" yang boleh mencatat handover. */
    public function isStaffOrAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_STAFF, self::ROLE_ADMIN], true);
    }
}
