<?php

namespace App\UseCases\ReadyPrints;

use App\Models\Design;
use App\Models\ReadyPrint;
use Illuminate\Support\Str;
use App\Models\OrderRequest;
use App\Enums\OrderRequestStatus;

class CreateReadyPrintFromOrderRequestUseCase
{
    public function handle(OrderRequest $orderRequest): ?ReadyPrint
    {
        $orderRequest->loadMissing('items.designs');

        if ($orderRequest->status !== OrderRequestStatus::Ready) {
            return null;
        }

        $design = $this->firstPublishableDesign($orderRequest);

        if (! $design) {
            return null;
        }

        $existing = ReadyPrint::query()
            ->where('source_design_id', $design->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $item = $design->item;
        $title = trim(($item?->product_name_snapshot ?: 'PrintLab').' #'.$orderRequest->id);

        return ReadyPrint::query()->create([
            'order_request_id' => $orderRequest->id,
            'source_design_id' => $design->id,
            'title' => $title,
            'title_translations' => [
                'ru' => $title,
                'uz' => $title,
            ],
            'slug' => $this->uniqueSlug($orderRequest, $design),
            'image_path' => $design->preview_image_path,
            'is_active' => true,
        ]);
    }

    private function firstPublishableDesign(OrderRequest $orderRequest): ?Design
    {
        foreach ($orderRequest->items as $item) {
            foreach ($item->designs as $design) {
                if (is_string($design->preview_image_path) && trim($design->preview_image_path) !== '') {
                    return $design;
                }
            }
        }

        return null;
    }

    private function uniqueSlug(OrderRequest $orderRequest, Design $design): string
    {
        $base = sprintf('order-request-%d-design-%d', $orderRequest->id, $design->id);

        if (! ReadyPrint::query()->where('slug', $base)->exists()) {
            return $base;
        }

        return $base.'-'.Str::lower(Str::random(6));
    }
}
