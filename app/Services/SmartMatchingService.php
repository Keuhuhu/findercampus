<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\User;
use App\Notifications\MatchDitemukanNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SmartMatchingService
{
    public const AMBANG_SKOR = 20;

    private const BOBOT = [
        'kategori' => 0.25,
        'lokasi' => 0.25,
        'waktu' => 0.20,
        'ciri' => 0.20,
        'warna' => 0.10,
    ];

    /**
     * @return array{skor_total: int, utilitas: array<string, float>}
     */
    public function hitungKecocokan(Laporan $hilang, Laporan $ditemukan): array
    {
        $utilitas = [
            'kategori' => $this->utilitasKategori($hilang, $ditemukan),
            'lokasi' => $this->utilitasLokasi($hilang, $ditemukan),
            'waktu' => $this->utilitasWaktu($hilang, $ditemukan),
            'ciri' => $this->utilitasCiri($hilang, $ditemukan),
            'warna' => $this->utilitasWarna($hilang, $ditemukan),
        ];

        $skorTotal = 0.0;

        foreach ($utilitas as $kriteria => $nilai) {
            $skorTotal += $nilai * self::BOBOT[$kriteria];
        }

        return [
            'skor_total' => (int) round($skorTotal * 100),
            'utilitas' => $utilitas,
        ];
    }

    public function cariKecocokanUntuk(Laporan $laporan): Collection
    {
        $tipePembanding = $laporan->tipe === 'hilang' ? 'ditemukan' : 'hilang';

        return Laporan::with(['fotos', 'lokasi', 'kategori'])
            ->where('tipe', $tipePembanding)
            ->where('status', 'aktif')
            ->get()
            ->map(function (Laporan $pembanding) use ($laporan): Laporan {
                $hilang = $laporan->tipe === 'hilang' ? $laporan : $pembanding;
                $ditemukan = $laporan->tipe === 'ditemukan' ? $laporan : $pembanding;
                $analisis = $this->hitungKecocokan($hilang, $ditemukan);

                $pembanding->smart_score = $analisis['skor_total'];
                $pembanding->smart_detail = $analisis['utilitas'];

                return $pembanding;
            })
            ->filter(fn (Laporan $match): bool => $match->smart_score > self::AMBANG_SKOR)
            ->sortByDesc('smart_score')
            ->values();
    }

    public function notifyMatchesFor(Laporan $laporan): void
    {
        if ($laporan->status !== 'aktif') {
            return;
        }

        foreach ($this->cariKecocokanUntuk($laporan) as $pembanding) {
            $hilang = $laporan->tipe === 'hilang' ? $laporan : $pembanding;
            $ditemukan = $laporan->tipe === 'ditemukan' ? $laporan : $pembanding;
            $matchKey = $hilang->id.':'.$ditemukan->id;

            DB::transaction(function () use ($hilang, $ditemukan, $matchKey): void {
                $laporans = Laporan::query()
                    ->whereIn('id', [$hilang->id, $ditemukan->id])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $lockedHilang = $laporans->get($hilang->id);
                $lockedDitemukan = $laporans->get($ditemukan->id);

                if (! $lockedHilang || ! $lockedDitemukan
                    || $lockedHilang->tipe !== 'hilang'
                    || $lockedDitemukan->tipe !== 'ditemukan'
                    || $lockedHilang->status !== 'aktif'
                    || $lockedDitemukan->status !== 'aktif') {
                    return;
                }

                $skor = $this->hitungKecocokan($lockedHilang, $lockedDitemukan)['skor_total'];

                if ($skor <= self::AMBANG_SKOR) {
                    return;
                }

                $pemilikLaporanHilang = User::query()->find($lockedHilang->user_id);
                $pemilikLaporanTemuan = User::query()->find($lockedDitemukan->user_id);
                $penerima = collect([
                    [$pemilikLaporanHilang, $lockedHilang, $lockedDitemukan],
                    [$pemilikLaporanTemuan, $lockedDitemukan, $lockedHilang],
                ])->filter(fn (array $entry): bool => $entry[0] !== null)
                    ->unique(fn (array $entry): int => $entry[0]->id);

                foreach ($penerima as [$user, $laporanMilikUser, $laporanCocok]) {
                    $matchPattern = '%"match_key":"'.$matchKey.'"%';
                    $alreadyNotified = $user->notifications()
                        ->where('type', MatchDitemukanNotification::class)
                        ->whereRaw('CAST(data AS TEXT) LIKE ?', [$matchPattern])
                        ->exists();

                    if (! $alreadyNotified) {
                        $user->notify(new MatchDitemukanNotification(
                            $laporanMilikUser,
                            $laporanCocok,
                            $skor,
                            $matchKey,
                        ));
                    }
                }
            }, 3);
        }
    }

    private function utilitasKategori(Laporan $hilang, Laporan $ditemukan): float
    {
        return (int) $hilang->kategori_id === (int) $ditemukan->kategori_id ? 1.0 : 0.0;
    }

    private function utilitasLokasi(Laporan $hilang, Laporan $ditemukan): float
    {
        return (int) $hilang->lokasi_id === (int) $ditemukan->lokasi_id ? 1.0 : 0.0;
    }

    private function utilitasWaktu(Laporan $hilang, Laporan $ditemukan): float
    {
        $tanggalHilang = Carbon::parse($hilang->tanggal_kejadian);
        $tanggalDitemukan = Carbon::parse($ditemukan->tanggal_kejadian);

        if ($tanggalDitemukan->isBefore($tanggalHilang)) {
            return 0.0;
        }

        $selisihHari = $tanggalHilang->diffInDays($tanggalDitemukan);

        if ($selisihHari <= 1) {
            return 1.0;
        }

        if ($selisihHari <= 3) {
            return 0.75;
        }

        if ($selisihHari <= 7) {
            return 0.50;
        }

        if ($selisihHari <= 14) {
            return 0.25;
        }

        return 0.0;
    }

    private function utilitasCiri(Laporan $hilang, Laporan $ditemukan): float
    {
        $teksHilang = strtolower($hilang->nama_barang.' '.($hilang->ciri_khusus ?? ''));
        $teksDitemukan = strtolower($ditemukan->nama_barang.' '.($ditemukan->ciri_khusus ?? ''));
        $kataHilang = array_filter(str_word_count($teksHilang, 1), fn (string $kata): bool => strlen($kata) > 3);
        $kataDitemukan = array_filter(str_word_count($teksDitemukan, 1), fn (string $kata): bool => strlen($kata) > 3);

        if ($kataHilang === [] || $kataDitemukan === []) {
            return 0.0;
        }

        $persentase = count(array_intersect($kataHilang, $kataDitemukan)) / count($kataHilang);

        if ($persentase >= 0.8) {
            return 1.0;
        }

        if ($persentase >= 0.6) {
            return 0.75;
        }

        if ($persentase >= 0.4) {
            return 0.5;
        }

        if ($persentase >= 0.2) {
            return 0.25;
        }

        return 0.0;
    }

    private function utilitasWarna(Laporan $hilang, Laporan $ditemukan): float
    {
        return strtolower((string) $hilang->warna) === strtolower((string) $ditemukan->warna) ? 1.0 : 0.0;
    }
}
