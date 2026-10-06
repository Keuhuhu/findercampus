<?php

namespace App\Services;

use App\Models\Laporan;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SmartMatchingService
{
    const BOBOT = [
        'kategori' => 0.25,
        'lokasi'   => 0.25,
        'waktu'    => 0.20,
        'ciri'     => 0.20,
        'warna'    => 0.10,
    ];

    /**
     * Hitung tingkat kecocokan antar 2 laporan (1 hilang, 1 ditemukan)
     */
    public function hitungKecocokan(Laporan $hilang, Laporan $ditemukan): array
    {
        $utilitas = [
            'kategori' => $this->utilitasKategori($hilang, $ditemukan),
            'lokasi'   => $this->utilitasLokasi($hilang, $ditemukan),
            'waktu'    => $this->utilitasWaktu($hilang, $ditemukan),
            'ciri'     => $this->utilitasCiri($hilang, $ditemukan),
            'warna'    => $this->utilitasWarna($hilang, $ditemukan),
        ];

        $skorTotal = 0;
        foreach ($utilitas as $kriteria => $nilai) {
            $skorTotal += $nilai * self::BOBOT[$kriteria];
        }

        return [
            'skor_total' => round($skorTotal * 100), // Persentase (0-100)
            'utilitas'   => $utilitas, // Nilai mentah (0.0 - 1.0)
        ];
    }

    /**
     * Cari semua laporan 'ditemukan' yang cocok dengan 1 laporan 'hilang'
     */
    public function cariKecocokanUntuk(Laporan $hilang): Collection
    {
        // Ambil semua laporan 'ditemukan' yang masih aktif
        $semuaDitemukan = Laporan::with(['fotos', 'lokasi', 'kategori'])
            ->where('tipe', 'ditemukan')
            ->where('status', 'aktif')
            ->get();

        $hasil = $semuaDitemukan->map(function ($ditemukan) use ($hilang) {
            $analisis = $this->hitungKecocokan($hilang, $ditemukan);
            $ditemukan->smart_score = $analisis['skor_total'];
            $ditemukan->smart_detail = $analisis['utilitas'];
            return $ditemukan;
        });

        // Urutkan berdasarkan skor tertinggi, filter skor > 20%
        return $hasil->filter(fn($item) => $item->smart_score > 20)
                     ->sortByDesc('smart_score')
                     ->values();
    }

    private function utilitasKategori(Laporan $hilang, Laporan $ditemukan): float
    {
        return $hilang->kategori_id === $ditemukan->kategori_id ? 1.0 : 0.0;
    }

    private function utilitasLokasi(Laporan $hilang, Laporan $ditemukan): float
    {
        return $hilang->lokasi_id === $ditemukan->lokasi_id ? 1.0 : 0.0;
    }

    private function utilitasWaktu(Laporan $hilang, Laporan $ditemukan): float
    {
        $tglHilang = Carbon::parse($hilang->tanggal_kejadian);
        $tglDitemukan = Carbon::parse($ditemukan->tanggal_kejadian);
        
        // Asumsi: barang ditemukan setelah atau di hari yang sama saat hilang
        // Jika ditemukan sebelum hilang, logikanya tidak cocok
        if ($tglDitemukan->isBefore($tglHilang)) {
            return 0.0;
        }

        $selisihHari = $tglHilang->diffInDays($tglDitemukan);

        if ($selisihHari <= 1) return 1.00;
        if ($selisihHari <= 3) return 0.75;
        if ($selisihHari <= 7) return 0.50;
        if ($selisihHari <= 14) return 0.25;
        
        return 0.00;
    }

    private function utilitasCiri(Laporan $hilang, Laporan $ditemukan): float
    {
        // Pencocokan keyword sederhana dari ciri_khusus dan nama_barang
        $teksHilang = strtolower($hilang->nama_barang . ' ' . $hilang->ciri_khusus);
        $teksDitemukan = strtolower($ditemukan->nama_barang . ' ' . $ditemukan->ciri_khusus);

        // Hapus tanda baca dan split jadi array kata
        $kataHilang = array_filter(str_word_count($teksHilang, 1), fn($w) => strlen($w) > 3);
        $kataDitemukan = array_filter(str_word_count($teksDitemukan, 1), fn($w) => strlen($w) > 3);

        if (empty($kataHilang) || empty($kataDitemukan)) {
            return 0.0; // Tidak bisa dicocokkan
        }

        $cocok = array_intersect($kataHilang, $kataDitemukan);
        $persentase = count($cocok) / count($kataHilang);

        if ($persentase >= 0.8) return 1.00;
        if ($persentase >= 0.6) return 0.75;
        if ($persentase >= 0.4) return 0.50;
        if ($persentase >= 0.2) return 0.25;

        return 0.00;
    }

    private function utilitasWarna(Laporan $hilang, Laporan $ditemukan): float
    {
        return strtolower($hilang->warna) === strtolower($ditemukan->warna) ? 1.0 : 0.0;
    }
}

