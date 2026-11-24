<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_valid_verification_link_verifies_email_without_changing_campus_identity_status(): void
    {
        $user = User::factory()->requiresEmailVerification()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('dashboard'));

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse($user->is_verified);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_new_unverified_account_is_redirected_to_notice_but_can_edit_profile(): void
    {
        $user = User::factory()->requiresEmailVerification()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->get(route('profil.edit'))->assertOk();
        $this->get(route('verification.notice'))->assertOk();
    }

    public function test_existing_account_without_email_verification_remains_allowed(): void
    {
        $user = User::factory()->unverified()->create([
            'email_verification_required' => false,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_new_unverified_admin_is_also_required_to_verify_email(): void
    {
        $admin = User::factory()->requiresEmailVerification()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verification_email_can_be_resent_with_a_rate_limit(): void
    {
        Notification::fake();
        $user = User::factory()->requiresEmailVerification()->create();
        $this->actingAs($user);

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post(route('verification.send'))->assertRedirect();
        }

        $this->post(route('verification.send'))->assertTooManyRequests();

        Notification::assertSentToTimes($user, VerifyEmail::class, 6);
    }

    public function test_expired_verification_link_shows_resend_instructions(): void
    {
        $user = User::factory()->requiresEmailVerification()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden()
            ->assertViewIs('auth.verify-email')
            ->assertViewHas('verificationLinkExpired', true);
    }

    public function test_invalid_verification_signature_is_rejected(): void
    {
        $user = User::factory()->requiresEmailVerification()->create();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );
        $tamperedUrl = $verificationUrl.'&signature=invalid';

        $this->actingAs($user)
            ->get($tamperedUrl)
            ->assertForbidden()
            ->assertViewIs('auth.verify-email')
            ->assertViewHas('verificationLinkExpired', true);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_changing_email_invalidates_old_verification_and_sends_a_new_link(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'name' => 'Finder Campus Tester',
            'email' => 'old-address@example.test',
            'nim_nip' => 'FC-EMAIL-TEST',
            'fakultas' => 'Teknik',
            'whatsapp' => '081234567890',
            'email_verified_at' => now(),
            'email_verification_required' => false,
        ]);
        $user->forceFill(['is_verified' => true])->save();

        $this->actingAs($user)
            ->put(route('profil.update'), [
                'name' => $user->name,
                'nim_nip' => $user->nim_nip,
                'fakultas' => $user->fakultas,
                'email' => 'new-address@example.test',
                'whatsapp' => $user->whatsapp,
            ])
            ->assertRedirect('/profil');

        $user->refresh();

        $this->assertSame('new-address@example.test', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->email_verification_required);
        $this->assertTrue($user->is_verified);
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
