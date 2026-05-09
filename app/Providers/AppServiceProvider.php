<?php

namespace App\Providers;

use App\Services\Ai\ImageGenerator;
use App\Services\Ai\FakeImageGenerator;
use Illuminate\Support\ServiceProvider;
use App\Services\Ai\OpenAiImageGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageGenerator::class, function (): ImageGenerator {
            return config('services.openai.image_driver') === 'openai'
                ? new OpenAiImageGenerator
                : new FakeImageGenerator;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
