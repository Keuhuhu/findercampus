<?php

namespace App\Console\Commands;

use App\Models\Laporan;
use App\Services\QrCodeService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

#[Signature('laporan:generate-missing-qr {--dry-run : Hitung laporan yang memenuhi syarat tanpa membuat QR}')]
#[Description('Membuat QR yang belum ada untuk laporan temuan aktif')]
class GenerateMissingReportQr extends Command
{
    public function handle(QrCodeService $qrCodeService): int
    {
        $laporans = Laporan::query()
            ->where('tipe', 'ditemukan')
            ->where('status', 'aktif')
            ->whereNull('qr_code_path');
        $total = (clone $laporans)->count();

        if ($this->option('dry-run')) {
            $this->info("Laporan ditemukan aktif tanpa QR: {$total}. Tidak ada perubahan yang dibuat.");

            return self::SUCCESS;
        }

        if ($total === 0) {
            $this->info('Tidak ada laporan temuan aktif yang memerlukan QR.');

            return self::SUCCESS;
        }

        if (! $this->isProductionConfigurationReady()) {
            return self::FAILURE;
        }

        $generated = 0;
        $failed = 0;

        $laporans->chunkById(100, function ($batch) use ($qrCodeService, &$generated, &$failed): void {
            foreach ($batch as $laporan) {
                try {
                    $qrPath = $qrCodeService->generate($laporan);
                    $laporan->forceFill(['qr_code_path' => $qrPath])->save();
                    $generated++;
                } catch (Throwable $exception) {
                    $failed++;
                    report($exception);
                    $this->warn("QR untuk laporan {$laporan->kode_laporan} gagal dibuat; jalankan ulang setelah memperbaiki masalahnya.");
                }
            }
        });

        $this->info("QR berhasil dibuat: {$generated}. Gagal: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function isProductionConfigurationReady(): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('Backfill QR harus dijalankan dengan koneksi PostgreSQL Supabase.');

            return false;
        }

        if (config('filesystems.upload_driver') !== 'supabase_s3') {
            $this->error('Set UPLOAD_STORAGE_DRIVER=supabase_s3 agar file QR disimpan ke Supabase Storage.');

            return false;
        }

        $appUrl = (string) config('app.url');
        $host = parse_url($appUrl, PHP_URL_HOST);

        if (
            parse_url($appUrl, PHP_URL_SCHEME) !== 'https'
            || ! is_string($host)
            || $host === ''
            || Str::endsWith($host, '.test')
            || in_array($host, ['localhost', '127.0.0.1', '::1'], true)
        ) {
            $this->error('Set APP_URL ke domain HTTPS FinderCampus produksi sebelum membuat QR.');

            return false;
        }

        return true;
    }
}
