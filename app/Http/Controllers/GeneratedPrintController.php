<?php

namespace App\Http\Controllers;

use App\Exceptions\AiImageGenerationFailed;
use App\Exceptions\AiImageGenerationNotConfigured;
use App\Http\Requests\StoreGeneratedPrintRequest;
use App\UseCases\GeneratedPrints\CreateGeneratedPrintUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GeneratedPrintController extends Controller
{
    public function store(StoreGeneratedPrintRequest $request, CreateGeneratedPrintUseCase $useCase): JsonResponse
    {
        try {
            $generatedPrint = $useCase->execute(
                prompt: $request->string('prompt')->toString(),
                referenceImageDataUrl: $request->input('reference_image'),
                request: $request,
            );
        } catch (AiImageGenerationNotConfigured) {
            return response()->json([
                'message' => 'AI print generation is not configured.',
            ], 503);
        } catch (AiImageGenerationFailed) {
            return response()->json([
                'message' => 'AI print generation failed.',
            ], 502);
        }

        $imageUrl = $generatedPrint->generated_image_path
            ? Storage::disk('public')->url($generatedPrint->generated_image_path)
            : null;

        return response()->json([
            'data' => [
                'id' => $generatedPrint->id,
                'status' => $generatedPrint->status,
                'prompt' => $generatedPrint->prompt,
                'model' => $generatedPrint->model,
                'image_url' => $imageUrl,
                'asset' => [
                    'type' => 'generated_image',
                    'file_name' => $generatedPrint->id ? "ai-print-{$generatedPrint->id}.png" : 'ai-print.png',
                    'mime_type' => $generatedPrint->metadata['mime_type'] ?? 'image/png',
                ],
            ],
        ], 201);
    }
}
