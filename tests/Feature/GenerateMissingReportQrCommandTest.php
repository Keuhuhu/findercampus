<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\Feature\Support\CreatesFinderCampusRecords;
use Tests\TestCase;

class GenerateMissingReportQrCommandTest extends TestCase
{
    use CreatesFinderCampusRecords;
    use RefreshDatabase;

    public function test_dry_run_counts_only_active_found_reports_without_qr_and_makes_no_changes(): void
    {
        $missingQr = $this->createReport();
        $this->createReport(null, ['qr_code_path' => 'qrcodes/existing.svg']);
        $this->createReport(null, ['status' => 'selesai']);
        $this->createReport(null, ['tipe' => 'hilang']);
        $archived = $this->createReport();
        $archived->delete();

        $this->artisan('laporan:generate-missing-qr', ['--dry-run' => true])
            ->expectsOutputToContain('tanpa QR: 1')
            ->assertExitCode(0);

        $this->assertNull($missingQr->fresh()->qr_code_path);
    }

    public function test_command_generates_qr_only_for_eligible_reports_and_can_be_rerun(): void
    {
        $missingQr = $this->createReport();
        $this->createReport(null, ['qr_code_path' => 'qrcodes/existing.svg']);
        $this->createReport(null, ['status' => 'expired']);
        $this->createReport(null, ['tipe' => 'hilang']);

        $this->mock(QrCodeService::class, function ($mock) use ($missingQr): void {
            $mock->shouldReceive('generate')
                ->once()
                ->with(Mockery::on(fn (Laporan $laporan): bool => $laporan->is($missingQr)))
                ->andReturn('qrcodes/'.$missingQr->kode_laporan.'.svg');
        });

        $this->artisan('laporan:generate-missing-qr')
            ->expectsOutputToContain('QR berhasil dibuat: 1. Gagal: 0.')
            ->assertExitCode(0);

        $this->assertSame('qrcodes/'.$missingQr->kode_laporan.'.svg', $missingQr->fresh()->qr_code_path);

        $this->artisan('laporan:generate-missing-qr')
            ->expectsOutputToContain('Tidak ada laporan temuan aktif yang memerlukan QR.')
            ->assertExitCode(0);
    }
}
