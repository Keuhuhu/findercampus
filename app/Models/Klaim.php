<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    protected $fillable = ['laporan_id', 'user_id', 'bukti_kepemilikan', 'foto_bukti', 'status'];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class);
    }

    public function riwayatPengembalian()
    {
        return $this->hasOne(RiwayatPengembalian::class);
    }
}

