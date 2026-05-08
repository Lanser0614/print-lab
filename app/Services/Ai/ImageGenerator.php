<?php

namespace App\Services\Ai;

interface ImageGenerator
{
    public function assertConfigured(): void;

    public function generate(string $prompt, ?string $referenceImageDataUrl = null): ImageGenerationResult;
}
