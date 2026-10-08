<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\User;
use App\Models\KategoriBarang;
use App\Models\LokasiKampus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'mahasiswa@findercampus.com')->first();
        if (!$user) return; // Pastikan user ada

        $kategoriElektronik = KategoriBarang::where('nama', 'Elektronik')->first();
        $kategoriDokumen = KategoriBarang::where('nama', 'Dokumen Penting')->first();
        $kategoriTas = KategoriBarang::where('nama', 'Tas & Ransel')->first();

        $lokasiPerpus = LokasiKampus::where('kode', 'PERPUS')->first();
        $lokasiParkir = LokasiKampus::where('kode', 'PARKIR')->first();
        $lokasiKantin = LokasiKampus::where('kode', 'KANTIN')->first();
        $lokasiFIK = LokasiKampus::where('kode', 'FIK')->first();

        // 5 Dummy Laporan
        $laporans = [
            [
                'kode_laporan' => 'FC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'tipe' => 'hilang',
                'nama_barang' => 'Laptop ASUS ROG',
                'deskripsi' => 'Laptop warna hitam, ada stiker logo Laravel di belakang.',
                'kategori_id' => $kategoriElektronik->id ?? 1,
                'lokasi_id' => $lokasiPerpus->id ?? 1,
                'tanggal_kejadian' => Carbon::today()->subDays(2)->toDateString(),
                'waktu_kejadian' => '14:30:00',
                'warna' => 'Hitam',
                'ciri_khusus' => 'Stiker Laravel',
                'status' => 'aktif',
            ],
            [
                'kode_laporan' => 'FC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'tipe' => 'ditemukan',
                'nama_barang' => 'KTM Budi',
                'deskripsi' => 'Ditemukan KTM atas nama Budi Mahasiswa jurusan Teknik Informatika.',
                'kategori_id' => $kategoriDokumen->id ?? 1,
                'lokasi_id' => $lokasiParkir->id ?? 1,
                'tanggal_kejadian' => Carbon::yesterday()->toDateString(),
                'waktu_kejadian' => '08:15:00',
                'warna' => 'Biru',
                'ciri_khusus' => 'Baret di bagian ujung',
                'status' => 'aktif',
            ],
            [
                'kode_laporan' => 'FC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'tipe' => 'hilang',
                'nama_barang' => 'Dompet Kulit Coklat',
                'deskripsi' => 'Dompet isi KTP, SIM, dan KTM. Tolong hubungi saya jika menemukan.',
                'kategori_id' => KategoriBarang::where('nama', 'Dompet & Uang')->first()->id ?? 1,
                'lokasi_id' => $lokasiKantin->id ?? 1,
                'tanggal_kejadian' => Carbon::today()->toDateString(),
                'waktu_kejadian' => '12:00:00',
                'warna' => 'Coklat',
                'ciri_khusus' => 'Ada gantungan kunci kecil',
                'status' => 'aktif',
            ],
            [
                'kode_laporan' => 'FC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'tipe' => 'ditemukan',
                'nama_barang' => 'Tas Ransel Eiger',
                'deskripsi' => 'Tas ransel warna abu-abu ditemukan tertinggal di lab lantai 2.',
                'kategori_id' => $kategoriTas->id ?? 1,
                'lokasi_id' => $lokasiFIK->id ?? 1,
                'tanggal_kejadian' => Carbon::today()->toDateString(),
                'waktu_kejadian' => '16:45:00',
                'warna' => 'Abu-abu',
                'ciri_khusus' => 'Gantungan resleting patah',
                'status' => 'aktif',
            ],
            [
                'kode_laporan' => 'FC-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'tipe' => 'hilang',
                'nama_barang' => 'Kunci Motor Honda',
                'deskripsi' => 'Kunci motor dengan gantungan beruang.',
                'kategori_id' => KategoriBarang::where('nama', 'Kunci')->first()->id ?? 1,
                'lokasi_id' => $lokasiParkir->id ?? 1,
                'tanggal_kejadian' => Carbon::today()->subDays(1)->toDateString(),
                'waktu_kejadian' => '09:00:00',
                'warna' => 'Hitam',
                'ciri_khusus' => 'Gantungan kunci beruang kecil',
                'status' => 'selesai', // Anggap sudah selesai
            ]
        ];

        foreach ($laporans as $data) {
            Laporan::create($data);
        }
    }
}
