<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\TenantAuthController;
use App\Http\Controllers\UserController;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Auth With Middlewate Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/userpermission', [AuthController::class, 'userpermission']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Tenant Routes
Route::prefix('/tenant')->group(function () {
    Route::post('/register', [TenantAuthController::class, 'register']);
    Route::post('/login', [TenantAuthController::class, 'login']);
    Route::post('/me', [TenantAuthController::class, 'me']);
    Route::post('/userpermission', [TenantAuthController::class, 'userpermission']);
    Route::post('/logout', [TenantAuthController::class, 'logout']);
});

// Products
Route::middleware([
    'api',
    'tenant',
    'auth:api',
])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('scopes:view-products');

    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('scopes:add-products');
});
