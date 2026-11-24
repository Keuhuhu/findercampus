<?php

namespace Tests\Feature;

use App\Models\FotoBarang;
use App\Models\Laporan;
use App\Models\User;
use App\Notifications\MatchDitemukanNotification;
use App\Services\ReportImageUploadService;
use App\Services\SmartMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Support\CreatesFinderCampusRecords;
use Tests\TestCase;

class LaporanControllerTest extends TestCase
{
    use CreatesFinderCampusRecords;
    use RefreshDatabase;

    public static function matchingReportOrientations(): array
    {
        return [
            'newly found item matches an existing lost report' => ['ditemukan', 'hilang'],
            'new lost report matches an existing found item' => ['hilang', 'ditemukan'],
        ];
    }

    public function test_owner_can_open_the_report_edit_form(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        config(['filesystems.default' => 'public', 'filesystems.upload_driver' => 'filesystem']);
        $owner = User::factory()->create();
        $laporan = $this->createReport($owner);
        FotoBarang::create([
            'laporan_id' => $laporan->id,
            'file_path' => 'images/barangs/current-photo.jpg',
            'is_primary' => true,
        ]);

        $this->actingAs($owner)
            ->get(route('laporan.edit', $laporan))
            ->assertOk()
            ->assertSee('Edit Laporan')
            ->assertSee('images/barangs/current-photo.jpg');
    }

    public function test_owner_cannot_change_report_lifecycle_status_in_the_edit_request(): void
    {
        $owner = User::factory()->create();
        $laporan = $this->createReport($owner);

        $this->actingAs($owner)
            ->put(route('laporan.update', $laporan), [
                'nama_barang' => 'Nama baru',
                'kategori_id' => $laporan->kategori_id,
                'lokasi_id' => $laporan->lokasi_id,
                'tanggal_kejadian' => now()->toDateString(),
                'warna' => 'Biru',
                'status' => 'selesai',
            ])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('laporans', [
            'id' => $laporan->id,
            'status' => 'aktif',
            'nama_barang' => 'Barang temuan',
        ]);
    }

    public function test_another_user_cannot_edit_a_report(): void
    {
        $laporan = $this->createReport();

        $this->actingAs(User::factory()->create())
            ->get(route('laporan.edit', $laporan))
            ->assertForbidden();
    }

    public function test_lost_report_cards_open_details_and_offer_a_found_report(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $lostReport = $this->createReport($user, ['tipe' => 'hilang', 'nama_barang' => 'Kunci motor hilang']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('laporan.show', $lostReport));

        $this->get(route('laporan.show', $lostReport))
            ->assertOk()
            ->assertSee(route('laporan.create', ['tipe' => 'ditemukan']))
            ->assertDontSee('Barang ini tidak tersedia untuk diklaim.');

        $this->get(route('laporan.create', ['tipe' => 'ditemukan']))
            ->assertOk()
            ->assertSee("tipe: 'ditemukan'", false);
    }

    public function test_public_qr_page_only_exposes_safe_information_for_active_found_reports(): void
    {
        $this->withoutVite();
        $owner = User::factory()->create([
            'name' => 'Nama Pelapor Privat',
            'whatsapp' => '08123456789',
            'whatsapp_visible' => true,
        ]);
        $report = $this->createReport($owner, [
            'qr_code_path' => 'qrcodes/report.svg',
            'detail_lokasi' => 'Lokasi detail privat',
            'ciri_khusus' => 'Ciri khusus privat',
            'deskripsi' => 'Deskripsi privat',
        ]);

        $this->get(route('laporan.qr.public', $report))
            ->assertOk()
            ->assertSee($report->nama_barang)
            ->assertSee($report->kategori->nama)
            ->assertSee($report->kode_laporan)
            ->assertSee($report->lokasi->nama)
            ->assertDontSee('Nama Pelapor Privat')
            ->assertDontSee('08123456789')
            ->assertDontSee('Lokasi detail privat')
            ->assertDontSee('Ciri khusus privat')
            ->assertDontSee('Deskripsi privat');
    }

    public function test_public_qr_page_does_not_show_lost_archived_or_non_qr_reports(): void
    {
        $lostReport = $this->createReport(null, [
            'tipe' => 'hilang',
            'qr_code_path' => 'qrcodes/lost.svg',
        ]);
        $archivedReport = $this->createReport(null, ['qr_code_path' => 'qrcodes/archived.svg']);
        $archivedReport->delete();
        $reportWithoutQr = $this->createReport();

        $this->get(route('laporan.qr.public', $lostReport))->assertNotFound();
        $this->get(route('laporan.qr.public', $archivedReport->id))->assertNotFound();
        $this->get(route('laporan.qr.public', $reportWithoutQr))->assertNotFound();
    }

    public function test_qr_preview_and_download_generate_current_public_destination(): void
    {
        $owner = User::factory()->create();
        $report = $this->createReport($owner, ['qr_code_path' => 'qrcodes/report.svg']);

        $this->actingAs($owner)
            ->get(route('laporan.qr.image', $report))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->assertSee('<svg xmlns=', false);

        $this->get(route('laporan.qr', $report))
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename="QR_'.$report->kode_laporan.'.svg"')
            ->assertSee('<svg xmlns=', false);
    }

    public function test_admin_can_edit_other_users_report_photos_and_archive_then_restore_without_losing_claims(): void
    {
        $this->withoutVite();
        Storage::fake('public');
        config(['filesystems.default' => 'public', 'filesystems.upload_driver' => 'filesystem']);

        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $claimant = User::factory()->create();
        $report = $this->createReport($owner);
        $oldPhoto = FotoBarang::create([
            'laporan_id' => $report->id,
            'file_path' => 'images/barangs/old-photo.jpg',
            'is_primary' => true,
        ]);
        Storage::disk('public')->put($oldPhoto->file_path, 'old photo');
        $claim = $this->createClaim($report, $claimant);

        $this->actingAs($admin)
            ->get(route('laporan.edit', $report))
            ->assertOk()
            ->assertSee('Hapus foto');

        $this->put(route('laporan.update', $report), [
            'nama_barang' => 'Dompet yang diperbarui',
            'kategori_id' => $report->kategori_id,
            'lokasi_id' => $report->lokasi_id,
            'tanggal_kejadian' => now()->toDateString(),
            'warna' => 'Biru',
            'remove_foto_ids' => [$oldPhoto->id],
            'foto' => [UploadedFile::fake()->image('new-photo.jpg', 200, 200)],
        ])->assertRedirect(route('laporan.show', $report));

        $this->assertDatabaseMissing('foto_barangs', ['id' => $oldPhoto->id]);
        $newPhoto = $report->fresh()->fotos()->firstOrFail();
        $this->assertTrue($newPhoto->is_primary);
        Storage::disk('public')->assertExists($newPhoto->file_path);

        $this->delete(route('laporan.destroy', $report))
            ->assertRedirect(route('admin.laporans'));

        $this->assertTrue(Laporan::withTrashed()->findOrFail($report->id)->trashed());
        $this->assertDatabaseHas('klaims', ['id' => $claim->id, 'laporan_id' => $report->id]);
        $this->assertSame($report->id, $claim->fresh()->laporan->id);

        $this->post(route('admin.laporans.restore', $report->id))
            ->assertRedirect(route('admin.laporans', ['archived' => true]));

        $this->assertFalse(Laporan::findOrFail($report->id)->trashed());
    }

    public function test_admin_laporan_page_lists_actions_and_separates_archived_reports(): void
    {
        $this->withoutVite();
        $admin = User::factory()->create(['role' => 'admin']);
        $activeReport = $this->createReport(null, ['nama_barang' => 'Laporan yang masih aktif']);
        $archivedReport = $this->createReport(null, ['nama_barang' => 'Laporan yang diarsipkan']);
        $archivedReport->delete();

        $this->actingAs($admin)
            ->get(route('admin.laporans'))
            ->assertOk()
            ->assertSee(route('laporan.edit', $activeReport))
            ->assertSee('Arsipkan')
            ->assertDontSee($archivedReport->nama_barang);

        $this->get(route('admin.laporans', ['archived' => true]))
            ->assertOk()
            ->assertSee($archivedReport->nama_barang)
            ->assertSee(route('admin.laporans.restore', $archivedReport->id));
    }

    public function test_authenticated_user_can_request_a_short_lived_direct_upload_url(): void
    {
        $user = User::factory()->create();
        $uploadService = $this->mock(ReportImageUploadService::class);
        $uploadService->shouldReceive('supportsSignedUploads')->once()->andReturn(true);
        $uploadService->shouldReceive('createSignedUpload')
            ->once()
            ->withArgs(fn (User $requestedUser, string $mimeType, int $size): bool => $requestedUser->is($user) && $mimeType === 'image/jpeg' && $size === 1024)
            ->andReturn([
                'id' => '8a22d385-fac0-48a8-9e90-e154880c4c26',
                'url' => 'https://project.storage.supabase.co/upload/signed',
                'headers' => ['Content-Type' => 'image/jpeg'],
                'expires_at' => now()->addMinutes(10)->toIso8601String(),
            ]);

        $this->actingAs($user)
            ->postJson(route('laporan.upload-urls.store'), ['content_type' => 'image/jpeg', 'size' => 1024])
            ->assertCreated()
            ->assertJsonPath('id', '8a22d385-fac0-48a8-9e90-e154880c4c26')
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_direct_upload_url_rejects_images_larger_than_five_megabytes(): void
    {
        $user = User::factory()->create();
        $uploadService = $this->mock(ReportImageUploadService::class);
        $uploadService->shouldReceive('supportsSignedUploads')->once()->andReturn(true);
        $uploadService->shouldNotReceive('createSignedUpload');

        $this->actingAs($user)
            ->postJson(route('laporan.upload-urls.store'), [
                'content_type' => 'image/jpeg',
                'size' => ReportImageUploadService::MAX_FILE_BYTES + 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('size');
    }

    #[DataProvider('matchingReportOrientations')]
    public function test_new_report_generates_found_item_qr_and_notifies_each_match_owner_once(string $newType, string $existingType): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public', 'filesystems.upload_driver' => 'filesystem']);

        $existingOwner = User::factory()->create();
        $newOwner = User::factory()->create();
        $category = $this->createCategory('Electronics');
        $existingLocation = $this->createLocation('Library');
        $newLocation = $this->createLocation('Laboratory');
        $existingDate = $existingType === 'hilang' ? now()->subDays(40) : now()->subDays(20);
        $newDate = $newType === 'hilang' ? now()->subDays(40) : now()->subDays(20);

        $this->createReport($existingOwner, [
            'tipe' => $existingType,
            'nama_barang' => 'Kamera merah',
            'kategori_id' => $category->id,
            'lokasi_id' => $existingLocation->id,
            'tanggal_kejadian' => $existingDate->toDateString(),
            'warna' => 'Merah',
            'ciri_khusus' => 'tali pendek',
        ]);

        $this->actingAs($newOwner)
            ->post(route('laporan.store'), [
                'tipe' => $newType,
                'nama_barang' => 'Laptop hijau',
                'kategori_id' => $category->id,
                'lokasi_id' => $newLocation->id,
                'tanggal_kejadian' => $newDate->toDateString(),
                'waktu_kejadian' => '10:30',
                'warna' => 'Hijau',
                'ciri_khusus' => 'stiker bundar',
                'deskripsi' => 'Detail laporan baru untuk pengujian.',
                'foto' => [UploadedFile::fake()->image('item.jpg', 200, 200)],
            ])
            ->assertRedirect('/dashboard');

        $laporanBaru = Laporan::where('user_id', $newOwner->id)->firstOrFail();

        $this->assertDatabaseHas('foto_barangs', ['laporan_id' => $laporanBaru->id]);
        Storage::disk('public')->assertExists($laporanBaru->fotos()->firstOrFail()->file_path);

        if ($newType === 'ditemukan') {
            $this->assertNotNull($laporanBaru->qr_code_path);
            Storage::disk('public')->assertExists($laporanBaru->qr_code_path);
            $this->get(route('laporan.qr', $laporanBaru))->assertOk();
        } else {
            $this->assertNull($laporanBaru->qr_code_path);
        }

        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $existingOwner->id,
            'type' => MatchDitemukanNotification::class,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $newOwner->id,
            'type' => MatchDitemukanNotification::class,
        ]);

        app(SmartMatchingService::class)->notifyMatchesFor($laporanBaru->fresh());

        $this->assertDatabaseCount('notifications', 2);
    }
}
