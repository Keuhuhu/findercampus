<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\KlaimDiajukanNotification;
use App\Notifications\KlaimStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Support\CreatesFinderCampusRecords;
use Tests\TestCase;

class KlaimControllerTest extends TestCase
{
    use CreatesFinderCampusRecords;
    use RefreshDatabase;

    public static function nonClaimableReports(): array
    {
        return [
            'lost report' => ['hilang', 'aktif', false],
            'inactive found report' => ['ditemukan', 'expired', false],
            'owner claims own report' => ['ditemukan', 'aktif', true],
        ];
    }

    public function test_valid_claim_updates_report_and_notifies_report_owner(): void
    {
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $laporan = $this->createReport($owner);

        $this->actingAs($claimant)
            ->post('/klaim', [
                'laporan_id' => $laporan->id,
                'bukti_kepemilikan' => 'Ada goresan berbentuk angka tujuh pada sisi kanan barang.',
            ])
            ->assertRedirect(route('klaim.show', 1));

        $this->assertDatabaseHas('klaims', [
            'laporan_id' => $laporan->id,
            'user_id' => $claimant->id,
            'status' => 'pending',
        ]);
        $this->assertSame('diklaim', $laporan->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $owner->id,
            'type' => KlaimDiajukanNotification::class,
        ]);
    }

    #[DataProvider('nonClaimableReports')]
    public function test_claim_is_rejected_for_non_claimable_report(string $type, string $status, bool $ownerClaims): void
    {
        $owner = User::factory()->create();
        $laporan = $this->createReport($owner, ['tipe' => $type, 'status' => $status]);
        $claimant = $ownerClaims ? $owner : User::factory()->create();

        $this->actingAs($claimant)
            ->post('/klaim', [
                'laporan_id' => $laporan->id,
                'bukti_kepemilikan' => 'Bukti kepemilikan dengan rincian lebih dari sepuluh.',
            ])
            ->assertSessionHasErrors('laporan_id');

        $this->assertDatabaseCount('klaims', 0);
        $this->assertSame($status, $laporan->fresh()->status);
    }

    public function test_claim_is_rejected_when_another_active_claim_already_exists(): void
    {
        $laporanOwner = User::factory()->create();
        $existingClaimant = User::factory()->create();
        $newClaimant = User::factory()->create();
        $laporan = $this->createReport($laporanOwner);
        $this->createClaim($laporan, $existingClaimant);

        $this->actingAs($newClaimant)
            ->post('/klaim', [
                'laporan_id' => $laporan->id,
                'bukti_kepemilikan' => 'Bukti kepemilikan yang tidak sama dengan klaim sebelumnya.',
            ])
            ->assertSessionHasErrors('laporan_id');

        $this->assertDatabaseCount('klaims', 1);
        $this->assertSame('aktif', $laporan->fresh()->status);
    }

    public function test_uploaded_claim_evidence_is_private_and_only_available_to_related_users(): void
    {
        Storage::fake('private_local');
        config(['filesystems.upload_driver' => 'filesystem']);

        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $laporan = $this->createReport($owner);

        $this->actingAs($claimant)
            ->post('/klaim', [
                'laporan_id' => $laporan->id,
                'bukti_kepemilikan' => 'Bukti dengan rincian foto pendukung untuk verifikasi.',
                'foto_bukti' => UploadedFile::fake()->image('proof.png', 160, 120),
            ])
            ->assertRedirect(route('klaim.show', 1));

        $claim = $laporan->klaims()->firstOrFail();
        $this->assertNotNull($claim->foto_bukti);
        Storage::disk('private_local')->assertExists($claim->foto_bukti);

        $this->get(route('klaim.evidence', $claim))->assertOk();
        $this->actingAs(User::factory()->create())
            ->get(route('klaim.evidence', $claim))
            ->assertForbidden();
    }

    public function test_approving_pending_claim_creates_return_history_and_rejects_repeat_decision(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $laporan = $this->createReport($owner, ['status' => 'diklaim']);
        $claim = $this->createClaim($laporan, $claimant);

        $this->actingAs($admin)
            ->post(route('admin.verifikasi.approve', $claim), ['catatan' => 'Bukti sudah diverifikasi.'])
            ->assertRedirect(route('admin.verifikasi.show', $claim));

        $this->assertDatabaseHas('klaims', ['id' => $claim->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('laporans', ['id' => $laporan->id, 'status' => 'selesai']);
        $this->assertDatabaseCount('verifikasis', 1);
        $this->assertDatabaseCount('riwayat_pengembalians', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $claimant->id,
            'type' => KlaimStatusUpdatedNotification::class,
        ]);

        $this->post(route('admin.verifikasi.approve', $claim))->assertConflict();
        $this->assertDatabaseCount('verifikasis', 1);
        $this->assertDatabaseCount('riwayat_pengembalians', 1);
    }

    public function test_rejecting_pending_claim_reactivates_report_and_rejects_repeat_decision(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $claimant = User::factory()->create();
        $laporan = $this->createReport($owner, ['status' => 'diklaim']);
        $claim = $this->createClaim($laporan, $claimant);

        $this->actingAs($admin)
            ->post(route('admin.verifikasi.reject', $claim), ['catatan' => 'Bukti kepemilikan belum cukup.'])
            ->assertRedirect(route('admin.verifikasi.show', $claim));

        $this->assertDatabaseHas('klaims', ['id' => $claim->id, 'status' => 'ditolak']);
        $this->assertDatabaseHas('laporans', ['id' => $laporan->id, 'status' => 'aktif']);
        $this->assertDatabaseCount('verifikasis', 1);
        $this->assertDatabaseCount('riwayat_pengembalians', 0);

        $this->post(route('admin.verifikasi.reject', $claim), ['catatan' => 'Bukti kepemilikan belum cukup.'])
            ->assertConflict();
        $this->assertDatabaseCount('verifikasis', 1);
    }
}
