<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\AuthService;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\UserService;
use App\Services\WishlistService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService();
        });

        $this->app->bind(BrandService::class, function ($app) {
            return new BrandService();
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->bind(AuthService::class, function ($app) {
            return new AuthService();
        });

        $this->app->bind(WishlistService::class, function ($app) {
            return new WishlistService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
