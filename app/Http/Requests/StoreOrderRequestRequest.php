<?php

namespace App\Http\Requests;

use App\Support\DataUrlImage;
use App\Models\ProductVariant;
use App\Models\ProductPrintArea;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreOrderRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'customer_comment' => ['nullable', 'string', 'max:2000'],
            'customer_city' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string', 'max:2000'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'side' => ['required_without:designs', 'string', 'in:front,back'],
            'canvas_json' => ['required_without:designs', 'array'],
            'canvas_json.layers' => ['array'],
            'canvas_json.print_area' => ['required_with:canvas_json', 'array'],
            'preview_image' => ['required_without:designs', 'string'],
            'print_image' => ['required_without:designs', 'string'],
            'assets' => ['nullable', 'array'],
            'assets.*.layer_id' => ['required_with:assets', 'string'],
            'assets.*.file_name' => ['required_with:assets', 'string'],
            'assets.*.data' => ['required_with:assets', 'string'],
            'designs' => ['nullable', 'array', 'min:1', 'max:2'],
            'designs.*.side' => ['required_with:designs', 'string', 'in:front,back'],
            'designs.*.canvas_json' => ['required_with:designs', 'array'],
            'designs.*.canvas_json.layers' => ['present', 'array'],
            'designs.*.canvas_json.print_area' => ['required_with:designs.*.canvas_json', 'array'],
            'designs.*.preview_image' => ['required_with:designs', 'string'],
            'designs.*.print_image' => ['required_with:designs', 'string'],
            'designs.*.assets' => ['nullable', 'array'],
            'designs.*.assets.*.layer_id' => ['required_with:designs.*.assets', 'string'],
            'designs.*.assets.*.file_name' => ['required_with:designs.*.assets', 'string'],
            'designs.*.assets.*.data' => ['required_with:designs.*.assets', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $variant = ProductVariant::query()->find($this->integer('variant_id'));

                if (! $variant || $variant->product_id !== $this->integer('product_id')) {
                    $validator->errors()->add('variant_id', 'The selected variant does not belong to the product.');
                }

                $seenSides = [];

                $usesDesignsArray = is_array($this->input('designs')) && count($this->input('designs')) > 0;

                foreach ($this->designPayloads() as $designIndex => $design) {
                    $side = (string) ($design['side'] ?? '');
                    $fieldPrefix = $usesDesignsArray ? "designs.$designIndex." : '';

                    if (in_array($side, $seenSides, true)) {
                        $validator->errors()->add($fieldPrefix.'side', 'Each design side may be submitted only once.');
                    }
                    $seenSides[] = $side;

                    $printAreaExists = ProductPrintArea::query()
                        ->where('product_variant_id', $this->integer('variant_id'))
                        ->where('side', $side)
                        ->exists();

                    if (! $printAreaExists) {
                        $validator->errors()->add($fieldPrefix.'side', 'The selected product variant does not have a print area for this side.');
                    }

                    foreach (['preview_image', 'print_image'] as $field) {
                        try {
                            DataUrlImage::parse((string) ($design[$field] ?? ''), $fieldPrefix.$field);
                        } catch (ValidationException) {
                            $validator->errors()->add($fieldPrefix.$field, 'The image must be a valid PNG, JPEG, or WebP data URL.');
                        }
                    }

                    foreach (($design['assets'] ?? []) as $assetIndex => $asset) {
                        try {
                            DataUrlImage::parse((string) ($asset['data'] ?? ''), $fieldPrefix."assets.$assetIndex.data");
                        } catch (ValidationException) {
                            $validator->errors()->add($fieldPrefix."assets.$assetIndex.data", 'The asset must be a valid PNG, JPEG, or WebP data URL.');
                        }
                    }
                }
            },
        ];
    }

    /**
     * @return array<array-key, array<string, mixed>>
     */
    private function designPayloads(): array
    {
        $designs = $this->input('designs');

        if (is_array($designs) && count($designs) > 0) {
            return $designs;
        }

        return [[
            'side' => $this->input('side'),
            'preview_image' => $this->input('preview_image'),
            'print_image' => $this->input('print_image'),
            'assets' => $this->input('assets', []),
        ]];
    }
}
