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
            'products.read'  => 'Read products',
            'products.write' => 'Create/update/delete products',
            'view-products' => 'View products',
            'add-products' => 'Add new products',
            'admin'          => 'Admin privileges',
            'orders.read'    => 'Read orders',
            'tenant:*'       => 'Access specific tenant scope',
            'tenant:{tenant_id}' => 'Tenant specific scope',
        ]);

        // Passport::setDefaultScope(['orders.read']);
    }
}
