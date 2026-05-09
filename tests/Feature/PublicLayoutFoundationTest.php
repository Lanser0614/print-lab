<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicLayoutFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_loads_manrope_font(): void
    {
        $response = $this->get('/ru');

        $response->assertOk();
        $response->assertSee('https://fonts.googleapis.com', false);
        $response->assertSee('family=Manrope', false);
    }
}
