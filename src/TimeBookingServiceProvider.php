<?php

namespace Weboldalnet\TimeBooking;

use Illuminate\Support\ServiceProvider;

class TimeBookingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'timebooking');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views/admin' => resource_path('views/admin'),
            __DIR__.'/../resources/views/site' => resource_path('views/site'),
        ], 'timebooking-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../public' => public_path('timebooking'),
        ], 'timebooking-assets');

        // Publish all
        $this->publishes([
            __DIR__.'/../resources/views/admin' => resource_path('views/admin'),
            __DIR__.'/../resources/views/site' => resource_path('views/site'),
            __DIR__.'/../public' => public_path('timebooking'),
        ], 'timebooking-all');
    }

    public function register()
    {
        // Register any application services
    }
}
