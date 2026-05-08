<?php

namespace App\Services\Ai;

class FakeImageGenerator implements ImageGenerator
{
    public function assertConfigured(): void
    {
        //
    }

    public function generate(string $prompt, ?string $referenceImageDataUrl = null): ImageGenerationResult
    {
        return new ImageGenerationResult(
            binary: base64_decode($this->base64Png(), true) ?: '',
            mimeType: 'image/png',
            extension: 'png',
            model: 'fake-image-generator',
            metadata: [
                'driver' => 'fake',
                'reference_image_provided' => $referenceImageDataUrl !== null,
            ],
        );
    }

    private function base64Png(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p94AAAAASUVORK5CYII=';
    }
}
