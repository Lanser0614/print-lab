<?php

namespace Database\Factories;

use App\Models\GeneratedPrint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeneratedPrint>
 */
class GeneratedPrintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prompt' => fake()->sentence(),
            'reference_image_path' => null,
            'generated_image_path' => 'generated-prints/'.fake()->uuid().'.png',
            'model' => 'fake-image-generator',
            'status' => 'completed',
            'error_message' => null,
            'guest_fingerprint' => hash('sha256', fake()->uuid()),
            'metadata' => [],
        ];
    }
}
