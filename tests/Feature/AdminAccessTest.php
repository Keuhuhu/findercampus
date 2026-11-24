<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['/admin'],
            'legacy dashboard URL' => ['/admin/dashboard'],
            'users' => ['/admin/users'],
            'reports' => ['/admin/laporans'],
            'claim verification' => ['/admin/verifikasi/1'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_authenticated_user_without_admin_role_gets_403(string $path): void
    {
        $this->actingAs(User::factory()->create())
            ->get($path)
            ->assertForbidden();
    }

    public function test_admin_can_log_in_and_open_both_dashboard_urls(): void
    {
        $this->withoutVite();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/login', [
            'login' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk();
        $this->get('/admin/dashboard')->assertRedirect(route('admin.dashboard'));
    }
}
