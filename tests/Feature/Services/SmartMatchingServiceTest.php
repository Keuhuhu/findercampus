<?php

namespace Tests\Feature\Services;

use App\Services\SmartMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesFinderCampusRecords;
use Tests\TestCase;

class SmartMatchingServiceTest extends TestCase
{
    use CreatesFinderCampusRecords;
    use RefreshDatabase;

    public function test_report_with_exactly_twenty_percent_match_is_below_the_notification_threshold(): void
    {
        $lostCategory = $this->createCategory('Lost category');
        $foundCategory = $this->createCategory('Found category');
        $lostLocation = $this->createLocation('Lost location');
        $foundLocation = $this->createLocation('Found location');
        $lostDate = now()->subDays(40);
        $foundDate = now()->subDays(10);

        $lost = $this->createReport(attributes: [
            'tipe' => 'hilang',
            'nama_barang' => 'Alpha bravo charlie delta echo',
            'kategori_id' => $lostCategory->id,
            'lokasi_id' => $lostLocation->id,
            'tanggal_kejadian' => $lostDate->toDateString(),
            'warna' => 'Biru',
            'ciri_khusus' => null,
        ]);
        $found = $this->createReport(attributes: [
            'tipe' => 'ditemukan',
            'nama_barang' => 'Alpha bravo zebra turtle ocean',
            'kategori_id' => $foundCategory->id,
            'lokasi_id' => $foundLocation->id,
            'tanggal_kejadian' => $foundDate->toDateString(),
            'warna' => 'Biru',
            'ciri_khusus' => null,
        ]);

        $service = app(SmartMatchingService::class);
        $analysis = $service->hitungKecocokan($lost, $found);

        $this->assertSame(20, $analysis['skor_total']);
        $this->assertCount(0, $service->cariKecocokanUntuk($lost));
    }
}
