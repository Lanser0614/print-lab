<?php

namespace Tests\Feature;

use Tests\TestCase;

class GoogleTagManagerConfigTest extends TestCase
{
    public function test_gtm_container_id_defaults_to_null(): void
    {
        config()->set('services.gtm.container_id', env('GTM_CONTAINER_ID'));

        $this->assertNull(config('services.gtm.container_id'));
    }

    public function test_gtm_container_id_reads_env_value(): void
    {
        $expected = 'GTM-PQSQLKKL';

        config()->set('services.gtm.container_id', $expected);

        $this->assertSame($expected, config('services.gtm.container_id'));
    }

    public function test_env_example_documents_gtm_container_id(): void
    {
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertIsString($envExample);
        $this->assertMatchesRegularExpression('/^GTM_CONTAINER_ID=\s*$/m', $envExample);
    }

    public function test_services_config_exposes_gtm_container_id_key(): void
    {
        $config = require base_path('config/services.php');

        $this->assertArrayHasKey('gtm', $config);
        $this->assertArrayHasKey('container_id', $config['gtm']);
    }
}
