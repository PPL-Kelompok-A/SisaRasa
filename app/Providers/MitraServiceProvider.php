<?php

namespace App\Providers;

use App\Http\Middleware\MitraMiddleware;
use Illuminate\Support\ServiceProvider;

class MitraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('mitra', function ($app) {
            return new MitraMiddleware();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
