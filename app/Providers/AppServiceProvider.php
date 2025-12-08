<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Brand;
use App\Models\PersonalAccessToken;
// use App\Models\Product;
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
            'brand' => Brand::class,
            //            'product' => Product::class,
            'user' => User::class,
        ]);
        //
    }
}
