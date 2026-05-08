<?php

namespace App\Services\Ai;

final readonly class ImageGenerationResult
{
    public function __construct(
        public string $binary,
        public string $mimeType,
        public string $extension,
        public string $model,
        public array $metadata = [],
    ) {}
}
