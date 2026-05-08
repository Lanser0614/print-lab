<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructorFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_constructor_fallback_is_not_available_outside_local(): void
    {
        $this->get(route('constructor.fallback'))->assertNotFound();
    }

    public function test_local_constructor_fallback_seeds_preview_product_and_redirects(): void
    {
        app()->detectEnvironment(fn (): string => 'local');

        $this->assertDatabaseCount('products', 0);

        $response = $this->get(route('constructor.fallback'));

        $response->assertRedirect(route('constructor.show', ['product' => 'classic-t-shirt']));
        $this->assertDatabaseHas('products', ['slug' => 'classic-t-shirt']);
    }
}
