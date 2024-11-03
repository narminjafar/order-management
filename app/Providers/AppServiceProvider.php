<?php

namespace App\Providers;

use App\Interfaces\Repository\OrderProductRepositoryInterface;
use App\Interfaces\Repository\OrderRepositoryInterface;
use App\Interfaces\Repository\ProductRepositoryInterface;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderProductRepositoryInterface::class, OrderProductRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
