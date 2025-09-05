<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Api\ProductController;
use Laravel\Passport\Http\Middleware\CheckToken;
use Laravel\Passport\Http\Middleware\CheckTokenForAnyScope;
// use App\Http\Controllers\TenantAuthController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Route::middleware([
//     'web',
//     InitializeTenancyByDomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     Route::get('/', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
// });
// Route::post('/tenantRegister', [TenantAuthController::class, 'register']);
// Route::post('/login', [TenantAuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware(CheckToken::using(['products.read']));

    Route::post('/products', [ProductController::class, 'store'])
        ->middleware(CheckTokenForAnyScope::using(['products.write', 'admin']));

    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->middleware(CheckToken::using(['products.read']));

    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware(CheckTokenForAnyScope::using(['products.write','admin']));

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware(CheckTokenForAnyScope::using(['products.write','admin']));
});
