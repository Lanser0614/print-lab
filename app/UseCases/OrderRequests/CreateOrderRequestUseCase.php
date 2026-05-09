<?php

namespace App\UseCases\OrderRequests;

use App\DTO\OrderRequests\CreateOrderRequestData;
use App\Enums\OrderRequestStatus;
use App\Models\Design;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use App\Support\DataUrlImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class CreateOrderRequestUseCase
{
    public function execute(CreateOrderRequestData $data): OrderRequest
    {
        return DB::transaction(function () use ($data): OrderRequest {
            $product = Product::query()->findOrFail($data->productId);
            $variant = ProductVariant::query()
                ->where('product_id', $product->id)
                ->findOrFail($data->variantId);

            ProductPrintArea::query()
                ->where('product_variant_id', $variant->id)
                ->where('side', $data->side)
                ->firstOrFail();

            $orderRequest = OrderRequest::query()->create([
                'user_id' => $data->userId,
                'status' => OrderRequestStatus::New,
                'customer_name' => $data->customerName,
                'customer_phone' => $data->customerPhone,
                'customer_comment' => $data->customerComment,
                'customer_city' => $data->customerCity,
                'customer_address' => $data->customerAddress,
                'currency' => 'UZS',
            ]);

            $unitPrice = $product->base_price + $variant->price_modifier;

            $item = $orderRequest->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'product_name_snapshot' => $product->localizedName(),
                'product_type_snapshot' => $product->type,
                'color_snapshot' => $variant->color,
                'size_snapshot' => $variant->size,
                'quantity' => $data->quantity,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $data->quantity,
            ]);

            $design = $item->design()->create([
                'side' => $data->side,
                'canvas_json' => $data->canvasJson,
                'preview_image_path' => $this->storeDataUrl($data->previewImage, 'order-requests/previews'),
                'print_image_path' => $this->storeDataUrl($data->printImage, 'order-requests/prints'),
            ]);

            $this->storeAssets($design, $data->assets);
            $this->storeTextLayers($design, $data->canvasJson['layers'] ?? []);

            return $orderRequest->load('items.design.assets', 'items.design.textLayers');
        });
    }

    private function storeDataUrl(string $dataUrl, string $directory): string
    {
        $image = DataUrlImage::parse($dataUrl);
        $path = sprintf('%s/%s.%s', $directory, (string) str()->uuid(), $image->extension);

        Storage::disk('public')->put($path, $image->binary);

        return $path;
    }

    /**
     * @param  list<array<string, string>>  $assets
     */
    private function storeAssets(Design $design, array $assets): void
    {
        foreach ($assets as $asset) {
            $image = DataUrlImage::parse($asset['data']);
            $path = sprintf('order-requests/assets/%s.%s', (string) str()->uuid(), $image->extension);
            Storage::disk('public')->put($path, $image->binary);

            $design->assets()->create([
                'type' => 'uploaded_image',
                'original_file_path' => $path,
                'file_name' => $asset['file_name'] ?? null,
                'mime_type' => $image->mimeType,
                'size_bytes' => strlen($image->binary),
                'metadata' => ['layer_id' => $asset['layer_id'] ?? null],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $layers
     */
    private function storeTextLayers(Design $design, array $layers): void
    {
        foreach ($layers as $layer) {
            if (($layer['type'] ?? null) !== 'text') {
                continue;
            }

            $design->textLayers()->create([
                'layer_id' => (string) ($layer['id'] ?? ''),
                'text' => (string) ($layer['text'] ?? ''),
                'font_family' => $layer['fontFamily'] ?? $layer['font_family'] ?? null,
                'font_size' => $layer['fontSize'] ?? $layer['font_size'] ?? null,
                'color' => $layer['color'] ?? null,
                'x' => $layer['x'] ?? null,
                'y' => $layer['y'] ?? null,
                'scale' => $layer['scale'] ?? null,
                'rotation' => $layer['rotation'] ?? null,
            ]);
        }
    }
}
