<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{   
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensCan([
            'users.read'    => 'Read users',
            'users.write'   => 'Create/Update users',
            'orders.read'   => 'Read orders',
            'orders.write'  => 'Create/Update orders',
        ]);

        Passport::setDefaultScope(['orders.read']);
    }
}
