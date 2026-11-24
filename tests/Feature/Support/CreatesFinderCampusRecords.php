<?php

namespace Tests\Feature\Support;

use App\Models\KategoriBarang;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\LokasiKampus;
use App\Models\User;
use Illuminate\Support\Str;

trait CreatesFinderCampusRecords
{
    protected function createCategory(?string $name = null): KategoriBarang
    {
        $name ??= 'Kategori '.Str::random(8);

        $category = new KategoriBarang;
        $category->forceFill([
            'nama' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
        ]);
        $category->save();

        return $category;
    }

    protected function createLocation(?string $name = null): LokasiKampus
    {
        return LokasiKampus::create([
            'nama' => $name ?? 'Lokasi '.Str::random(8),
        ]);
    }

    /** @param array<string, mixed> $attributes */
    protected function createReport(?User $owner = null, array $attributes = []): Laporan
    {
        $owner ??= User::factory()->create();
        $categoryId = $attributes['kategori_id'] ?? $this->createCategory()->id;
        $locationId = $attributes['lokasi_id'] ?? $this->createLocation()->id;

        return Laporan::create(array_merge([
            'user_id' => $owner->id,
            'kode_laporan' => 'FC-'.strtoupper(Str::random(8)),
            'tipe' => 'ditemukan',
            'nama_barang' => 'Barang temuan',
            'kategori_id' => $categoryId,
            'lokasi_id' => $locationId,
            'tanggal_kejadian' => now()->toDateString(),
            'waktu_kejadian' => '12:00:00',
            'warna' => 'Hitam',
            'ciri_khusus' => 'tanda khusus',
            'deskripsi' => 'Deskripsi laporan untuk pengujian.',
            'status' => 'aktif',
        ], $attributes));
    }

    protected function createClaim(Laporan $laporan, User $claimant, string $status = 'pending'): Klaim
    {
        return Klaim::create([
            'laporan_id' => $laporan->id,
            'user_id' => $claimant->id,
            'bukti_kepemilikan' => 'Bukti kepemilikan yang cukup panjang untuk verifikasi.',
            'status' => $status,
        ]);
    }
}
