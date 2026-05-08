<?php

namespace App\Http\Requests;

use App\Support\DataUrlImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class StoreGeneratedPrintRequest extends FormRequest
{
    private const MAX_REFERENCE_IMAGE_BYTES = 5 * 1024 * 1024;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'max:250'],
            'reference_image' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty() || ! $this->filled('reference_image')) {
                    return;
                }

                try {
                    $image = DataUrlImage::parse($this->string('reference_image')->toString(), 'reference_image');
                } catch (ValidationException) {
                    $validator->errors()->add('reference_image', 'The reference image must be a valid PNG, JPEG, or WebP data URL.');

                    return;
                }

                if (strlen($image->binary) > self::MAX_REFERENCE_IMAGE_BYTES) {
                    $validator->errors()->add('reference_image', 'The reference image may not be greater than 5 MB.');
                }
            },
        ];
    }
}
