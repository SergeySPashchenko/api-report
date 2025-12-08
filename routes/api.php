<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])
    ->name('auth.login');
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('auth.logout');
    Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()))
        ->name('user.current');
    Route::prefix('external')->name('external.')->group(function (): void {
        Route::get('/brands', [BrandController::class, 'getBrands'])
            ->name('brands.list');
        Route::get('/sync-brands', [BrandController::class, 'syncBrands'])
            ->name('brands.sync');
    });

});
