<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
        $this->app['url']->forceScheme('https');

        // Set application locale to Arabic
        App::setLocale('ar');

        // Set Carbon locale to Arabic
        Carbon::setLocale('ar');
    }
    }
}
