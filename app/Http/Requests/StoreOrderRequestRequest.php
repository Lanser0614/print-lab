<?php

namespace App\Http\Requests;

use App\Models\ProductPrintArea;
use App\Models\ProductVariant;
use App\Support\DataUrlImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'side' => ['required', 'string', 'in:front,back'],
            'canvas_json' => ['required', 'array'],
            'canvas_json.layers' => ['required', 'array'],
            'canvas_json.print_area' => ['required', 'array'],
            'preview_image' => ['required', 'string'],
            'print_image' => ['required', 'string'],
            'assets' => ['nullable', 'array'],
            'assets.*.layer_id' => ['required_with:assets', 'string'],
            'assets.*.file_name' => ['required_with:assets', 'string'],
            'assets.*.data' => ['required_with:assets', 'string'],
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

                $printAreaExists = ProductPrintArea::query()
                    ->where('product_variant_id', $this->integer('variant_id'))
                    ->where('side', $this->string('side')->toString())
                    ->exists();

                if (! $printAreaExists) {
                    $validator->errors()->add('side', 'The selected product variant does not have a print area for this side.');
                }

                foreach (['preview_image', 'print_image'] as $field) {
                    try {
                        DataUrlImage::parse($this->string($field)->toString(), $field);
                    } catch (\Illuminate\Validation\ValidationException) {
                        $validator->errors()->add($field, 'The image must be a valid PNG, JPEG, or WebP data URL.');
                    }
                }

                foreach ($this->input('assets', []) as $index => $asset) {
                    try {
                        DataUrlImage::parse((string) ($asset['data'] ?? ''), "assets.$index.data");
                    } catch (\Illuminate\Validation\ValidationException) {
                        $validator->errors()->add("assets.$index.data", 'The asset must be a valid PNG, JPEG, or WebP data URL.');
                    }
                }
            },
        ];
    }
}
