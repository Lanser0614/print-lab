<?php

namespace App\DTO\OrderRequests;

final readonly class CreateOrderRequestData
{
    /**
     * @param  array<string, mixed>  $canvasJson
     * @param  list<array<string, string>>  $assets
     */
    public function __construct(
        public string $customerName,
        public string $customerPhone,
        public ?string $customerComment,
        public ?string $customerCity,
        public ?string $customerAddress,
        public int $productId,
        public int $variantId,
        public int $quantity,
        public string $side,
        public array $canvasJson,
        public string $previewImage,
        public string $printImage,
        public array $assets,
        public ?int $userId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated, ?int $userId = null): self
    {
        return new self(
            customerName: $validated['customer_name'],
            customerPhone: $validated['customer_phone'],
            customerComment: $validated['customer_comment'] ?? null,
            customerCity: $validated['customer_city'] ?? null,
            customerAddress: $validated['customer_address'] ?? null,
            productId: (int) $validated['product_id'],
            variantId: (int) $validated['variant_id'],
            quantity: (int) $validated['quantity'],
            side: $validated['side'],
            canvasJson: $validated['canvas_json'],
            previewImage: $validated['preview_image'],
            printImage: $validated['print_image'],
            assets: $validated['assets'] ?? [],
            userId: $userId,
        );
    }
}
