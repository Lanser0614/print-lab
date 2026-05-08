<?php

namespace App\Providers;

use App\Services\Ai\FakeImageGenerator;
use App\Services\Ai\ImageGenerator;
use App\Services\Ai\OpenAiImageGenerator;
use Illuminate\Support\ServiceProvider;

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
