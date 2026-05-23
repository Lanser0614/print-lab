<?php

namespace App\Providers;

use App\Services\Ai\ImageGenerator;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS URL generation in production. We sit behind a reverse proxy
        // (host Nginx + Certbot) that terminates TLS, so PHP sees plain HTTP.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
