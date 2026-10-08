<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LokasiKampusSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = [
            ['nama' => 'Gedung Rektorat', 'kode' => 'REK', 'area' => 'pusat', 'deskripsi' => 'Gedung Pusat Administrasi'],
            ['nama' => 'Fakultas Ilmu Komputer', 'kode' => 'FIK', 'area' => 'utara', 'deskripsi' => 'Gedung FIK dan Lab Komputer'],
            ['nama' => 'Fakultas Teknik', 'kode' => 'FT', 'area' => 'timur', 'deskripsi' => 'Gedung Fakultas Teknik'],
            ['nama' => 'Fakultas Ekonomi dan Bisnis', 'kode' => 'FEB', 'area' => 'selatan', 'deskripsi' => 'Gedung FEB'],
            ['nama' => 'Perpustakaan Pusat', 'kode' => 'PERPUS', 'area' => 'pusat', 'deskripsi' => 'Gedung Perpustakaan Utama'],
            ['nama' => 'Masjid Kampus', 'kode' => 'MASJID', 'area' => 'pusat', 'deskripsi' => 'Masjid Raya Kampus'],
            ['nama' => 'Kantin Pusat', 'kode' => 'KANTIN', 'area' => 'pusat', 'deskripsi' => 'Kantin Utama Mahasiswa'],
            ['nama' => 'Parkiran Motor Terpadu', 'kode' => 'PARKIR', 'area' => 'barat', 'deskripsi' => 'Area parkir utama'],
            ['nama' => 'Lapangan Olahraga', 'kode' => 'GOR', 'area' => 'barat', 'deskripsi' => 'Stadion dan GOR'],
            ['nama' => 'Lainnya', 'kode' => 'LAIN', 'area' => 'pusat', 'deskripsi' => 'Lokasi tidak terdefinisi'],
        ];

        foreach ($lokasi as $lok) {
            DB::table('lokasi_kampuses')->insert([
                'nama' => $lok['nama'],
                'kode' => $lok['kode'],
                'area' => $lok['area'],
                'deskripsi' => $lok['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
