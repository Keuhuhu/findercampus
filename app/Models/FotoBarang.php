<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    protected $fillable = ['laporan_id', 'file_path', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}
