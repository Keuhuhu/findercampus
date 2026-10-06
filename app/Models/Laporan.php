<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = [
        'user_id', 'kode_laporan', 'tipe', 'nama_barang', 'kategori_id',
        'lokasi_id', 'tanggal_kejadian', 'warna', 'ciri_khusus',
        'deskripsi', 'qr_code_path', 'status'
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKampus::class);
    }

    public function fotos()
    {
        return $this->hasMany(FotoBarang::class);
    }

    public function klaims()
    {
        return $this->hasMany(Klaim::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function fotoUtama()
    {
        return $this->fotos->where('is_primary', true)->first() ?? $this->fotos->first();
    }
}
