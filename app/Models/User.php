<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmail, Notifiable;

    protected $fillable = [
        'name',
        'nim_nip',
        'fakultas',
        'email',
        'whatsapp',
        'whatsapp_visible',
        'email_verification_required',
        'password',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_required' => 'boolean',
            'is_verified' => 'boolean',
            'password' => 'hashed',
            'whatsapp_visible' => 'boolean',
        ];
    }

    // Relationships
    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }

    public function klaims()
    {
        return $this->hasMany(Klaim::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function campusEmailVerifications(): HasMany
    {
        return $this->hasMany(CampusEmailVerification::class);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
