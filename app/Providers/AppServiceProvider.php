<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Address;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Expenses;
use App\Models\ExpenseType;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\PersonalAccessToken;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\UnknownCustomer;
use App\Models\User;
use App\Services\SecureSellerService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SecureSellerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Relation::enforceMorphMap([
            'address' => Address::class,
            'brand' => Brand::class,
            'customer' => Customer::class,
            'expense' => Expenses::class,
            'expense_type' => ExpenseType::class,
            'order' => Order::class,
            'order_items' => OrderItems::class,
            'product_item' => ProductItem::class,
            'product' => Product::class,
            'unknown_customer' => UnknownCustomer::class,
            'user' => User::class,
        ]);
        //
    }
}
