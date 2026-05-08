<?php

namespace Tests\Feature;

use App\Models\GeneratedPrint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratedPrintApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_required_prompt(): void
    {
        $this->postJson('/api/generated-prints', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['prompt']);
    }

    public function test_it_rejects_prompt_longer_than_250_characters(): void
    {
        $this->postJson('/api/generated-prints', [
            'prompt' => str_repeat('a', 251),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['prompt']);
    }

    public function test_it_rejects_invalid_reference_image(): void
    {
        $this->postJson('/api/generated-prints', [
            'prompt' => 'Minimal logo for a coffee shop',
            'reference_image' => 'not-an-image',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['reference_image']);
    }

    public function test_fake_driver_generates_image_without_openai_key(): void
    {
        Storage::fake('public');
        Http::preventStrayRequests();
        config([
            'services.openai.image_driver' => 'fake',
            'services.openai.api_key' => null,
        ]);

        $response = $this->postJson('/api/generated-prints', [
            'prompt' => 'Minimal streetwear logo',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.model', 'fake-image-generator')
            ->assertJsonPath('data.asset.type', 'generated_image');

        $generatedPrint = GeneratedPrint::query()->firstOrFail();

        $this->assertSame('completed', $generatedPrint->status);
        $this->assertNotNull($generatedPrint->generated_image_path);
        Storage::disk('public')->assertExists($generatedPrint->generated_image_path);
    }

    public function test_guest_user_can_generate_only_two_successful_images_per_day(): void
    {
        Storage::fake('public');
        config(['services.openai.image_driver' => 'fake']);
        Carbon::setTestNow('2026-05-08 10:00:00');

        GeneratedPrint::factory()->count(2)->create([
            'status' => 'completed',
            'guest_fingerprint' => GeneratedPrint::guestFingerprint('127.0.0.1', 'PrintLabTestAgent', Carbon::today()),
            'created_at' => Carbon::now(),
        ]);

        $this->withHeaders(['User-Agent' => 'PrintLabTestAgent'])
            ->postJson('/api/generated-prints', [
                'prompt' => 'Third daily print',
            ])
            ->assertTooManyRequests()
            ->assertJsonPath('message', 'Daily AI print generation limit reached.');
    }

    public function test_failed_and_yesterday_generations_do_not_count_toward_guest_daily_limit(): void
    {
        Storage::fake('public');
        config(['services.openai.image_driver' => 'fake']);
        Carbon::setTestNow('2026-05-08 10:00:00');

        $fingerprint = GeneratedPrint::guestFingerprint('127.0.0.1', 'PrintLabTestAgent', Carbon::today());

        GeneratedPrint::factory()->create([
            'status' => 'failed',
            'guest_fingerprint' => $fingerprint,
            'created_at' => Carbon::now(),
        ]);

        GeneratedPrint::factory()->create([
            'status' => 'completed',
            'guest_fingerprint' => GeneratedPrint::guestFingerprint('127.0.0.1', 'PrintLabTestAgent', Carbon::yesterday()),
            'created_at' => Carbon::yesterday(),
        ]);

        $this->withHeaders(['User-Agent' => 'PrintLabTestAgent'])
            ->postJson('/api/generated-prints', [
                'prompt' => 'Allowed daily print',
            ])
            ->assertCreated();
    }

    public function test_openai_driver_generates_image_with_mocked_response(): void
    {
        Storage::fake('public');
        config([
            'services.openai.image_driver' => 'openai',
            'services.openai.api_key' => 'test-key',
            'services.openai.image_model' => 'gpt-image-1-mini',
        ]);

        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [
                    ['b64_json' => $this->fakeBase64PngPayload()],
                ],
                'output_format' => 'png',
                'usage' => ['total_tokens' => 42],
            ], 200),
        ]);

        $response = $this->postJson('/api/generated-prints', [
            'prompt' => 'Minimal logo for PrintLab',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', 'completed')
            ->assertJsonPath('data.model', 'gpt-image-1-mini');

        $generatedPrint = GeneratedPrint::query()->firstOrFail();

        Storage::disk('public')->assertExists($generatedPrint->generated_image_path);
        Http::assertSent(fn ($request) => $request->url() === 'https://api.openai.com/v1/images/generations'
            && $request['model'] === 'gpt-image-1-mini'
            && $request['prompt'] === 'Minimal logo for PrintLab');
    }

    public function test_reference_image_is_stored_and_sent_to_openai_edit_endpoint(): void
    {
        Storage::fake('public');
        config([
            'services.openai.image_driver' => 'openai',
            'services.openai.api_key' => 'test-key',
            'services.openai.image_model' => 'gpt-image-1-mini',
        ]);

        Http::fake([
            'api.openai.com/v1/images/edits' => Http::response([
                'data' => [
                    ['b64_json' => $this->fakeBase64PngPayload()],
                ],
                'output_format' => 'png',
            ], 200),
        ]);

        $referenceImage = $this->fakeBase64Png();

        $this->postJson('/api/generated-prints', [
            'prompt' => 'Use this logo as reference',
            'reference_image' => $referenceImage,
        ])->assertCreated();

        $generatedPrint = GeneratedPrint::query()->firstOrFail();

        $this->assertNotNull($generatedPrint->reference_image_path);
        Storage::disk('public')->assertExists($generatedPrint->reference_image_path);
        Http::assertSent(fn ($request) => $request->url() === 'https://api.openai.com/v1/images/edits'
            && $request['prompt'] === 'Use this logo as reference'
            && $request['images'][0]['image_url'] === $referenceImage);
    }

    public function test_openai_driver_requires_api_key(): void
    {
        Storage::fake('public');
        Http::preventStrayRequests();
        config([
            'services.openai.image_driver' => 'openai',
            'services.openai.api_key' => null,
        ]);

        $this->postJson('/api/generated-prints', [
            'prompt' => 'Minimal streetwear logo',
        ])->assertServiceUnavailable()
            ->assertJsonPath('message', 'AI print generation is not configured.');

        $this->assertSame(0, GeneratedPrint::query()->count());
    }

    public function test_openai_failure_returns_bad_gateway_and_saves_failed_record(): void
    {
        Storage::fake('public');
        config([
            'services.openai.image_driver' => 'openai',
            'services.openai.api_key' => 'test-key',
            'services.openai.image_model' => 'gpt-image-1-mini',
        ]);

        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response(['error' => ['message' => 'upstream failed']], 500),
        ]);

        $this->postJson('/api/generated-prints', [
            'prompt' => 'Minimal logo',
        ])->assertStatus(502)
            ->assertJsonPath('message', 'AI print generation failed.');

        $generatedPrint = GeneratedPrint::query()->firstOrFail();

        $this->assertSame('failed', $generatedPrint->status);
        $this->assertNull($generatedPrint->generated_image_path);
        $this->assertNotNull($generatedPrint->error_message);
    }

    private function fakeBase64Png(): string
    {
        return 'data:image/png;base64,'.$this->fakeBase64PngPayload();
    }

    private function fakeBase64PngPayload(): string
    {
        return base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p94AAAAASUVORK5CYII='
        ));
    }
}
