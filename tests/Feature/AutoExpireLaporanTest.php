<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesFinderCampusRecords;
use Tests\TestCase;

class AutoExpireLaporanTest extends TestCase
{
    use CreatesFinderCampusRecords;
    use RefreshDatabase;

    public function test_command_marks_only_old_active_reports_expired_with_timestamp(): void
    {
        $this->travelTo('2026-11-24 12:00:00');
        $oldReport = $this->createReport();
        $oldReport->forceFill([
            'created_at' => now()->subDays(31),
            'updated_at' => now()->subDays(31),
        ])->save();
        $recentReport = $this->createReport();
        $recentReport->forceFill([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ])->save();

        $this->artisan('laporan:auto-expire', ['--days' => 30])
            ->assertExitCode(0);

        $this->assertDatabaseHas('laporans', [
            'id' => $oldReport->id,
            'status' => 'expired',
            'expired_at' => '2026-11-24 12:00:00',
        ]);
        $this->assertDatabaseHas('laporans', [
            'id' => $recentReport->id,
            'status' => 'aktif',
            'expired_at' => null,
        ]);
    }
}
