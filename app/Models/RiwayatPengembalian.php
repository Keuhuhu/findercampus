<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPengembalian extends Model
{
    protected $fillable = ['klaim_id', 'dikonfirmasi_oleh', 'tanggal_pengembalian', 'metode_pengembalian', 'catatan'];

    protected $casts = [
        'tanggal_pengembalian' => 'datetime',
    ];

    public function klaim()
    {
        return $this->belongsTo(Klaim::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }
}
