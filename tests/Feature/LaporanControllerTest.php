<?php

namespace Tests\Feature;

use App\Models\FotoBarang;
use App\Models\Laporan;
use App\Models\User;
use App\Notifications\MatchDitemukanNotification;
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
