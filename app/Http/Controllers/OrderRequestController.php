<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreOrderRequestRequest;
use App\DTO\OrderRequests\CreateOrderRequestData;
use App\UseCases\OrderRequests\CreateOrderRequestUseCase;

class OrderRequestController extends Controller
{
    public function store(
        StoreOrderRequestRequest $request,
        CreateOrderRequestUseCase $useCase,
    ): JsonResponse|RedirectResponse {
        Log::debug('store', [
            'product_id' => $request->input('product_id'),
            'variant_id' => $request->input('variant_id'),
            'designs' => collect($request->input('designs') ?: [[
                'side' => $request->input('side'),
                'canvas_json' => $request->input('canvas_json', []),
                'assets' => $request->input('assets', []),
            ]])->map(fn (array $design): array => [
                'side' => $design['side'] ?? null,
                'layer_count' => count($design['canvas_json']['layers'] ?? []),
                'asset_count' => count($design['assets'] ?? []),
            ])->values()->all(),
        ]);

        $orderRequest = $useCase->execute(
            CreateOrderRequestData::fromValidated(
                $request->validated(),
                userId: auth()->id(),
            ),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $orderRequest->id,
                    'status' => $orderRequest->status->value,
                ],
            ], 201);
        }

        return redirect()->route('home');
    }

    public function success(): View
    {
        return view('order-requests.success');
    }
}
