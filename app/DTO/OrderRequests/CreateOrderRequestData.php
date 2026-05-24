<?php

namespace App\DTO\OrderRequests;

final readonly class CreateOrderRequestData
{
    /**
     * @param array<string, mixed>        $canvasJson
     * @param list<array<string, string>> $assets
     * @param list<array{
     *     side: string,
     *     canvas_json: array<string, mixed>,
     *     preview_image: string,
     *     print_image: string,
     *     assets: list<array<string, string>>
     * }> $designs
     */
    public function __construct(
        public string $customerName,
        public string $customerPhone,
        public ?string $customerComment,
        public ?string $customerCity,
        public ?string $customerAddress,
        public float $deliveryLat,
        public float $deliveryLng,
        public int $productId,
        public int $variantId,
        public int $quantity,
        public string $side,
        public array $canvasJson,
        public string $previewImage,
        public string $printImage,
        public array $assets,
        public array $designs,
        public ?int $userId = null,
    ) {}

    /**
     * @param array<string, mixed> $validated
     */
    public static function fromValidated(array $validated, ?int $userId = null): self
    {
        $designs = $validated['designs'] ?? [[
            'side' => $validated['side'],
            'canvas_json' => $validated['canvas_json'],
            'preview_image' => $validated['preview_image'],
            'print_image' => $validated['print_image'],
            'assets' => $validated['assets'] ?? [],
        ]];

        $designs = array_values(array_map(static fn (array $design): array => [
            'side' => $design['side'],
            'canvas_json' => $design['canvas_json'],
            'preview_image' => $design['preview_image'],
            'print_image' => $design['print_image'],
            'assets' => $design['assets'] ?? [],
        ], $designs));

        $firstDesign = $designs[0];

        return new self(
            customerName: $validated['customer_name'],
            customerPhone: $validated['customer_phone'],
            customerComment: $validated['customer_comment'] ?? null,
            customerCity: $validated['customer_city'] ?? null,
            customerAddress: $validated['customer_address'] ?? null,
            deliveryLat: (float) $validated['delivery_lat'],
            deliveryLng: (float) $validated['delivery_lng'],
            productId: (int) $validated['product_id'],
            variantId: (int) $validated['variant_id'],
            quantity: (int) $validated['quantity'],
            side: $firstDesign['side'],
            canvasJson: $firstDesign['canvas_json'],
            previewImage: $firstDesign['preview_image'],
            printImage: $firstDesign['print_image'],
            assets: $firstDesign['assets'],
            designs: $designs,
            userId: $userId,
        );
    }
}
