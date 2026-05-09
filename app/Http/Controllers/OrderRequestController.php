<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
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
