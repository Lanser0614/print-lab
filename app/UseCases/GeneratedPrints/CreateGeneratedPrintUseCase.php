<?php

namespace App\UseCases\GeneratedPrints;

use App\Exceptions\AiImageGenerationFailed;
use App\Models\GeneratedPrint;
use App\Services\Ai\ImageGenerator;
use App\Support\DataUrlImage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

final readonly class CreateGeneratedPrintUseCase
{
    private const DAILY_GUEST_LIMIT = 2;

    public function __construct(
        private ImageGenerator $generator,
    ) {}

    public function execute(string $prompt, ?string $referenceImageDataUrl, Request $request): GeneratedPrint
    {
        $this->generator->assertConfigured();

        $fingerprint = GeneratedPrint::guestFingerprint(
            (string) $request->ip(),
            (string) $request->userAgent(),
            Carbon::today(),
        );

        if ($this->dailyCompletedCount($fingerprint) >= self::DAILY_GUEST_LIMIT) {
            abort(response()->json([
                'message' => 'Daily AI print generation limit reached.',
            ], 429));
        }

        $referenceImagePath = $referenceImageDataUrl
            ? $this->storeReferenceImage($referenceImageDataUrl)
            : null;

        try {
            $result = $this->generator->generate($prompt, $referenceImageDataUrl);
        } catch (AiImageGenerationFailed $exception) {
            GeneratedPrint::query()->create([
                'prompt' => $prompt,
                'reference_image_path' => $referenceImagePath,
                'generated_image_path' => null,
                'model' => (string) config('services.openai.image_model', 'gpt-image-1-mini'),
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'guest_fingerprint' => $fingerprint,
                'metadata' => ['driver' => config('services.openai.image_driver', 'fake')],
            ]);

            throw $exception;
        }

        $generatedImagePath = sprintf(
            'generated-prints/%s.%s',
            (string) str()->uuid(),
            $result->extension,
        );

        Storage::disk('public')->put($generatedImagePath, $result->binary);

        return GeneratedPrint::query()->create([
            'prompt' => $prompt,
            'reference_image_path' => $referenceImagePath,
            'generated_image_path' => $generatedImagePath,
            'model' => $result->model,
            'status' => 'completed',
            'error_message' => null,
            'guest_fingerprint' => $fingerprint,
            'metadata' => array_merge($result->metadata, [
                'mime_type' => $result->mimeType,
                'size_bytes' => strlen($result->binary),
            ]),
        ]);
    }

    private function dailyCompletedCount(string $fingerprint): int
    {
        return GeneratedPrint::query()
            ->where('guest_fingerprint', $fingerprint)
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    private function storeReferenceImage(string $dataUrl): string
    {
        $image = DataUrlImage::parse($dataUrl, 'reference_image');
        $path = sprintf(
            'generated-prints/references/%s.%s',
            (string) str()->uuid(),
            $image->extension,
        );

        Storage::disk('public')->put($path, $image->binary);

        return $path;
    }
}
