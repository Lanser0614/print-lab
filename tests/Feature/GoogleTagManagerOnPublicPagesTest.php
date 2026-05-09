<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleTagManagerOnPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_includes_gtm_when_container_id_is_set(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $response = $this->get('/ru');

        $response->assertOk();
        // gtm.js URL is built at JS runtime: HTML has the literal path + the ID as a JS string.
        $response->assertSee('https://www.googletagmanager.com/gtm.js?id=', false);
        $response->assertSee("'GTM-PQSQLKKL'", false);
        // noscript iframe URL is rendered server-side by Blade — full URL is in HTML.
        $response->assertSee('https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL', false);
    }

    public function test_homepage_does_not_include_gtm_when_container_id_is_empty(): void
    {
        config()->set('services.gtm.container_id', null);

        $response = $this->get('/ru');

        $response->assertOk();
        $response->assertDontSee('googletagmanager.com', false);
    }

    public function test_gtm_head_snippet_is_above_meta_description(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $html = $this->get('/ru')->getContent();

        $gtmPos = strpos($html, 'googletagmanager.com/gtm.js');
        $metaPos = strpos($html, '<meta name="description"');

        $this->assertNotFalse($gtmPos, 'GTM head snippet must be present');
        $this->assertNotFalse($metaPos, 'meta description must be present');
        $this->assertLessThan(
            $metaPos,
            $gtmPos,
            'GTM head snippet must appear before <meta name="description"> (i.e. as high in <head> as possible)',
        );
    }

    public function test_gtm_noscript_snippet_appears_immediately_after_body_open(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $html = $this->get('/ru')->getContent();

        // Match: <body ...> ... <noscript><iframe src="...googletagmanager..."
        // with only whitespace / blade whitespace allowed in between.
        $this->assertMatchesRegularExpression(
            '/<body\b[^>]*>\s*<noscript><iframe src="https:\/\/www\.googletagmanager\.com\/ns\.html\?id=GTM-PQSQLKKL"/',
            $html,
            'GTM noscript iframe must appear immediately after the opening <body> tag',
        );
    }
}
