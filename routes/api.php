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
use App\Http\Controllers\ProductItemController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()))->name('user.current');

    /*
    |--------------------------------------------------------------------------
    | External API Sync Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('external')->name('external.')->group(function (): void {
        // Brands
        Route::get('/brands', [BrandController::class, 'getBrands'])->name('brands.list');
        Route::get('/sync-brands', [BrandController::class, 'syncBrands'])->name('brands.sync');

        // Products
        Route::get('/products', [ProductController::class, 'getProducts'])->name('products.list');
        Route::get('/sync-products', [ProductController::class, 'syncProducts'])->name('products.sync');

        // Expense Types
        Route::get('/expense-types', [ExpenseTypeController::class, 'getExpenseTypes'])->name('expense_types.list');
        Route::get('/sync-expense-types', [ExpenseTypeController::class, 'syncExpenseTypes'])->name('expense_types.sync');

        // Expenses
        Route::get('/expenses', [ExpensesController::class, 'getExpenses'])->name('expenses.list');
        Route::get('/sync-expenses', [ExpensesController::class, 'syncExpenses'])->name('expenses.sync');

        // Orders
        Route::get('/orders', [OrderController::class, 'getOrders'])->name('orders.list');
        Route::get('/sync-orders', [OrderController::class, 'syncOrders'])->name('orders.sync');

        // ProductsItems
        Route::get('/products-items/', [ProductItemController::class, 'getProductsItems'])->name('products.list');
        Route::get('/sync-products-items', [ProductItemController::class, 'syncProducts'])->name('products.sync');
    });

    /*
    |--------------------------------------------------------------------------
    | Resource Routes
    |--------------------------------------------------------------------------
    */

    // Brands
    Route::prefix('brands')->name('brands.')->group(function (): void {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::post('/', [BrandController::class, 'store'])->name('store');
        Route::get('/{brand:slug}', [BrandController::class, 'show'])->name('show');
        Route::put('/{brand:slug}', [BrandController::class, 'update'])->name('update');
        Route::delete('/{brand:slug}', [BrandController::class, 'destroy'])->name('destroy');

        // Nested: Brand Products
        Route::get('/{brand:slug}/products', [BrandController::class, 'brandProducts'])->name('products');

        // Nested: Brand Orders
        Route::get('/{brand:slug}/orders', [OrderController::class, 'index'])->name('orders');

        // Nested: Brand Expenses
        Route::get('/{brand:slug}/expenses', [ExpensesController::class, 'index'])->name('expenses');
    });

    // Products
    Route::prefix('products')->name('products.')->group(function (): void {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product:slug}', [ProductController::class, 'show'])->name('show');
        Route::put('/{product:slug}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product:slug}', [ProductController::class, 'destroy'])->name('destroy');

        // Nested: Product Expenses
        Route::get('/{product:slug}/expenses', [ProductController::class, 'productExpenses'])->name('expenses');

        // Nested: Product Orders
        Route::get('/{product:slug}/orders', [OrderController::class, 'index'])->name('orders');
    });

    // ProductItems
    Route::prefix('products-items')->name('products-items.')->group(function (): void {
        Route::get('/', [ProductItemController::class, 'index'])->name('index');
        Route::post('/', [ProductItemController::class, 'store'])->name('store');
        Route::get('/{productItem}', [ProductItemController::class, 'show'])->name('show');
        Route::put('/{productItem}', [ProductItemController::class, 'update'])->name('update');
        Route::delete('/{productItem}', [ProductItemController::class, 'destroy'])->name('destroy');
    });

    // Expense Types
    Route::apiResource('expense-types', ExpenseTypeController::class)->parameters([
        'expense-types' => 'expense_type:slug',
    ]);

    // Expenses
    Route::apiResource('expenses', ExpensesController::class)->parameters([
        'expenses' => 'expense:slug',
    ]);

    // Orders
    Route::apiResource('orders', OrderController::class);

    // Customers
    Route::prefix('customers')->name('customers.')->group(function (): void {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

        // Nested: Customer Orders
        Route::get('/{customer}/orders', [CustomerController::class, 'customerOrders'])->name('orders');

        // Nested: Customer Addresses
        Route::get('/{customer}/addresses', [CustomerController::class, 'customerAddresses'])->name('addresses');
    });

    // Addresses
    Route::apiResource('addresses', AddressController::class);
});
