<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Elektronik', 'icon' => 'fas fa-laptop', 'deskripsi' => 'Laptop, HP, Charger, Flashdisk, dll'],
            ['nama' => 'Dokumen Penting', 'icon' => 'fas fa-id-card', 'deskripsi' => 'KTM, KTP, SIM, STNK, Buku Tabungan'],
            ['nama' => 'Pakaian & Aksesoris', 'icon' => 'fas fa-tshirt', 'deskripsi' => 'Jaket, Jas Almamater, Topi, Kacamata'],
            ['nama' => 'Buku & Alat Tulis', 'icon' => 'fas fa-book', 'deskripsi' => 'Buku Cetak, Buku Catatan, Kotak Pensil'],
            ['nama' => 'Dompet & Uang', 'icon' => 'fas fa-wallet', 'deskripsi' => 'Dompet, Uang Tunai, Kartu ATM'],
            ['nama' => 'Kunci', 'icon' => 'fas fa-key', 'deskripsi' => 'Kunci Motor, Kunci Kos, Kunci Loker'],
            ['nama' => 'Tas & Ransel', 'icon' => 'fas fa-briefcase', 'deskripsi' => 'Tas Punggung, Tas Laptop, Tote Bag'],
            ['nama' => 'Lain-lain', 'icon' => 'fas fa-box', 'deskripsi' => 'Barang yang tidak masuk kategori lain'],
        ];

        foreach ($kategori as $kat) {
            DB::table('kategori_barangs')->insert([
                'nama' => $kat['nama'],
                'slug' => Str::slug($kat['nama']),
                'icon' => $kat['icon'],
                'deskripsi' => $kat['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
