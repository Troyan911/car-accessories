<?php

namespace App\Providers;

use App\Repositories\Contracts\ImageRepositoryContract;
use App\Repositories\Contracts\ProductsRepositoryContract;
use App\Repositories\ImageRepository;
use App\Repositories\ProductsRepository;
use App\Services\Contract\FileStorageServiceContract;
use App\Services\FileStorageService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //    public array $bindings = [
    //        FileStorageServiceContract::class => FileStorageService::class
    //    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileStorageServiceContract::class, FileStorageService::class);
        $this->app->bind(ProductsRepositoryContract::class, ProductsRepository::class);
        $this->app->bind(ImageRepositoryContract::class, ImageRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
