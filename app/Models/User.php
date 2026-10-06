<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nim_nip',
        'fakultas',
        'email',
        'whatsapp',
        'whatsapp_visible',
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
            'password'          => 'hashed',
            'whatsapp_visible'  => 'boolean',
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

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
