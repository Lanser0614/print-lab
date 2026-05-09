<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleTagManagerNotInAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_does_not_include_gtm_even_when_container_id_is_set(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $response = $this->get('/admin/login');

        // We don't care about the exact status (Filament may redirect), just that GTM is absent.
        $response->assertDontSee('googletagmanager.com', false);
    }

    public function test_admin_dashboard_does_not_include_gtm_when_authenticated(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@printlab.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertDontSee('googletagmanager.com', false);
    }
}
