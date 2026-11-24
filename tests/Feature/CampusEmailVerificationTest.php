<?php

namespace Tests\Feature;

use App\Models\CampusEmailVerification;
use App\Models\User;
use App\Notifications\VerifyCampusEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CampusEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_profile_shows_separate_campus_email_verification_action(): void
    {
        $user = User::factory()->create(['email' => 'main-account@example.test']);

        $this->actingAs($user)
            ->get(route('profil.show'))
            ->assertOk()
            ->assertSee('Verifikasi Email Kampus')
            ->assertSee('Email Akun')
            ->assertSee('main-account@example.test');
    }

    public function test_user_can_request_an_allowed_campus_email_without_changing_primary_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'main-account@example.test']);
        $campusEmail = 'student.123@student.unsri.ac.id';

        $this->actingAs($user)
            ->post(route('profil.campus-verification.send'), ['campus_email' => $campusEmail])
            ->assertRedirect();

        $this->assertSame('main-account@example.test', $user->fresh()->email);
        $this->assertFalse($user->fresh()->is_verified);
        $this->assertDatabaseHas('campus_email_verifications', [
            'user_id' => $user->id,
            'email' => $campusEmail,
            'status' => 'pending',
        ]);
        Notification::assertSentOnDemand(
            VerifyCampusEmail::class,
            fn (VerifyCampusEmail $notification, array $channels, object $notifiable): bool => array_key_first($notifiable->routes['mail']) === $campusEmail,
        );
    }

    public function test_only_the_approved_unsri_email_domains_can_receive_the_badge(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        foreach (['person@gmail.com', 'student.unsri.ac.id@evil.test', 'student@fake.student.unsri.ac.id'] as $email) {
            $this->actingAs($user)
                ->post(route('profil.campus-verification.send'), ['campus_email' => $email])
                ->assertSessionHasErrors('campus_email');
        }

        $this->assertDatabaseCount('campus_email_verifications', 0);
        Notification::assertNothingSent();
    }

    public function test_campus_email_can_only_be_linked_to_one_account(): void
    {
        Notification::fake();
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $campusEmail = 'student.456@student.unsri.ac.id';

        CampusEmailVerification::factory()->create([
            'user_id' => $firstUser->id,
            'email' => $campusEmail,
        ]);

        $this->actingAs($secondUser)
            ->post(route('profil.campus-verification.send'), ['campus_email' => $campusEmail])
            ->assertSessionHasErrors('campus_email');

        $this->assertDatabaseCount('campus_email_verifications', 1);
        Notification::assertNothingSent();
    }

    public function test_guest_can_verify_campus_email_in_a_new_tab_without_logging_in(): void
    {
        $user = User::factory()->create(['email' => 'main-account@example.test']);
        $verification = CampusEmailVerification::factory()->create([
            'user_id' => $user->id,
            'email' => 'staff@unsri.ac.id',
            'status' => 'pending',
        ]);

        $this->get($this->signedVerificationUrl($verification))
            ->assertOk()
            ->assertSee('Email Unsri berhasil diverifikasi')
            ->assertSee('boleh menutup tab ini');

        $this->assertGuest();
        $this->assertTrue($user->fresh()->is_verified);
        $this->assertSame('main-account@example.test', $user->fresh()->email);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseHas('campus_email_verifications', [
            'id' => $verification->id,
            'status' => 'verified',
        ]);
        $this->assertNotNull($verification->fresh()->verified_at);

        $this->get($this->signedVerificationUrl($verification->fresh()))
            ->assertOk()
            ->assertSee('Email Unsri berhasil diverifikasi');
    }

    public function test_replacing_campus_email_keeps_current_badge_and_invalidates_old_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_verified' => true]);
        $verifiedEmail = CampusEmailVerification::factory()->verified()->create([
            'user_id' => $user->id,
            'email' => 'current@student.unsri.ac.id',
        ]);
        $oldPendingEmail = CampusEmailVerification::factory()->create([
            'user_id' => $user->id,
            'email' => 'old-pending@student.unsri.ac.id',
        ]);
        $oldVerificationUrl = $this->signedVerificationUrl($oldPendingEmail);

        $this->actingAs($user)
            ->post(route('profil.campus-verification.send'), ['campus_email' => 'replacement@ilkom.unsri.ac.id'])
            ->assertRedirect();

        $this->assertTrue($user->fresh()->is_verified);
        $this->assertDatabaseHas('campus_email_verifications', [
            'id' => $verifiedEmail->id,
            'status' => 'verified',
        ]);
        $this->assertDatabaseMissing('campus_email_verifications', ['id' => $oldPendingEmail->id]);

        $this->get($oldVerificationUrl)
            ->assertForbidden()
            ->assertViewHas('verified', false);

        $replacement = CampusEmailVerification::query()->where('user_id', $user->id)->where('status', 'pending')->firstOrFail();

        $this->get($this->signedVerificationUrl($replacement))
            ->assertOk()
            ->assertSee('Email Unsri berhasil diverifikasi');

        $this->assertTrue($user->fresh()->is_verified);
        $this->assertDatabaseMissing('campus_email_verifications', ['id' => $verifiedEmail->id]);
        $this->assertDatabaseHas('campus_email_verifications', [
            'id' => $replacement->id,
            'status' => 'verified',
        ]);
    }

    public function test_expired_campus_link_does_not_verify_the_email(): void
    {
        $user = User::factory()->create();
        $verification = CampusEmailVerification::factory()->create([
            'user_id' => $user->id,
            'email' => 'expired@student.unsri.ac.id',
        ]);
        $relativeUrl = URL::temporarySignedRoute(
            'verification.campus',
            now()->subMinute(),
            ['verification' => $verification->id, 'hash' => sha1($verification->email)],
            absolute: false,
        );

        $this->get(URL::to($relativeUrl))
            ->assertForbidden()
            ->assertViewIs('auth.campus-email-verification-result')
            ->assertViewHas('verified', false);

        $this->assertFalse($user->fresh()->is_verified);
        $this->assertDatabaseHas('campus_email_verifications', [
            'id' => $verification->id,
            'status' => 'pending',
        ]);
    }

    private function signedVerificationUrl(CampusEmailVerification $verification): string
    {
        $relativeUrl = URL::temporarySignedRoute(
            'verification.campus',
            now()->addMinutes(60),
            ['verification' => $verification->id, 'hash' => sha1($verification->email)],
            absolute: false,
        );

        return URL::to($relativeUrl);
    }
}
