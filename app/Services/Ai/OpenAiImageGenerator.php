<?php

namespace App\Services\Ai;

use App\Exceptions\AiImageGenerationFailed;
use App\Exceptions\AiImageGenerationNotConfigured;
use Illuminate\Support\Facades\Http;

class OpenAiImageGenerator implements ImageGenerator
{
    public function assertConfigured(): void
    {
        if (! config('services.openai.api_key')) {
            throw new AiImageGenerationNotConfigured('AI print generation is not configured.');
        }
    }

    public function generate(string $prompt, ?string $referenceImageDataUrl = null): ImageGenerationResult
    {
        $this->assertConfigured();

        $model = (string) config('services.openai.image_model', 'gpt-image-1-mini');
        $endpoint = $referenceImageDataUrl
            ? 'https://api.openai.com/v1/images/edits'
            : 'https://api.openai.com/v1/images/generations';

        $payload = [
            'model' => $model,
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'quality' => 'medium',
            'output_format' => 'png',
        ];

        if ($referenceImageDataUrl) {
            $payload['images'] = [
                ['image_url' => $referenceImageDataUrl],
            ];
        }

        $response = Http::withToken((string) config('services.openai.api_key'))
            ->asJson()
            ->post($endpoint, $payload);

        if ($response->failed()) {
            throw new AiImageGenerationFailed('OpenAI image request failed with status '.$response->status());
        }

        $base64 = $response->json('data.0.b64_json');

        if (! is_string($base64) || $base64 === '') {
            throw new AiImageGenerationFailed('OpenAI image response did not include b64_json.');
        }

        $binary = base64_decode($base64, true);

        if ($binary === false || $binary === '') {
            throw new AiImageGenerationFailed('OpenAI image response contained invalid base64.');
        }

        return new ImageGenerationResult(
            binary: $binary,
            mimeType: 'image/png',
            extension: 'png',
            model: $model,
            metadata: [
                'driver' => 'openai',
                'endpoint' => $endpoint,
                'output_format' => $response->json('output_format'),
                'usage' => $response->json('usage'),
            ],
        );
    }
}
