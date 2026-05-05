<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final readonly class DataUrlImage
{
    public function __construct(
        public string $mimeType,
        public string $extension,
        public string $binary,
    ) {}

    /**
     * @throws ValidationException
     */
    public static function parse(string $value, string $field = 'image'): self
    {
        if (! preg_match('/^data:(image\/(?:png|jpeg|jpg|webp));base64,([A-Za-z0-9+\/=\r\n]+)$/', $value, $matches)) {
            throw ValidationException::withMessages([
                $field => 'The image must be a PNG, JPEG, or WebP data URL.',
            ]);
        }

        $binary = base64_decode(str_replace(["\r", "\n"], '', $matches[2]), true);

        if ($binary === false || $binary === '') {
            throw ValidationException::withMessages([
                $field => 'The image payload is not valid base64.',
            ]);
        }

        $mimeType = $matches[1] === 'image/jpg' ? 'image/jpeg' : $matches[1];

        return new self(
            mimeType: $mimeType,
            extension: match ($mimeType) {
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
            },
            binary: $binary,
        );
    }
}
