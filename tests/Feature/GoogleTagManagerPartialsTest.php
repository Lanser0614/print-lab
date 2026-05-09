<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleTagManagerPartialsTest extends TestCase
{
    public function test_head_partial_renders_gtm_script_when_id_is_set(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $html = view('partials.gtm-head')->render();

        // The gtm.js URL is concatenated at runtime in JavaScript ('?id='+i+dl),
        // so the HTML contains the literal path and the ID as a JS string argument.
        $this->assertStringContainsString('https://www.googletagmanager.com/gtm.js?id=', $html);
        $this->assertStringContainsString("'GTM-PQSQLKKL'", $html);
        $this->assertStringContainsString('dataLayer', $html);
        $this->assertStringContainsString("'gtm.start'", $html);
    }

    public function test_body_partial_renders_noscript_iframe_when_id_is_set(): void
    {
        config()->set('services.gtm.container_id', 'GTM-PQSQLKKL');

        $html = view('partials.gtm-body')->render();

        $this->assertStringContainsString(
            '<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQSQLKKL"',
            $html,
        );
        $this->assertStringContainsString('display:none;visibility:hidden', $html);
    }

    public function test_head_partial_renders_nothing_when_id_is_empty(): void
    {
        config()->set('services.gtm.container_id', null);

        $html = trim(view('partials.gtm-head')->render());

        $this->assertSame('', $html);
        $this->assertStringNotContainsString('googletagmanager.com', $html);
    }

    public function test_body_partial_renders_nothing_when_id_is_empty(): void
    {
        config()->set('services.gtm.container_id', '');

        $html = trim(view('partials.gtm-body')->render());

        $this->assertSame('', $html);
        $this->assertStringNotContainsString('googletagmanager.com', $html);
    }

    public function test_partials_do_not_hardcode_container_id(): void
    {
        $head = file_get_contents(resource_path('views/partials/gtm-head.blade.php'));
        $body = file_get_contents(resource_path('views/partials/gtm-body.blade.php'));

        $this->assertStringNotContainsString('GTM-PQSQLKKL', $head);
        $this->assertStringNotContainsString('GTM-PQSQLKKL', $body);
    }
}
