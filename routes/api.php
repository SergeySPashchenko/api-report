<?php

declare(strict_types=1);

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\OrderController;
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
        Route::get('/expense-types', [ExpenseTypeController::class, 'getExpenseTypes'])
            ->name('expense_types.list');
        Route::get('/sync-expense-types', [ExpenseTypeController::class, 'syncExpenseTypes'])
            ->name('expense_types.sync');
        Route::get('/expenses', [ExpensesController::class, 'getExpenses'])
            ->name('expenses.list');
        Route::get('/sync-expenses', [ExpensesController::class, 'syncExpenses'])
            ->name('expenses.sync');
        Route::get('/orders', [OrderController::class, 'getOrders'])
            ->name('orders.list');
        Route::get('/sync-orders', [OrderController::class, 'syncOrders'])
            ->name('orders.sync');
    });
    Route::prefix('brands')->name('brands.')->group(function (): void {
        Route::get('/', [BrandController::class, 'index'])
            ->name('brands.index');
        Route::post('/', [BrandController::class, 'store'])
            ->name('brands.store');
        Route::get('/{brand:slug}', [BrandController::class, 'show'])
            ->name('brands.show');
        Route::put('/{brand:slug}', [BrandController::class, 'update'])
            ->name('brands.update');
        Route::delete('/{brand:slug}', [BrandController::class, 'destroy'])
            ->name('brands.destroy');
        Route::get('/{brand:slug}/products', [BrandController::class, 'brandProducts'])
            ->name('brands.brand.products');
    });
    Route::prefix('products')->name('products.')->group(function (): void {
        Route::get('/', [ProductController::class, 'index'])
            ->name('products.index');
        Route::post('/', [ProductController::class, 'store'])
            ->name('products.store');
        Route::get('/{product:slug}', [ProductController::class, 'show'])
            ->name('products.show');
        Route::put('/{product:slug}', [ProductController::class, 'update'])
            ->name('products.update');
        Route::get('/{product:slug}/expenses', [ProductController::class, 'productExpenses'])
            ->name('expenses');
        Route::delete('/{product:slug}', [ProductController::class, 'destroy'])
            ->name('products.destroy');
    });
    Route::prefix('expense-types')->name('expense_types.')->group(function (): void {
        Route::get('/', [ExpenseTypeController::class, 'index'])
            ->name('expense_types.index');
        Route::post('/', [ExpenseTypeController::class, 'store'])
            ->name('expense_types.store');
        Route::get('/{expense_type:slug}', [ExpenseTypeController::class, 'show'])
            ->name('expense_types.show');
        Route::put('/{expense_type:slug}', [ExpenseTypeController::class, 'update'])
            ->name('expense_types.update');
        Route::delete('/{expense_type:slug}', [ExpenseTypeController::class, 'destroy'])
            ->name('expense_types.destroy');
    });
    Route::prefix('expenses')->name('expenses.')->group(function (): void {
        Route::get('/', [ExpensesController::class, 'index'])
            ->name('expenses.index');
        Route::post('/', [ExpensesController::class, 'store'])
            ->name('expenses.store');
        Route::get('/{expense:slug}', [ExpensesController::class, 'show'])
            ->name('expenses.show');
        Route::put('/{expense:slug}', [ExpensesController::class, 'update'])
            ->name('expenses.update');
        Route::delete('/{expense:slug}', [ExpensesController::class, 'destroy'])
            ->name('expenses.destroy');
    });
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('addresses', AddressController::class);
});
