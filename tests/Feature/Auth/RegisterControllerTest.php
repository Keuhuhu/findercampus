<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_saves_whatsapp_to_the_user_record(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Finder Campus User',
            'nim_nip' => 'FC-TEST-1001',
            'fakultas' => 'Teknik',
            'email' => 'finder-user@example.test',
            'whatsapp' => '081234567890',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'terms' => 'yes',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'finder-user@example.test')->firstOrFail();

        $this->assertSame('081234567890', $user->whatsapp);
        $this->assertTrue(Hash::check('StrongPassword123!', $user->password));
        $this->assertTrue($user->email_verification_required);
        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
