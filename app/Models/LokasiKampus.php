<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKampus extends Model
{
    protected $fillable = ['nama', 'deskripsi'];

    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}

