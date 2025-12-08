<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
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
        Route::get('/products', [ProductController::class, 'getProducts'])
            ->name('products.list');
        Route::get('/sync-products', [ProductController::class, 'syncProducts'])
            ->name('products.sync');
    });
    Route::prefix('brands')->name('brands.')->group(function (): void {
        Route::get('/', [BrandController::class, 'index'])
            ->name('brands.index');
        Route::post('/', [BrandController::class, 'store'])
            ->name('brands.store');
        Route::get('/:slug', [BrandController::class, 'show'])
            ->name('brands.show');
        Route::put('/:slug', [BrandController::class, 'update'])
            ->name('brands.update');
        Route::delete('/:slug', [BrandController::class, 'destroy'])
            ->name('brands.destroy');
    });
    Route::prefix('products')->name('products.')->group(function (): void {
        Route::get('/', [BrandController::class, 'index'])
            ->name('products.index');
        Route::post('/', [BrandController::class, 'store'])
            ->name('products.store');
        Route::get('/:slug', [BrandController::class, 'show'])
            ->name('products.show');
        Route::put('/:slug', [BrandController::class, 'update'])
            ->name('products.update');
        Route::delete('/:slug', [BrandController::class, 'destroy'])
            ->name('products.destroy');
    });

});
