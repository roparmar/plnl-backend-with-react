<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;

// Issue Oauth access token (login)
Route::post('/oauth/token', [AccessTokenController::class, 'issueToken'])->name('passport.token');

// CSRF token refresh for SPA
Route::middleware(['web'])
    ->get('/oauth/token/refresh', [TransientTokenController::class, 'refresh'])
    ->name('passport.token.refresh');

// Personal Access Tokens (auth:api middleware)
Route::middleware(['auth:api'])->group(function () {
    Route::get('/oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'forUser'])->name('passport.personal.tokens');
    Route::post('/oauth/personal-access-tokens', [PersonalAccessTokenController::class, 'store'])->name('passport.personal.tokens.store');
    Route::delete('/oauth/personal-access-tokens/{token_id}', [PersonalAccessTokenController::class, 'destroy'])->name('passport.personal.tokens.destroy');
});
