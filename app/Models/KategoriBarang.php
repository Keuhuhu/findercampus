<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'ikon'];

    public function laporans()
    {
        return $this->hasMany(Laporan::class);
    }
}

