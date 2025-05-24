<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OllamaChatbotService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register OllamaChatbotService as a singleton
        $this->app->singleton(OllamaChatbotService::class, function ($app) {
            return new OllamaChatbotService();
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
