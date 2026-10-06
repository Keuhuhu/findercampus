<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    protected $fillable = ['laporan_id', 'file_path', 'is_primary'];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
