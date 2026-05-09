<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_russian_by_default(): void
    {
        $this->get('/')
            ->assertRedirect('/ru');
    }

    public function test_root_redirects_to_locale_saved_in_cookie(): void
    {
        $this->withCookie('locale', 'uz')
            ->get('/')
            ->assertRedirect('/uz');
    }

    public function test_localized_home_sets_application_locale_and_persists_choice(): void
    {
        $this->get('/uz')
            ->assertOk()
            ->assertSee('lang="uz"', false)
            ->assertSessionHas('locale', 'uz')
            ->assertCookie('locale', 'uz');
    }

    public function test_localized_catalog_uses_same_page_language_switch_links(): void
    {
        $this->get('/ru/catalog?category=t-shirts')
            ->assertOk()
            ->assertSee('pl-language-switcher', false)
            ->assertSee('href="http://localhost:8000/ru/catalog?category=t-shirts"', false)
            ->assertSee('href="http://localhost:8000/uz/catalog?category=t-shirts"', false);
    }

    public function test_localized_home_renders_language_switcher(): void
    {
        $this->get('/ru')
            ->assertOk()
            ->assertSee('pl-language-switcher', false)
            ->assertSee('href="http://localhost:8000/uz"', false);
    }

    public function test_legacy_language_switch_saves_locale_and_redirects_to_localized_home(): void
    {
        $this->get('/language/uz')
            ->assertRedirect('/uz')
            ->assertSessionHas('locale', 'uz')
            ->assertCookie('locale', 'uz');
    }

    public function test_invalid_locale_prefix_returns_not_found(): void
    {
        $this->get('/en/catalog')
            ->assertNotFound();
    }
}
