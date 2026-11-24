<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_upload_uses_the_configured_public_storage(): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public', 'filesystems.upload_driver' => 'filesystem']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profil/avatar', ['avatar' => UploadedFile::fake()->image('avatar.png', 100, 100)])
            ->assertRedirect();

        $avatarPath = $user->fresh()->avatar;
        $this->assertNotEmpty($avatarPath);
        Storage::disk('public')->assertExists($avatarPath);
    }
}
