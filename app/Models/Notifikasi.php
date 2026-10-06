<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'data',
        'dibaca_at',
    ];

    protected $casts = [
        'data'      => 'array',
        'dibaca_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isDibaca(): bool
    {
        return !is_null($this->dibaca_at);
    }
}
